<?php

namespace App\Livewire;

use App\Models\SavedProperty;
use App\Models\SmartSearch;
use App\Search\SearchQuery;
use App\Services\PropertySearchEngine;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PropertySearch extends Component
{
    use WithPagination;

    protected PropertySearchEngine $searchEngine;
    protected \App\Services\PropertyWishlistService $wishlistService;

    // URL-bound search parameters
    #[Url(as: 'q')]
    public string $searchQuery = '';

    #[Url(as: 'type')]
    public ?string $listingType = null;

    #[Url(as: 'property_type')]
    public ?int $propertyTypeId = null;

    #[Url(as: 'bedrooms')]
    public ?string $bedrooms = null;

    #[Url(as: 'min_price')]
    public ?int $minPrice = null;

    #[Url(as: 'max_price')]
    public ?int $maxPrice = null;

    #[Url(as: 'furnishing')]
    public ?string $furnishing = null;

    #[Url(as: 'city')]
    public ?int $cityId = null;

    #[Url(as: 'area')]
    public ?int $areaId = null;

    #[Url(as: 'sort')]
    public string $sortBy = 'relevance';

    #[Url(as: 'saved_search')]
    public ?string $savedSearchId = null;

    // UI state
    public bool $showSuggestions = false;
    public array $suggestions = [];
    public bool $showFilters = false;

    // Search result metadata
    public int $processingTimeMs = 0;
    public int $featuredCount = 0;
    public array $facets = [];

    // Saved properties
    public array $savedPropertyIds = [];

    // Filter options
    public array $filterOptions = [];

    public function boot(PropertySearchEngine $searchEngine, \App\Services\PropertyWishlistService $wishlistService): void
    {
        $this->searchEngine = $searchEngine;
        $this->wishlistService = $wishlistService;
    }

    public function mount(): void
    {
        $this->filterOptions = $this->searchEngine->getFilterOptions();
        $this->loadSavedProperties();

        // Load SmartSearch if provided
        if ($this->savedSearchId) {
            $this->loadSmartSearch($this->savedSearchId);
        }
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();

        if (strlen($this->searchQuery) >= 2) {
            $this->loadSuggestions();
            $this->showSuggestions = true;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function loadSuggestions(): void
    {
        $suggestions = $this->searchEngine->suggest($this->searchQuery);
        $this->suggestions = $suggestions->map(fn ($s) => $s->toArray())->toArray();
    }

    public function selectSuggestion(int $index): void
    {
        if (!isset($this->suggestions[$index])) {
            return;
        }

        $suggestion = $this->suggestions[$index];

        // If it's a property, go directly to it
        if ($suggestion['type'] === 'property' && isset($suggestion['meta']['slug'])) {
            $this->redirect(route('property.show', $suggestion['meta']['slug']));
            return;
        }

        // If it's a location, search for that location
        if ($suggestion['type'] === 'location') {
            $this->searchQuery = $suggestion['text'];
            $this->hideSuggestions();
            $this->resetPage();
            return;
        }

        // Otherwise, search for the text
        $this->searchQuery = $suggestion['text'];
        $this->hideSuggestions();
        $this->resetPage();
    }

    public function hideSuggestions(): void
    {
        $this->showSuggestions = false;
    }

    public function search(): void
    {
        $this->hideSuggestions();
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function setListingType(?string $type): void
    {
        $this->listingType = $this->listingType === $type ? null : $type;
        $this->resetPage();
    }

    public function setPropertyType(?int $typeId): void
    {
        $this->propertyTypeId = $typeId ?: null;
        $this->resetPage();
    }

    public function setBedrooms(?string $bedrooms): void
    {
        $this->bedrooms = $this->bedrooms === $bedrooms ? null : $bedrooms;
        $this->resetPage();
    }

    public function setFurnishing(?string $furnishing): void
    {
        $this->furnishing = $this->furnishing === $furnishing ? null : $furnishing;
        $this->resetPage();
    }

    public function setPriceRange(?int $min, ?int $max): void
    {
        $this->minPrice = $min;
        $this->maxPrice = $max;
        $this->resetPage();
    }

    public function setSortBy(string $sort): void
    {
        $this->sortBy = $sort;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->listingType = null;
        $this->propertyTypeId = null;
        $this->bedrooms = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->furnishing = null;
        $this->cityId = null;
        $this->areaId = null;
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->listingType !== null
            || $this->propertyTypeId !== null
            || $this->bedrooms !== null
            || $this->minPrice !== null
            || $this->maxPrice !== null
            || $this->furnishing !== null
            || $this->cityId !== null
            || $this->areaId !== null;
    }

    public function getPropertiesProperty()
    {
        $bedroomsArray = null;
        if ($this->bedrooms) {
            $bedroomsArray = $this->bedrooms === '5+' ? [5] : [(int) $this->bedrooms];
        }

        $query = new SearchQuery(
            term: $this->searchQuery ?: null,
            listingType: $this->listingType,
            propertyTypeId: $this->propertyTypeId,
            cityId: $this->cityId,
            areaId: $this->areaId,
            minPrice: $this->minPrice,
            maxPrice: $this->maxPrice,
            bedrooms: $bedroomsArray,
            furnishing: $this->furnishing,
            sortBy: $this->sortBy,
            perPage: 12,
            page: $this->getPage(),
        );

        $result = $this->searchEngine->search($query);

        $this->processingTimeMs = $result->processingTimeMs;
        $this->featuredCount = $result->featuredCount;
        $this->facets = $result->facets;

        return $result->toPaginator();
    }

    private function loadSmartSearch(string $searchId): void
    {
        $search = SmartSearch::find($searchId);
        if (!$search) {
            return;
        }

        // Listing Type
        if ($search->search_type) {
            $this->listingType = $search->search_type === 'buy' ? 'sale' : $search->search_type;
        }

        // Property Type
        if ($search->selected_property_type) {
            $this->propertyTypeId = $search->selected_property_type;
        }

        // Budget
        if ($search->budget_min) {
            $this->minPrice = $search->budget_min;
        }
        if ($search->budget_max) {
            $this->maxPrice = $search->budget_max;
        }

        // Bedrooms from additional_filters
        if (isset($search->additional_filters['bedrooms'])) {
            $this->bedrooms = (string) $search->additional_filters['bedrooms'];
        }

        // Location
        if ($search->location_preferences) {
            $loc = $search->location_preferences;
            if (isset($loc['city'])) {
                $this->cityId = $loc['city'];
            }
            if (isset($loc['selected_areas']) && is_array($loc['selected_areas']) && count($loc['selected_areas']) === 1) {
                $this->areaId = $loc['selected_areas'][0];
            }
        }
    }

    private function loadSavedProperties(): void
    {
        if (auth()->check()) {
            $this->savedPropertyIds = $this->wishlistService->getSavedPropertyIds(auth()->user());
        }
    }

    public function toggleSaveProperty(int $propertyId): void
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please login to save properties.');
            $this->redirect(route('login'));
            return;
        }

        $isSaved = $this->wishlistService->toggleSave(auth()->user(), $propertyId);

        if ($isSaved) {
            $this->savedPropertyIds[] = $propertyId;
            $this->dispatch('property-saved', message: 'Property saved successfully');
        } else {
            $this->savedPropertyIds = array_values(array_diff($this->savedPropertyIds, [$propertyId]));
            $this->dispatch('property-unsaved', message: 'Property removed from saved list');
        }
    }

    public function isPropertySaved(int $propertyId): bool
    {
        return $this->wishlistService->isSaved(auth()->user(), $propertyId);
    }

    public function render()
    {
        return view('livewire.property-search', [
            'properties' => $this->properties,
        ])->layout('layouts.guest-app', ['title' => 'Property Search']);
    }
}
