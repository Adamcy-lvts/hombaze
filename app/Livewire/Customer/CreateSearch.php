<?php

namespace App\Livewire\Customer;

use App\Models\SmartSearch;
use App\Models\SmartSearchSubscription;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertySubtype;
use App\Models\PropertyType;
use App\Models\PlotSize;
use App\Services\SmartSearchMatcher;
use App\Jobs\ProcessSmartSearchMatches;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateSearch extends Component
{
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

    // Job status tracking
    public $jobStatus = null; // 'searching', 'completed', 'failed'
    public $jobResults = null;

    // Active purchase info
    public ?SmartSearchSubscription $activePurchase = null;
    public int $remainingSearches = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'interested_in' => 'required|string|in:buying,renting,shortlet',
        'selected_property_type' => 'nullable|integer',
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

    public function mount()
    {
        // Check if user has an active purchase with remaining searches
        $this->checkActivePurchase();

        // If no active purchase, redirect to pricing
        if (!$this->activePurchase) {
            session()->flash('error', 'You need to purchase a SmartSearch plan to create searches.');
            return redirect()->route('smartsearch.pricing');
        }

        // Set some default values
        $this->interested_in = 'renting';
        $this->search_type = 'rent';
        $this->selected_property_type = null;
        $this->area_selection_type = 'any';
        $this->selected_areas = [];

        // Set notification settings based on tier channels
        $tierChannels = $this->activePurchase
            ? SmartSearch::TIER_CONFIGS[$this->activePurchase->tier]['channels'] ?? ['email']
            : ['email'];

        $this->notification_settings = [
            'email_alerts' => in_array('email', $tierChannels),
            'sms_alerts' => in_array('sms', $tierChannels),
            'whatsapp_alerts' => in_array('whatsapp', $tierChannels),
        ];
    }

    /**
     * Check if user has an active purchase with remaining searches
     */
    private function checkActivePurchase(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Find an active purchase with remaining searches
        $this->activePurchase = SmartSearchSubscription::where('user_id', $user->id)
            ->active()
            ->get()
            ->first(fn($purchase) => $purchase->canCreateSearch());

        if ($this->activePurchase) {
            $this->remainingSearches = $this->activePurchase->getRemainingSearches();
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

    public function updatedInterestedIn($value)
    {
        // Clear property type and subtypes when interest changes
        $this->selected_property_type = null;
        $this->selected_subtypes = [];

        // Update search_type based on interest
        $searchTypeMap = [
            'renting' => 'rent',
            'buying' => 'buy',
            'shortlet' => 'shortlet'
        ];
        $this->search_type = $searchTypeMap[$value] ?? 'rent';
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
            });
    }

    public function createSearch()
    {
        $this->validate();

        // Re-check purchase validity before creating
        $this->checkActivePurchase();
        if (!$this->activePurchase || !$this->activePurchase->canCreateSearch()) {
            session()->flash('error', 'You have no remaining searches. Please purchase a new plan.');
            return redirect()->route('smartsearch.pricing');
        }

        // Prepare location preferences
        $locationPreferences = [];
        if ($this->state_id) $locationPreferences['state'] = $this->state_id;
        if ($this->city_id) $locationPreferences['city'] = $this->city_id;

        // Handle area selection
        if ($this->area_selection_type === 'any') {
            $locationPreferences['area_selection_type'] = 'any';
        } elseif ($this->area_selection_type === 'all') {
            $locationPreferences['area_selection_type'] = 'all';
        } elseif ($this->area_selection_type === 'specific' && !empty($this->selected_areas)) {
            $locationPreferences['area_selection_type'] = 'specific';
            $locationPreferences['selected_areas'] = $this->selected_areas;
        }

        // Keep backward compatibility
        if ($this->area_id) $locationPreferences['area'] = $this->area_id;

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

        // Calculate land size ranges for each category
        $cleanedLandSizes = [];
        foreach ($this->land_sizes as $category => $landSize) {
            if (!empty($landSize['plot_min']) || !empty($landSize['plot_max']) ||
                !empty($landSize['sqm_min']) || !empty($landSize['sqm_max'])) {
                $cleanedLandSizes[$category] = [
                    'plot_min' => !empty($landSize['plot_min']) ? (float) $landSize['plot_min'] : null,
                    'plot_max' => !empty($landSize['plot_max']) ? (float) $landSize['plot_max'] : null,
                    'sqm_min' => !empty($landSize['sqm_min']) ? (float) $landSize['sqm_min'] : null,
                    'sqm_max' => !empty($landSize['sqm_max']) ? (float) $landSize['sqm_max'] : null,
                ];
            }
        }

        // Get tier and duration from the purchase
        $tier = $this->activePurchase->tier;
        $tierConfig = SmartSearch::TIER_CONFIGS[$tier] ?? SmartSearch::TIER_CONFIGS[SmartSearch::TIER_STARTER];

        $search = Auth::user()->smartSearches()->create([
            'name' => $this->name,
            'description' => $this->description,
            'search_type' => $this->search_type,
            'search_criteria' => ['created_via' => 'new_interface'], // Legacy required field
            'selected_property_type' => $this->selected_property_type,
            'property_categories' => null, // Legacy field, keeping null for new searches
            'location_preferences' => !empty($locationPreferences) ? $locationPreferences : null,
            'property_subtypes' => !empty($this->selected_subtypes) ? $this->selected_subtypes : null,
            'budget_min' => null, // We'll use the new budgets structure
            'budget_max' => null,
            'additional_filters' => [
                'budgets' => $cleanedBudgets,
                'land_sizes' => $cleanedLandSizes,
                'purchase_id' => $this->activePurchase->id,
            ],
            'notification_settings' => $this->notification_settings,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'tier' => $tier,
            'expires_at' => now()->addDays($tierConfig['duration_days']),
            'purchased_at' => $this->activePurchase->paid_at,
            'purchase_reference' => $this->activePurchase->payment_reference,
            'matches_sent' => 0,
        ]);

        // Increment the purchase's used search count
        $this->activePurchase->incrementSearchCount();

        // If this is set as default, remove default from other searches
        if ($this->is_default) {
            Auth::user()->smartSearches()
                ->where('id', '!=', $search->id)
                ->update(['is_default' => false]);
        }

        // Set status to show we're searching
        $this->jobStatus = 'searching';

        // Store search ID and timestamp for job tracking
        session([
            'created_search_id' => $search->id,
            'search_created_at' => now()
        ]);

        // Dispatch the search job for real-time progress tracking
        ProcessSmartSearchMatches::dispatch(null, $search->id, false);

        $tierName = $tierConfig['name'];
        session()->flash('success',
            "Your {$tierName} SmartSearch '{$this->name}' is now active! We're scanning all available properties for matches. " .
            "You'll receive notifications when we find matches."
        );

        return redirect()->route('customer.searches.index');
    }

    public function render()
    {
        $states = State::orderBy('name')->get();
        $cities = $this->state_id ? City::where('state_id', $this->state_id)->orderBy('name')->get() : collect();
        $areas = $this->city_id ? Area::where('city_id', $this->city_id)->orderBy('name')->get() : collect();

        // Get tier info for display
        $tierInfo = null;
        if ($this->activePurchase) {
            $tierConfig = SmartSearch::TIER_CONFIGS[$this->activePurchase->tier] ?? null;
            $tierInfo = [
                'name' => $tierConfig['name'] ?? 'Unknown',
                'remaining' => $this->remainingSearches,
                'total' => $this->activePurchase->searches_limit,
                'is_unlimited' => $this->activePurchase->hasUnlimitedSearches(),
                'days_left' => $this->activePurchase->getDaysRemaining(),
                'channels' => $tierConfig['channels'] ?? ['email'],
            ];
        }

        return view('livewire.customer.create-search', [
            'states' => $states,
            'cities' => $cities,
            'areas' => $areas,
            'availablePropertyTypes' => $this->getAvailablePropertyTypes(),
            'availableSubtypes' => $this->getAvailableSubtypes(),
            'availablePlotSizes' => $this->getAvailablePlotSizes(),
            'plotSizeUnits' => $this->getPlotSizeUnits(),
            'availableAreas' => $this->getAvailableAreas(),
            'tierInfo' => $tierInfo,
        ])->layout('layouts.app');
    }
}