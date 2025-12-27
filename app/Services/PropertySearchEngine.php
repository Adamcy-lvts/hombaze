<?php

namespace App\Services;

use App\Models\Area;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RecentSearch;
use App\Search\SearchQuery;
use App\Search\SearchResult;
use App\Search\Suggestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Meilisearch\Client as MeilisearchClient;

class PropertySearchEngine
{
    protected MeilisearchClient $client;

    public function __construct()
    {
        $this->client = app(MeilisearchClient::class);
    }

    /**
     * Execute a property search with Meilisearch.
     */
    public function search(SearchQuery $query): SearchResult
    {
        $searchOptions = [
            'filter' => $this->buildFilters($query),
            'sort' => $this->buildSort($query),
            'limit' => $query->perPage,
            'offset' => ($query->page - 1) * $query->perPage,
            'facets' => ['listing_type', 'property_type_id', 'bedrooms', 'city_id', 'furnishing_status'],
        ];

        // Remove empty filter array
        if (empty($searchOptions['filter'])) {
            unset($searchOptions['filter']);
        }

        try {
            $results = Property::search($query->term ?? '', function ($meilisearch, $term, $options) use ($searchOptions) {
                return $meilisearch->search($term, $searchOptions);
            })->raw();

            $properties = $this->hydrateResults($results['hits'] ?? []);
            $featuredCount = $properties->filter(fn ($p) => $p->isFeaturedActive())->count();

            // Record search for logged-in users
            $this->recordSearch($query, $results['estimatedTotalHits'] ?? 0);

            return new SearchResult(
                properties: $properties,
                total: $results['estimatedTotalHits'] ?? 0,
                facets: $results['facetDistribution'] ?? [],
                processingTimeMs: $results['processingTimeMs'] ?? 0,
                featuredCount: $featuredCount,
                page: $query->page,
                perPage: $query->perPage,
            );
        } catch (\Exception $e) {
            // Fallback to database search if Meilisearch fails
            return $this->fallbackSearch($query);
        }
    }

    /**
     * Get autocomplete suggestions.
     */
    public function suggest(string $term, int $limit = 8): Collection
    {
        if (strlen($term) < 2) {
            return collect();
        }

        $suggestions = collect();

        // 1. Recent searches (if user logged in)
        if (auth()->check()) {
            $suggestions = $suggestions->merge($this->getRecentSearchSuggestions($term, 3));
        }

        // 2. Property title matches (using Meilisearch)
        $suggestions = $suggestions->merge($this->getPropertyTitleSuggestions($term, 3));

        // 3. Location suggestions (cities + areas)
        $suggestions = $suggestions->merge($this->getLocationSuggestions($term, 3));

        // 4. Property type suggestions
        $suggestions = $suggestions->merge($this->getPropertyTypeSuggestions($term, 2));

        return $suggestions->take($limit)->values();
    }

    /**
     * Get filter options (cached).
     */
    public function getFilterOptions(): array
    {
        return Cache::remember('search_filter_options', now()->addHour(), function () {
            return [
                'listing_types' => [
                    ['value' => 'rent', 'label' => 'For Rent'],
                    ['value' => 'sale', 'label' => 'For Sale'],
                    ['value' => 'shortlet', 'label' => 'Short Let'],
                ],
                'property_types' => PropertyType::orderBy('name')
                    ->get()
                    ->map(fn ($type) => [
                        'value' => $type->id,
                        'label' => $type->name,
                    ])
                    ->toArray(),
                'bedrooms' => [
                    ['value' => 1, 'label' => '1'],
                    ['value' => 2, 'label' => '2'],
                    ['value' => 3, 'label' => '3'],
                    ['value' => 4, 'label' => '4'],
                    ['value' => 5, 'label' => '5+'],
                ],
                'furnishing' => [
                    ['value' => 'furnished', 'label' => 'Furnished'],
                    ['value' => 'semi_furnished', 'label' => 'Semi-Furnished'],
                    ['value' => 'unfurnished', 'label' => 'Unfurnished'],
                ],
                'price_ranges' => [
                    ['min' => 0, 'max' => 500000, 'label' => 'Under ₦500K'],
                    ['min' => 500000, 'max' => 1000000, 'label' => '₦500K - ₦1M'],
                    ['min' => 1000000, 'max' => 2000000, 'label' => '₦1M - ₦2M'],
                    ['min' => 2000000, 'max' => 5000000, 'label' => '₦2M - ₦5M'],
                    ['min' => 5000000, 'max' => null, 'label' => 'Above ₦5M'],
                ],
            ];
        });
    }

