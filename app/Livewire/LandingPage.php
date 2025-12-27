<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\SavedProperty;
use App\Models\State;
use App\Search\SearchQuery;
use App\Services\PropertySearchEngine;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class LandingPage extends Component
{
    use WithPagination;

    protected PropertySearchEngine $searchEngine;

    // Search functionality (handled by SearchBar component, but we need to catch URL params)
    public string $searchQuery = '';

    // Filters
    public ?int $selectedPropertyType = null;
    public ?string $selectedListingType = null;
    public ?int $selectedPriceRange = null;
    public ?string $selectedBedrooms = null;
    public ?string $selectedFurnishing = null;

    // Location filters
    public ?int $selectedState = null;
    public ?int $selectedCity = null;
    public ?int $selectedArea = null;

    // UI state
    public bool $showFilters = false;

    // Saved properties tracking
    public array $savedPropertyIds = [];

    // Filter options
    public array $filterOptions = [];

    public function boot(PropertySearchEngine $searchEngine): void
    {
        $this->searchEngine = $searchEngine;
    }

    public function mount(): void
    {
        $this->filterOptions = $this->searchEngine->getFilterOptions();
        $this->loadSavedProperties();
    }

    public function updatedSelectedState(): void
    {
        $this->selectedCity = null;
        $this->selectedArea = null;
        $this->resetPage();
    }

    public function updatedSelectedCity(): void
    {
        $this->selectedArea = null;
        $this->resetPage();
    }

    public function setListingType(?string $type): void
    {
        $this->selectedListingType = $this->selectedListingType === $type ? null : $type;
        $this->resetPage();
    }

    public function setPropertyType(?int $typeId): void
    {
        $this->selectedPropertyType = $typeId ?: null;
        $this->resetPage();
    }

    public function setBedrooms(?string $bedrooms): void
    {
        $this->selectedBedrooms = $this->selectedBedrooms === $bedrooms ? null : $bedrooms;
        $this->resetPage();
    }

    public function setFurnishing(?string $furnishing): void
    {
        $this->selectedFurnishing = $this->selectedFurnishing === $furnishing ? null : $furnishing;
        $this->resetPage();
    }

    public function clearAllFilters(): void
    {
        $this->selectedPropertyType = null;
        $this->selectedListingType = null;
        $this->selectedPriceRange = null;
        $this->selectedBedrooms = null;
        $this->selectedFurnishing = null;
        $this->selectedState = null;
        $this->selectedCity = null;
        $this->selectedArea = null;
        $this->searchQuery = '';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->selectedPropertyType !== null
            || $this->selectedListingType !== null
            || $this->selectedPriceRange !== null
            || $this->selectedBedrooms !== null
            || $this->selectedFurnishing !== null
            || $this->selectedState !== null
            || $this->selectedCity !== null
            || $this->selectedArea !== null;
    }

    public function getPropertiesProperty()
    {
        $bedroomsArray = null;
        if ($this->selectedBedrooms) {
            $bedroomsArray = $this->selectedBedrooms === '5+' ? [5] : [(int) $this->selectedBedrooms];
        }

        $query = new SearchQuery(
            term: $this->searchQuery ?: null,
            listingType: $this->selectedListingType,
            propertyTypeId: $this->selectedPropertyType,
            stateId: $this->selectedState,
            cityId: $this->selectedCity,
            areaId: $this->selectedArea,
            maxPrice: $this->selectedPriceRange,
            bedrooms: $bedroomsArray,
            furnishing: $this->selectedFurnishing,
            sortBy: 'relevance',
            perPage: 12,
            page: $this->getPage(),
        );

        $result = $this->searchEngine->search($query);

        return $result->toPaginator();
    }

    public function getFeaturedPropertiesProperty()
    {
        return Cache::remember('featured_properties_landing', 300, function () {
            return Property::with(['city', 'state', 'propertyType', 'media'])
                ->published()
                ->available()
                ->featured()
                ->latest()
                ->take(20)
                ->get();
        });
    }

    public function getStatsProperty()
    {
        return Cache::remember('landing_stats', 3600, function () {
            return [
                'total_properties' => Property::published()->available()->count(),
                'featured_properties' => Property::published()->available()->featured()->count(),
                'cities_covered' => City::whereHas('properties', function ($query) {
                    $query->published()->available();
                })->count(),
            ];
        });
    }

    public function getLocationOptionsProperty(): array
    {
        $options = [
            'states' => State::select('id', 'name')->orderBy('name')->get(),
            'cities' => collect(),
            'areas' => collect(),
        ];

        if ($this->selectedState) {
            $options['cities'] = City::where('state_id', $this->selectedState)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        if ($this->selectedCity) {
            $options['areas'] = Area::where('city_id', $this->selectedCity)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return $options;
    }

    private function loadSavedProperties(): void
    {
        if (auth()->check()) {
            $this->savedPropertyIds = SavedProperty::where('user_id', auth()->id())
                ->pluck('property_id')
                ->toArray();
        }
    }

    public function toggleSaveProperty(int $propertyId): void
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please login to save properties.');
            $this->redirect(route('login'));
            return;
        }

        $userId = auth()->id();
        $savedProperty = SavedProperty::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->first();

        if ($savedProperty) {
            $savedProperty->delete();
            $this->savedPropertyIds = array_values(array_diff($this->savedPropertyIds, [$propertyId]));
            $this->dispatch('property-unsaved', message: 'Property removed from saved list');
        } else {
            SavedProperty::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
            ]);
            $this->savedPropertyIds[] = $propertyId;
            $this->dispatch('property-saved', message: 'Property saved successfully');
        }
    }

    public function isPropertySaved(int $propertyId): bool
    {
        return in_array($propertyId, $this->savedPropertyIds);
    }

    public function render()
    {
        return view('livewire.landing-page', [
            'properties' => $this->properties,
            'featuredProperties' => $this->featuredProperties,
            'stats' => $this->stats,
            'locationOptions' => $this->locationOptions,
        ])->layout('layouts.guest-app', ['title' => 'Find Your Perfect Home - HomeBaze']);
    }
}
