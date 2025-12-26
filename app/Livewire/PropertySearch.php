<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\City;
use App\Models\State;
use Livewire\Component;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\SavedProperty;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class PropertySearch extends Component
{
    use WithPagination;

    // Main search query
    #[Url(as: 'q')]
    public $searchQuery = '';
    
    // Active filters as array
    #[Url(as: 'filters')]
    public $activeFilters = [];
    
    // Sort option
    #[Url(as: 'sort')]
    public $sortBy = 'relevance';
    
    // UI state
    public $showSuggestions = false;
    public $suggestions = [];
    public $isLoading = false;
    public $showFilters = false;
    public $isDarkMode = false;
    public $isRateLimited = false;
    public $rateLimitMessage = '';

    // Selected filter states (for UI)
    public $selectedBedrooms = [];
    public $selectedListingTypes = [];
    public $selectedPropertyType = '';
    public $selectedPriceRanges = [];

    // Saved properties tracking
    public $savedPropertyIds = [];
    
    // Available filter options
    public $filterOptions = [
        'listing_type' => ['rent', 'sale', 'shortlet'],
        'property_type' => [],
        'bedrooms' => ['1', '2', '3', '4', '5+'],
        'bathrooms' => ['1', '2', '3', '4+'],
        'price_range' => ['0-500000', '500000-1000000', '1000000-2000000', '2000000+'],
        'furnishing' => ['furnished', 'semi-furnished', 'unfurnished'],
        'features' => ['swimming_pool', 'gym', 'security', 'parking', 'generator', 'air_conditioning']
    ];

    // Saved search ID
    #[Url]
    public $saved_search = '';

    // Custom price range
    public $minPrice = null;
    public $maxPrice = null;

    public function mount()
    {
        // Load property types for filter options
        $this->filterOptions['property_type'] = PropertyType::pluck('name', 'id')->toArray();

        // Load saved property IDs for authenticated users
        $this->loadSavedProperties();

        // Load SmartSearch if provided
        if ($this->saved_search) {
            $this->loadSmartSearch($this->saved_search);
        }

        // Initialize selected states from active filters
        $this->initializeSelectedStates();
    }

    private function loadSmartSearch($searchId)
    {
        $search = \App\Models\SmartSearch::find($searchId);
        if (!$search) return;

        // Reset filters
        $this->activeFilters = [];

        // 1. Listing Type
        if ($search->search_type) {
            $type = $search->search_type === 'buy' ? 'sale' : $search->search_type;
            if (in_array($type, $this->filterOptions['listing_type'])) {
                $this->addFilter('listing_type', $type);
            }
        }

        // 2. Property Type
        if ($search->selected_property_type) {
            $this->addFilter('property_type', $search->selected_property_type);
        }

        // 3. Location
        if ($search->location_preferences) {
            $loc = $search->location_preferences;
            // Handle specific areas
            if (isset($loc['selected_areas']) && is_array($loc['selected_areas'])) {
                 $areas = \App\Models\Area::whereIn('id', $loc['selected_areas'])->pluck('name');
                 foreach($areas as $areaName) {
                     $this->addFilter('location', $areaName);
                 }
            }
            // Handle city/state if no specific areas or as fallback
            elseif (isset($loc['city'])) {
                 $city = \App\Models\City::find($loc['city']);
                 if ($city) $this->addFilter('location', $city->name);
            }
            elseif (isset($loc['state'])) {
                 $state = \App\Models\State::find($loc['state']);
                 if ($state) $this->addFilter('location', $state->name);
            }
        }

        // 4. Budget (Custom Range)
        if ($search->budget_min) $this->minPrice = $search->budget_min;
        if ($search->budget_max) $this->maxPrice = $search->budget_max;

        // 5. Bedrooms
        if (isset($search->additional_filters['bedrooms'])) {
            $beds = $search->additional_filters['bedrooms'];
            // SmartSearch might store "2" or "2+". 
            // If it's a specific number, added it.
            // If it's a range or "2+", we might need to adapt.
            // For now, assuming direct match keys:
            if (in_array($beds, $this->filterOptions['bedrooms'])) {
                $this->addFilter('bedrooms', $beds);
            }
        }
    }
    private function initializeSelectedStates(): void
    {
        $this->selectedBedrooms = [];
        $this->selectedListingTypes = [];
        $this->selectedPropertyType = '';
        $this->selectedPriceRanges = [];

        foreach ($this->activeFilters as $filter) {
            $type = $filter['type'] ?? null;
            $value = $filter['value'] ?? null;
            if (!$type) {
                continue;
            }

            switch ($type) {
                case 'listing_type':
                    $this->selectedListingTypes[] = $value;
                    break;
                case 'property_type':
                    $this->selectedPropertyType = (string) $value;
                    break;
                case 'bedrooms':
                    $this->selectedBedrooms[] = $value;
                    break;
                case 'price_range':
                    $this->selectedPriceRanges[] = $value;
                    break;
            }
        }

        $this->selectedListingTypes = array_values(array_unique($this->selectedListingTypes));
        $this->selectedBedrooms = array_values(array_unique($this->selectedBedrooms));
        $this->selectedPriceRanges = array_values(array_unique($this->selectedPriceRanges));
    }

    // ... existing updated methods ...

    // ... existing updateSuggestions ...

    // ... existing selectSuggestion ...
    
    // ... existing hideSuggestions ...

    // ... existing addFilter ...

    // ... existing removeFilter ...

    // ... existing clearAllFilters ...

    // ... existing toggleFilters ...
    
    // ... existing toggleTheme ...
    
    // ... existing toggleFilter ...
    
    // ... existing isFilterActive ...

    public function getPropertiesProperty()
    {
        if ($this->shouldThrottleSearch()) {
            return Property::query()->whereRaw('1 = 0')->paginate(12);
        }

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
                    ->orWhere('landmark', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('city', function ($cityQuery) use ($searchTerm) {
                        $cityQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('state', function ($stateQuery) use ($searchTerm) {
                        $stateQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('area', function ($areaQuery) use ($searchTerm) {
                        $areaQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('propertyType', function ($typeQuery) use ($searchTerm) {
                        $typeQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Apply active filters
        foreach ($this->activeFilters as $filter) {
            $this->applyFilter($query, $filter['type'], $filter['value']);
        }

        // Apply custom price range (from SmartSearch or advanced filters)
        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Apply sorting
        $this->applySorting($query);

        return $query->paginate(12);
    }

    private function applyFilter($query, $type, $value)
    {
        switch ($type) {
            case 'listing_type':
                $query->where('listing_type', $value);
                break;
            case 'property_type':
                $query->where('property_type_id', $value);
                break;
            case 'bedrooms':
                if ($value === '5+') {
                    $query->where('bedrooms', '>=', 5);
                } else {
                    $query->where('bedrooms', $value);
                }
                break;
            case 'bathrooms':
                if ($value === '4+') {
                    $query->where('bathrooms', '>=', 4);
                } else {
                    $query->where('bathrooms', $value);
                }
                break;
            case 'price_range':
                $this->applyPriceRange($query, $value);
                break;
            case 'furnishing':
                $query->where('furnishing_status', $value);
                break;
            case 'features':
                $query->whereHas('features', function ($q) use ($value) {
                    $q->where('slug', $value);
                });
                break;
            case 'location':
                $query->where(function ($q) use ($value) {
                    $q->whereHas('city', function ($cityQuery) use ($value) {
                        $cityQuery->where('name', $value);
                    })
                    ->orWhereHas('area', function ($areaQuery) use ($value) {
                        $areaQuery->where('name', $value);
                    })
                    ->orWhereHas('state', function ($stateQuery) use ($value) {
                        $stateQuery->where('name', $value);
                    });
                });
                break;
        }
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

    private function applySorting($query)
    {
        switch ($this->sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'relevance':
            default:
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('updated_at', 'desc');
                break;
        }
    }

    private function generateSuggestions()
    {
        $suggestions = [];
        $searchTerm = strtolower($this->searchQuery);

        // Search locations
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

        // Search areas
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

    private function shouldThrottleSearch(): bool
    {
        $rateLimitKey = 'property-search:' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 60)) {
            $this->isRateLimited = true;
            $this->rateLimitMessage = 'Too many searches right now. Please wait a moment and try again.';
            return true;
        }

        RateLimiter::hit($rateLimitKey, 60);
        $this->isRateLimited = false;
        $this->rateLimitMessage = '';
        return false;
    }

    private function getFilterLabel($type, $value)
    {
        switch ($type) {
            case 'listing_type':
                return ucfirst($value);
            case 'property_type':
                $propertyType = PropertyType::find($value);
                return $propertyType ? $propertyType->name : $value;
            case 'bedrooms':
                return $value . ' Bedroom' . ($value > 1 ? 's' : '');
            case 'bathrooms':
                return $value . ' Bathroom' . ($value > 1 ? 's' : '');
            case 'price_range':
                return $this->formatPriceRange($value);
            case 'furnishing':
                return ucfirst(str_replace('_', ' ', $value));
            case 'features':
                return ucfirst(str_replace('_', ' ', $value));
            default:
                return $value;
        }
    }

    private function formatPriceRange($range)
    {
        switch ($range) {
            case '0-500000':
                return 'Under ₦500K';
            case '500000-1000000':
                return '₦500K - ₦1M';
            case '1000000-2000000':
                return '₦1M - ₦2M';
            case '2000000+':
                return 'Over ₦2M';
            default:
                return $range;
        }
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

    public function render()
    {
        return view('livewire.property-search', [
            'properties' => $this->properties,
        ])->layout('layouts.guest-app', ['title' => 'Property Search']);
    }
}