    /**
     * Build Meilisearch filter string.
     */
    protected function buildFilters(SearchQuery $query): array
    {
        $filters = [];

        if ($query->listingType) {
            $filters[] = "listing_type = '{$query->listingType}'";
        }

        if ($query->propertyTypeId) {
            $filters[] = "property_type_id = {$query->propertyTypeId}";
        }

        if ($query->stateId) {
            $filters[] = "state_id = {$query->stateId}";
        }

        if ($query->cityId) {
            $filters[] = "city_id = {$query->cityId}";
        }

        if ($query->areaId) {
            $filters[] = "area_id = {$query->areaId}";
        }

        if ($query->minPrice) {
            $filters[] = "price >= {$query->minPrice}";
        }

        if ($query->maxPrice) {
            $filters[] = "price <= {$query->maxPrice}";
        }

        if ($query->bedrooms && is_array($query->bedrooms)) {
            $bedroomFilters = [];
            foreach ($query->bedrooms as $bedroom) {
                if ($bedroom === 5 || $bedroom === '5+') {
                    $bedroomFilters[] = "bedrooms >= 5";
                } else {
                    $bedroomFilters[] = "bedrooms = " . (int) $bedroom;
                }
            }
            if (!empty($bedroomFilters)) {
                $filters[] = '(' . implode(' OR ', $bedroomFilters) . ')';
            }
        }

        if ($query->furnishing) {
            $filters[] = "furnishing_status = '{$query->furnishing}'";
        }

        if ($query->featuredOnly) {
            $filters[] = "is_featured_active = true";
        }

        if ($query->verifiedOnly) {
            $filters[] = "is_verified = true";
        }

        return $filters;
    }

    /**
     * Build sort array for Meilisearch.
     * Featured properties ALWAYS appear first.
     */
    protected function buildSort(SearchQuery $query): array
    {
        // ALWAYS put featured first
        $sort = ['is_featured_active:desc'];

        // Then apply user's sort preference
        $sort[] = match ($query->sortBy) {
            'price_low' => 'price:asc',
            'price_high' => 'price:desc',
            'newest' => 'created_at:desc',
            'oldest' => 'created_at:asc',
            'popular' => 'view_count:desc',
            default => 'created_at:desc',
        };

        return $sort;
    }

