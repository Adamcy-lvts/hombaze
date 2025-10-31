<?php

namespace App\Livewire\Customer;

use App\Models\SavedSearch;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertySubtype;
use App\Models\PropertyType;
use App\Models\PlotSize;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditSearch extends Component
{
    public SavedSearch $search;

    // Basic search info
    public $name = '';
    public $description = '';
    public $search_type = 'rent'; // rent, buy, shortlet

    // Location preferences
    public $state_id = null;
    public $city_id = null;
    public $area_id = null;
    public $selected_areas = [];
    public $area_selection_type = 'any';

    // Interest preference (single selection)
    public $interested_in = 'renting';

    // Property type (single selection)
    public $selected_property_type = null;

    // Property categories (legacy support)
    public $property_categories = [];

    // Property subtypes (new approach)
    public $selected_subtypes = [];

    // Budget preferences
    public $budgets = [
        'house_buy' => ['min' => '', 'max' => ''],
        'house_rent' => ['min' => '', 'max' => ''],
        'land_buy' => ['min' => '', 'max' => ''],
        'shop_buy' => ['min' => '', 'max' => ''],
        'shop_rent' => ['min' => '', 'max' => ''],
    ];

    // Land size preferences
    public $land_sizes = [
        'land_buy' => [
            'predefined_size_id' => '',        // Selected predefined plot size
            'use_custom_size' => false,        // Toggle for custom size input
            'custom_size_value' => '',         // Custom size value
            'custom_size_unit' => 'sqm',       // Custom size unit
            'plot_min' => '', 'plot_max' => '', // Legacy support - Number of plots
            'sqm_min' => '', 'sqm_max' => '',   // Legacy support - Square meters
        ],
    ];

    // Notification preferences
    public $notification_settings = [
        'email_alerts' => true,
        'sms_alerts' => false,
        'whatsapp_alerts' => false,
    ];

    public $is_active = true;
    public $is_default = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'interested_in' => 'required|string|in:buying,renting,shortlet',
        'selected_property_type' => 'nullable|integer',
        'property_categories' => 'nullable|array',
        'selected_subtypes' => 'nullable|array',
        'selected_areas' => 'nullable|array',
        'selected_areas.*' => 'integer|exists:areas,id',
        'area_selection_type' => 'nullable|string|in:any,all,specific',
    ];

    protected $messages = [
        'interested_in.required' => 'Please select an interest.',
        'interested_in.in' => 'Please select a valid interest.',
        'selected_areas.*.exists' => 'Please select valid areas.',
        'selected_areas.*.integer' => 'Area selection is invalid.',
    ];

    public function mount(SavedSearch $search)
    {
        // Ensure the search belongs to the authenticated user
        if ($search->user_id !== Auth::id()) {
            abort(404);
        }

        $this->search = $search;

        // Load existing data
        $this->name = $search->name;
        $this->description = $search->description;
        $this->search_type = $search->search_type ?? 'property';

        // Load location preferences
        $locations = $search->location_preferences ?? [];
        $this->state_id = $locations['state'] ?? null;
        $this->city_id = $locations['city'] ?? null;
        $this->area_id = $locations['area'] ?? null;

        // Handle area selection for new system
        if (isset($locations['area_selection_type'])) {
            $this->area_selection_type = $locations['area_selection_type'];
            $this->selected_areas = $locations['selected_areas'] ?? [];
        } elseif (isset($locations['area'])) {
            // Legacy: single area selection
            $this->area_selection_type = 'specific';
            $this->selected_areas = [$locations['area']];
        } else {
            $this->area_selection_type = 'any';
            $this->selected_areas = [];
        }

        // Load property type and categories
        $this->selected_property_type = $search->selected_property_type ?? null;
        $this->property_categories = $search->property_categories ?? [];

        // Load selected subtypes (new approach - array of PropertySubtype IDs)
        $this->selected_subtypes = $search->property_subtypes ?? [];

        // IMPORTANT: Infer property types and interests FIRST before loading budgets
        // Infer interests from property categories
        $this->inferInterestsFromCategories();

        // Infer property type from categories if not set
        $this->inferPropertyTypeFromCategories();

        // NOW load budgets after we have the correct property type and interest info
        $additionalFilters = $search->additional_filters ?? [];
        if (isset($additionalFilters['budgets'])) {
            foreach ($additionalFilters['budgets'] as $category => $budget) {
                $this->budgets[$category] = [
                    'min' => $budget['min'] ? number_format($budget['min'], 0, '', '') : '',
                    'max' => $budget['max'] ? number_format($budget['max'], 0, '', '') : '',
                ];
            }
        }

        // Also load from legacy budget fields if available
        if ($search->budget_min || $search->budget_max) {
            // Map to appropriate category based on property type and search type
            $budgetKey = $this->getBudgetKeyForCurrentSearch();
            if ($budgetKey) {
                $this->budgets[$budgetKey] = [
                    'min' => $search->budget_min ? number_format($search->budget_min, 0, '', '') : '',
                    'max' => $search->budget_max ? number_format($search->budget_max, 0, '', '') : '',
                ];
            }
        }

        // Load land sizes
        if (isset($additionalFilters['land_sizes'])) {
            $this->land_sizes = array_merge($this->land_sizes, $additionalFilters['land_sizes']);
        }


        // Load notification settings
        $this->notification_settings = $search->notification_settings ?? [
            'email_alerts' => true,
            'sms_alerts' => false,
            'whatsapp_alerts' => false,
        ];

        $this->is_active = $search->is_active;
        $this->is_default = $search->is_default;
    }

    protected function inferInterestsFromCategories()
    {
        // First try to infer from search_type (most reliable)
        if ($this->search_type) {
            $typeMap = [
                'buy' => 'buying',
                'rent' => 'renting',
                'shortlet' => 'shortlet'
            ];
            if (isset($typeMap[$this->search_type])) {
                $this->interested_in = $typeMap[$this->search_type];
                return;
            }
        }

        // Fallback to property categories
        $interest = 'renting'; // default
        foreach ($this->property_categories ?? [] as $category) {
            if (str_contains($category, '_buy')) {
                $interest = 'buying';
                break; // Take the first buying category found
            }
            if (str_contains($category, '_rent')) {
                $interest = 'renting';
                // Don't break here, as buying takes precedence
            }
        }

        $this->interested_in = $interest;
    }

    protected function inferPropertyTypeFromCategories()
    {
        if ($this->selected_property_type) {
            return; // Already set
        }

        // Map property categories to property types
        $categoryToTypeMap = [
            'house_buy' => 2,  // House
            'house_rent' => 1, // Apartment (prefer apartment for rent)
            'land_buy' => 3,   // Land
            'shop_buy' => 4,   // Commercial
            'shop_rent' => 4,  // Commercial
        ];

        foreach ($this->property_categories as $category) {
            if (isset($categoryToTypeMap[$category])) {
                $this->selected_property_type = $categoryToTypeMap[$category];
                break; // Take the first match
            }
        }
    }

    protected function getBudgetKeyForCurrentSearch(): ?string
    {
        // First try with selected property type and interested_in
        if ($this->selected_property_type && $this->interested_in) {
            $searchType = $this->interested_in === 'buying' ? 'buy' :
                         ($this->interested_in === 'renting' ? 'rent' : 'shortlet');
            return $this->getBudgetKeyFromPropertyTypeAndSearchType($this->selected_property_type, $searchType);
        }

        // Second try with selected property type and search_type
        if ($this->selected_property_type && $this->search_type) {
            return $this->getBudgetKeyFromPropertyTypeAndSearchType($this->selected_property_type, $this->search_type);
        }

        // Fallback to property categories
        if (!empty($this->property_categories)) {
            // Use the first category as the budget key
            return $this->property_categories[0];
        }

        // Default fallback based on interested_in or search_type
        $intention = $this->interested_in ?? ($this->search_type === 'buy' ? 'buying' : 'renting');
        return $intention === 'buying' ? 'house_buy' : 'house_rent';
    }

    protected function getBudgetKeyFromPropertyTypeAndSearchType(int $propertyTypeId, string $searchType): ?string
    {
        switch ($propertyTypeId) {
            case 1: // Apartment
            case 2: // House
                return $searchType === 'buy' ? 'house_buy' : 'house_rent';
            case 3: // Land
                return 'land_buy';
            case 4: // Commercial
            case 5: // Office
            case 6: // Warehouse
                return $searchType === 'buy' ? 'shop_buy' : 'shop_rent';
            default:
                return null;
        }
    }

    public function updatedStateId($value)
    {
        $this->city_id = null;
        $this->area_id = null;
        $this->selected_areas = [];
    }

    public function updatedCityId($value)
    {
        $this->area_id = null;
        $this->selected_areas = [];
    }

    public function updatedSelectedPropertyType($value)
    {
        // Clear selected subtypes when property type changes
        $this->selected_subtypes = [];
    }

    public function updatedAreaSelectionType($value)
    {
        // Clear selected areas when selection type changes
        if ($value === 'any' || $value === 'all') {
            $this->selected_areas = [];
        }
    }

    public function updatedInterestedIn($value)
    {
        // Clear property type and subtypes when interest changes
        $this->selected_property_type = null;
        $this->selected_subtypes = [];

        // Clear property categories that are no longer valid based on new interests
        $availableCategories = collect($this->getAvailablePropertyCategories())->pluck('value')->toArray();
        $this->property_categories = array_intersect($this->property_categories, $availableCategories);

        // Update search_type based on interest
        $searchTypeMap = [
            'renting' => 'rent',
            'buying' => 'buy',
            'shortlet' => 'shortlet'
        ];
        $this->search_type = $searchTypeMap[$value] ?? 'rent';
    }

    public function updatedPropertyCategories($value)
    {
        // Clear selected subtypes when property categories change
        $this->selected_subtypes = [];
    }

    public function getAvailablePropertyCategories()
    {
        $interest = $this->interested_in;
        $categories = [];

        if (empty($interest)) {
            return [];
        }

        // House categories
        if ($interest === 'buying') {
            $categories[] = [
                'value' => 'house_buy',
                'label' => 'Houses & Apartments (Buy)',
                'description' => 'Residential properties for purchase',
            ];
        } elseif ($interest === 'renting' || $interest === 'shortlet') {
            $categories[] = [
                'value' => 'house_rent',
                'label' => 'Houses & Apartments (Rent)',
                'description' => 'Residential properties for rent',
            ];
        }

        // Land categories (only for buying)
        if ($interest === 'buying') {
            $categories[] = [
                'value' => 'land_buy',
                'label' => 'Land & Plots',
                'description' => 'Land and plots for purchase',
            ];
        }

        // Shop categories
        if ($interest === 'buying') {
            $categories[] = [
                'value' => 'shop_buy',
                'label' => 'Commercial Shops (Buy)',
                'description' => 'Commercial spaces for purchase',
            ];
        } elseif ($interest === 'renting') {
            $categories[] = [
                'value' => 'shop_rent',
                'label' => 'Commercial Shops (Rent)',
                'description' => 'Commercial spaces for rent',
            ];
        }

        return $categories;
    }

    public function getPropertyTypeMapping()
    {
        return [
            'house_buy' => 2,  // House
            'house_rent' => [1, 2], // Apartment and House
            'land_buy' => 3,   // Land
            'shop_buy' => [4, 5], // Commercial and Office Space
            'shop_rent' => [4, 5], // Commercial and Office Space
        ];
    }

    public function getAvailablePropertyTypes()
    {
        return PropertyType::active()
            ->ordered()
            ->get()
            ->mapWithKeys(function ($propertyType) {
                return [$propertyType->id => $propertyType->name];
            })
            ->toArray();
    }

    public function getAvailableSubtypes()
    {
        $propertyTypeIds = $this->selected_property_type ? [$this->selected_property_type] : [];

        if (empty($propertyTypeIds)) {
            return [];
        }

        return PropertySubtype::whereIn('property_type_id', $propertyTypeIds)
            ->active()
            ->ordered()
            ->with('propertyType')
            ->get()
            ->groupBy(function($subtype) {
                return $subtype->propertyType->name;
            });
    }

    public function getAvailablePlotSizes()
    {
        return PlotSize::active()
            ->ordered()
            ->get()
            ->mapWithKeys(function ($plotSize) {
                return [$plotSize->id => $plotSize->name . ' (' . $plotSize->formatted_display . ')'];
            })
            ->toArray();
    }

    public function getPlotSizeUnits()
    {
        return PlotSize::getUnits();
    }

    public function getAvailableAreas()
    {
        if (!$this->city_id) {
            return collect();
        }

        return Area::where('city_id', $this->city_id)
            ->active()
            ->ordered()
            ->get()
            ->mapWithKeys(function ($area) {
                return [$area->id => $area->name];
            })
            ->toArray();
    }

    public function updateSearch()
    {
        $this->validate();

        // Prepare location preferences
        $locationPreferences = [];
        if ($this->state_id) $locationPreferences['state'] = $this->state_id;
        if ($this->city_id) $locationPreferences['city'] = $this->city_id;
        if ($this->area_id) $locationPreferences['area'] = $this->area_id;
        if (!empty($this->selected_areas)) $locationPreferences['selected_areas'] = $this->selected_areas;
        if ($this->area_selection_type) $locationPreferences['area_selection_type'] = $this->area_selection_type;

        // Calculate budget ranges for each category
        $cleanedBudgets = [];
        foreach ($this->budgets as $category => $budget) {
            if (!empty($budget['min']) || !empty($budget['max'])) {
                $cleanedBudgets[$category] = [
                    'min' => !empty($budget['min']) ? (float) str_replace(',', '', $budget['min']) : null,
                    'max' => !empty($budget['max']) ? (float) str_replace(',', '', $budget['max']) : null,
                ];
            }
        }

        $this->search->update([
            'name' => $this->name,
            'description' => $this->description,
            'search_type' => $this->search_type,
            'selected_property_type' => $this->selected_property_type,
            'property_categories' => !empty($this->property_categories) ? $this->property_categories : null,
            'location_preferences' => !empty($locationPreferences) ? $locationPreferences : null,
            'property_subtypes' => !empty($this->selected_subtypes) ? $this->selected_subtypes : null,
            'additional_filters' => [
                'budgets' => $cleanedBudgets,
                'land_sizes' => $this->land_sizes,
            ],
            'notification_settings' => $this->notification_settings,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
        ]);

        // If this is set as default, remove default from other searches
        if ($this->is_default) {
            Auth::user()->savedSearches()
                ->where('id', '!=', $this->search->id)
                ->update(['is_default' => false]);
        }

        session()->flash('success', 'Search updated successfully!');

        return redirect()->route('customer.searches.index');
    }

    public function render()
    {
        $states = State::orderBy('name')->get();
        $cities = $this->state_id ? City::where('state_id', $this->state_id)->orderBy('name')->get() : collect();
        $areas = $this->city_id ? Area::where('city_id', $this->city_id)->orderBy('name')->get() : collect();

        return view('livewire.customer.edit-search', [
            'states' => $states,
            'cities' => $cities,
            'areas' => $areas,
            'availablePropertyTypes' => $this->getAvailablePropertyTypes(),
            'availablePropertyCategories' => $this->getAvailablePropertyCategories(),
            'availableSubtypes' => $this->getAvailableSubtypes(),
            'availablePlotSizes' => $this->getAvailablePlotSizes(),
            'plotSizeUnits' => $this->getPlotSizeUnits(),
            'availableAreas' => $this->getAvailableAreas(),
        ])->layout('layouts.app');
    }
}