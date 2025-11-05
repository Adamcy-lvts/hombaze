<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\SavedProperty;
use App\Models\City;
use App\Models\Area;
use App\Models\State;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class LandingPage extends Component
{
    use WithPagination;

    // Search functionality
    public $searchQuery = '';
    public $suggestions = [];
    public $showSuggestions = false;

    // Basic filters for landing page
    public $selectedPropertyType = '';
    public $selectedListingType = '';
    public $selectedPriceRange = '';
    public $selectedBedrooms = '';
    public $selectedFurnishing = '';
    public $selectedLocation = '';

    // New location filters
    public $selectedState = '';
    public $selectedCity = '';
    public $selectedArea = '';

    // UI state
    public $isDarkMode = false;
    public $showFilters = false;
    public $showMobileFilters = false;

    // Saved properties tracking
    public $savedPropertyIds = [];

    public function mount()
    {
        // Load saved property IDs for authenticated users
        $this->loadSavedProperties();
    }

    public function updatedSearchQuery()
    {
        $this->resetPage();

        if (strlen($this->searchQuery) >= 2) {
            $this->updateSuggestions();
        } else {
            $this->hideSuggestions();
        }
    }

    public function updateSuggestions()
    {
        if (strlen($this->searchQuery) < 2) {
            $this->suggestions = [];
            $this->showSuggestions = false;
            return;
        }

        $this->suggestions = $this->generateSuggestions();
        $this->showSuggestions = count($this->suggestions) > 0;
    }

    public function selectSuggestion($suggestion)
    {
        $this->searchQuery = $suggestion['text'];
        $this->hideSuggestions();
        $this->resetPage();
    }

    public function hideSuggestions()
    {
        $this->showSuggestions = false;
    }

    public function updateFilter($type, $value)
    {
        switch ($type) {
            case 'property_type':
                $this->selectedPropertyType = $this->selectedPropertyType === $value ? '' : $value;
                break;
            case 'listing_type':
                $this->selectedListingType = $this->selectedListingType === $value ? '' : $value;
                break;
            case 'price_range':
                $this->selectedPriceRange = $this->selectedPriceRange === $value ? '' : $value;
                break;
            case 'bedrooms':
                $this->selectedBedrooms = $this->selectedBedrooms === $value ? '' : $value;
                break;
            case 'furnishing':
                $this->selectedFurnishing = $this->selectedFurnishing === $value ? '' : $value;
                break;
            case 'location':
                $this->selectedLocation = $this->selectedLocation === $value ? '' : $value;
                break;
            case 'state':
                $oldState = $this->selectedState;
                $this->selectedState = $this->selectedState === $value ? '' : $value;
                // Reset dependent filters when state actually changes
                if ($oldState !== $this->selectedState) {
                    $this->selectedCity = '';
                    $this->selectedArea = '';
                }
                break;
            case 'city':
                $oldCity = $this->selectedCity;
                $this->selectedCity = $this->selectedCity === $value ? '' : $value;
                // Reset area when city actually changes
                if ($oldCity !== $this->selectedCity) {
                    $this->selectedArea = '';
                }
                break;
            case 'area':
                $this->selectedArea = $this->selectedArea === $value ? '' : $value;
                break;
        }
        $this->resetPage();
    }

    public function updatedSelectedState()
    {
        $this->selectedCity = '';
        $this->selectedArea = '';
        $this->resetPage();
        // Force refresh of filter options to get cities for the selected state
        $this->dispatch('$refresh');
    }

    public function updatedSelectedCity()
    {
        $this->selectedArea = '';
        $this->resetPage();
        // Force refresh of filter options to get areas for the selected city
        $this->dispatch('$refresh');
    }

    public function updateCities()
    {
        $this->selectedCity = '';
        $this->selectedArea = '';
        $this->resetPage();
    }

    public function updateAreas()
    {
        $this->selectedArea = '';
        $this->resetPage();
    }

    public function clearAllFilters()
    {
        $this->selectedPropertyType = '';
        $this->selectedListingType = '';
        $this->selectedPriceRange = '';
        $this->selectedBedrooms = '';
        $this->selectedFurnishing = '';
        $this->selectedLocation = '';
        $this->selectedState = '';
        $this->selectedCity = '';
        $this->selectedArea = '';
        $this->searchQuery = '';
        $this->resetPage();
    }

    public function toggleTheme()
    {
        $this->isDarkMode = !$this->isDarkMode;
    }

    public function toggleMobileFilters()
    {
        $this->showMobileFilters = !$this->showMobileFilters;
    }

    public function getPropertiesProperty()
    {
        $query = Property::with(['city', 'state', 'area', 'propertyType', 'agency', 'agent'])
            ->published()
            ->available();

        // Apply search query
        if (!empty($this->searchQuery)) {
            $searchTerm = $this->searchQuery;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('address', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('city', function ($cityQuery) use ($searchTerm) {
                        $cityQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('state', function ($stateQuery) use ($searchTerm) {
                        $stateQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('area', function ($areaQuery) use ($searchTerm) {
                        $areaQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Apply filters
        if ($this->selectedPropertyType) {
            $query->where('property_type_id', $this->selectedPropertyType);
        }

        if ($this->selectedListingType) {
            $query->where('listing_type', $this->selectedListingType);
        }

        if ($this->selectedPriceRange) {
            // Direct numeric range filter for slider input
            $query->where('price', '<=', $this->selectedPriceRange);
        }

        if ($this->selectedBedrooms) {
            if ($this->selectedBedrooms === '5+') {
                $query->where('bedrooms', '>=', 5);
            } else {
                $query->where('bedrooms', $this->selectedBedrooms);
            }
        }

        if ($this->selectedFurnishing) {
            $query->where('furnishing_status', $this->selectedFurnishing);
        }

        if ($this->selectedLocation) {
            $query->whereHas('city', function ($q) {
                $q->where('name', $this->selectedLocation);
            });
        }

        // New location filters
        if ($this->selectedState) {
            $query->where('state_id', $this->selectedState);
        }

        if ($this->selectedCity) {
            $query->where('city_id', $this->selectedCity);
        }

        if ($this->selectedArea) {
            $query->where('area_id', $this->selectedArea);
        }

        // Default sorting: featured first, then newest
        $query->orderBy('is_featured', 'desc')
              ->orderBy('updated_at', 'desc');

        return $query->paginate(12);
    }

    private function applyPriceRange($query, $range)
    {
        switch ($range) {
            case '0-500000':
                $query->where('price', '<=', 500000);
                break;
            case '500000-1000000':
                $query->whereBetween('price', [500000, 1000000]);
                break;
            case '1000000-2000000':
                $query->whereBetween('price', [1000000, 2000000]);
                break;
            case '2000000+':
                $query->where('price', '>=', 2000000);
                break;
        }
    }

    private function generateSuggestions()
    {
        $suggestions = [];
        $searchTerm = strtolower($this->searchQuery);

        // Search locations (cities and areas)
        $cities = City::where('name', 'LIKE', "%{$searchTerm}%")
                     ->with('state')
                     ->limit(3)
                     ->get();

        foreach ($cities as $city) {
            $suggestions[] = [
                'text' => $city->name . ', ' . $city->state->name,
                'type' => 'location',
                'icon' => 'location-dot',
                'category' => 'Cities'
            ];
        }

        $areas = Area::where('name', 'LIKE', "%{$searchTerm}%")
                    ->with(['city', 'state'])
                    ->limit(3)
                    ->get();

        foreach ($areas as $area) {
            $suggestions[] = [
                'text' => $area->name . ', ' . $area->city->name,
                'type' => 'location',
                'icon' => 'location-dot',
                'category' => 'Areas'
            ];
        }

        // Search property types
        $propertyTypes = PropertyType::where('name', 'LIKE', "%{$searchTerm}%")
                                   ->limit(3)
                                   ->get();

        foreach ($propertyTypes as $type) {
            $suggestions[] = [
                'text' => $type->name,
                'type' => 'property_type',
                'icon' => 'home',
                'category' => 'Property Types'
            ];
        }

        return array_slice($suggestions, 0, 8);
    }

    /**
     * Load saved property IDs for the authenticated user
     */
    private function loadSavedProperties()
    {
        if (auth()->check()) {
            $this->savedPropertyIds = SavedProperty::where('user_id', auth()->id())
                ->pluck('property_id')
                ->toArray();
        }
    }

    /**
     * Toggle save status of a property
     */
    public function toggleSaveProperty($propertyId)
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please login to save properties.');
            return $this->redirect(route('login'));
        }

        $userId = auth()->id();
        $savedProperty = SavedProperty::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->first();

        if ($savedProperty) {
            // Remove from saved properties
            $savedProperty->delete();
            $this->savedPropertyIds = array_diff($this->savedPropertyIds, [$propertyId]);

            $this->dispatch('property-unsaved', ['message' => 'Property removed from saved list']);
        } else {
            // Add to saved properties
            SavedProperty::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
            ]);
            $this->savedPropertyIds[] = $propertyId;

            $this->dispatch('property-saved', ['message' => 'Property saved successfully']);
        }
    }

    /**
     * Check if a property is saved by the current user
     */
    public function isPropertySaved($propertyId)
    {
        return in_array($propertyId, $this->savedPropertyIds);
    }

    /**
     * Get quick filter options for the landing page
     */
    public function getFilterOptionsProperty()
    {
        $options = Cache::remember('landing_filter_options_base', 3600, function () {
            return [
                'property_types' => PropertyType::select('id', 'name')->get(),
                'listing_types' => [
                    ['value' => 'rent', 'label' => 'For Rent'],
                    ['value' => 'sale', 'label' => 'For Sale'],
                    ['value' => 'shortlet', 'label' => 'Short Let'],
                ],
                'price_ranges' => [
                    ['value' => '0-500000', 'label' => 'Under ₦500K'],
                    ['value' => '500000-1000000', 'label' => '₦500K - ₦1M'],
                    ['value' => '1000000-2000000', 'label' => '₦1M - ₦2M'],
                    ['value' => '2000000+', 'label' => 'Over ₦2M'],
                ],
                'bedrooms' => [
                    ['value' => '1', 'label' => '1 Bed'],
                    ['value' => '2', 'label' => '2 Beds'],
                    ['value' => '3', 'label' => '3 Beds'],
                    ['value' => '4', 'label' => '4 Beds'],
                    ['value' => '5+', 'label' => '5+ Beds'],
                ],
                'furnishing_types' => [
                    ['value' => 'furnished', 'label' => 'Furnished'],
                    ['value' => 'semi_furnished', 'label' => 'Semi-Furnished'],
                    ['value' => 'unfurnished', 'label' => 'Unfurnished'],
                ],
                'states' => State::select('id', 'name')->orderBy('name')->get(),
                'popular_locations' => City::whereHas('properties', function ($query) {
                    $query->published()->available();
                })->withCount('properties')
                ->orderBy('properties_count', 'desc')
                ->limit(8)
                ->get()
                ->map(function ($city) {
                    return ['value' => $city->name, 'label' => $city->name];
                })->toArray()
            ];
        });

        // Add dynamic cities based on selected state
        if ($this->selectedState) {
            $options['cities'] = City::where('state_id', $this->selectedState)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        // Add dynamic areas based on selected city
        if ($this->selectedCity) {
            $options['areas'] = Area::where('city_id', $this->selectedCity)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return $options;
    }

    /**
     * Get landing page statistics
     */
    public function getStatsProperty()
    {
        return Cache::remember('landing_stats', 3600, function () {
            return [
                'total_properties' => Property::published()->available()->count(),
                'featured_properties' => Property::published()->available()->where('is_featured', true)->count(),
                'cities_covered' => City::whereHas('properties', function ($query) {
                    $query->published()->available();
                })->count(),
            ];
        });
    }

    public function render()
    {
        return view('livewire.landing-page', [
            'properties' => $this->properties,
            'filterOptions' => $this->filterOptions,
            'stats' => $this->stats,
        ])->layout('layouts.guest-app', ['title' => 'Find Your Perfect Home - HomeBaze']);
    }
}