    /**
     * Hydrate Meilisearch results into Property models.
     */
    protected function hydrateResults(array $hits): Collection
    {
        if (empty($hits)) {
            return collect();
        }

        $ids = collect($hits)->pluck('id')->toArray();

        // Preserve order from Meilisearch
        $properties = Property::with([
            'propertyType',
            'propertySubtype',
            'city',
            'state',
            'area',
            'media',
        ])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $properties->get($id))
            ->filter();
    }

    /**
     * Fallback to database search if Meilisearch is unavailable.
     */
    protected function fallbackSearch(SearchQuery $query): SearchResult
    {
        $builder = Property::query()
            ->published()
            ->available()
            ->with(['propertyType', 'propertySubtype', 'city', 'state', 'area', 'media']);

        // Apply text search
        if ($query->term) {
            $term = '%' . $query->term . '%';
            $builder->where(function ($q) use ($term) {
                $q->where('title', 'LIKE', $term)
                    ->orWhere('description', 'LIKE', $term)
                    ->orWhere('address', 'LIKE', $term)
                    ->orWhereHas('city', fn ($q) => $q->where('name', 'LIKE', $term))
                    ->orWhereHas('area', fn ($q) => $q->where('name', 'LIKE', $term));
            });
        }

        // Apply filters
        if ($query->listingType) {
            $builder->where('listing_type', $query->listingType);
        }

        if ($query->propertyTypeId) {
            $builder->where('property_type_id', $query->propertyTypeId);
        }

        if ($query->stateId) {
            $builder->where('state_id', $query->stateId);
        }

        if ($query->cityId) {
            $builder->where('city_id', $query->cityId);
        }

        if ($query->areaId) {
            $builder->where('area_id', $query->areaId);
        }

        if ($query->minPrice) {
            $builder->where('price', '>=', $query->minPrice);
        }

        if ($query->maxPrice) {
            $builder->where('price', '<=', $query->maxPrice);
        }

        if ($query->bedrooms) {
            $builder->where(function ($q) use ($query) {
                foreach ($query->bedrooms as $bedroom) {
                    if ($bedroom === 5 || $bedroom === '5+') {
                        $q->orWhere('bedrooms', '>=', 5);
                    } else {
                        $q->orWhere('bedrooms', (int) $bedroom);
                    }
                }
            });
        }

        if ($query->furnishing) {
            $builder->where('furnishing_status', $query->furnishing);
        }

        // Featured first, then sort
        $builder->orderByRaw('CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until > NOW()) THEN 0 ELSE 1 END');

        match ($query->sortBy) {
            'price_low' => $builder->orderBy('price', 'asc'),
            'price_high' => $builder->orderBy('price', 'desc'),
            'newest' => $builder->orderBy('created_at', 'desc'),
            'oldest' => $builder->orderBy('created_at', 'asc'),
            'popular' => $builder->orderBy('view_count', 'desc'),
            default => $builder->orderBy('created_at', 'desc'),
        };

        $total = $builder->count();
        $properties = $builder
            ->offset(($query->page - 1) * $query->perPage)
            ->limit($query->perPage)
            ->get();

        $featuredCount = $properties->filter(fn ($p) => $p->isFeaturedActive())->count();

        return new SearchResult(
            properties: $properties,
            total: $total,
            facets: [],
            processingTimeMs: 0,
            featuredCount: $featuredCount,
            page: $query->page,
            perPage: $query->perPage,
        );
    }

    /**
     * Get recent search suggestions for the current user.
     */
    protected function getRecentSearchSuggestions(string $term, int $limit): Collection
    {
        if (!class_exists(RecentSearch::class)) {
            return collect();
        }

        try {
            return RecentSearch::where('user_id', auth()->id())
                ->where('term', 'LIKE', "%{$term}%")
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(fn ($search) => Suggestion::recent($search->term, $search->result_count));
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get property title suggestions from Meilisearch.
     */
    protected function getPropertyTitleSuggestions(string $term, int $limit): Collection
    {
        try {
            $results = Property::search($term)
                ->take($limit)
                ->get();

            return $results->map(fn ($property) => Suggestion::property(
                title: $property->title,
                location: $property->area?->name ?? $property->city?->name ?? '',
                slug: $property->slug,
            ));
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get location suggestions (cities and areas).
     */
    protected function getLocationSuggestions(string $term, int $limit): Collection
    {
        $suggestions = collect();

        // Cities
        $cities = City::where('name', 'LIKE', "%{$term}%")
            ->with('state')
            ->limit(ceil($limit / 2))
            ->get();

        foreach ($cities as $city) {
            $suggestions->push(Suggestion::location(
                name: $city->name,
                type: 'city',
                parent: $city->state?->name,
            ));
        }

        // Areas
        $areas = Area::where('name', 'LIKE', "%{$term}%")
            ->with('city')
            ->limit(floor($limit / 2))
            ->get();

        foreach ($areas as $area) {
            $suggestions->push(Suggestion::location(
                name: $area->name,
                type: 'area',
                parent: $area->city?->name,
            ));
        }

        return $suggestions->take($limit);
    }

    /**
     * Get property type suggestions.
     */
    protected function getPropertyTypeSuggestions(string $term, int $limit): Collection
    {
        return PropertyType::where('name', 'LIKE', "%{$term}%")
            ->withCount(['properties' => fn ($q) => $q->published()->available()])
            ->limit($limit)
            ->get()
            ->map(fn ($type) => Suggestion::propertyType(
                name: $type->name,
                count: $type->properties_count,
            ));
    }

    /**
     * Record search for logged-in users.
     */
    protected function recordSearch(SearchQuery $query, int $resultCount): void
    {
        if (!auth()->check() || empty($query->term)) {
            return;
        }

        if (!class_exists(RecentSearch::class)) {
            return;
        }

        try {
            RecentSearch::record(
                user: auth()->user(),
                term: $query->term,
                filters: $query->hasFilters() ? $query->toArray() : null,
                resultCount: $resultCount,
            );
        } catch (\Exception $e) {
            // Silently fail - not critical
        }
    }
}
