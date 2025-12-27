<?php

namespace App\Search;

use Illuminate\Http\Request;

class SearchQuery
{
    public function __construct(
        public ?string $term = null,
        public ?string $listingType = null,
        public ?int $propertyTypeId = null,
        public ?int $stateId = null,
        public ?int $cityId = null,
        public ?int $areaId = null,
        public ?int $minPrice = null,
        public ?int $maxPrice = null,
        public ?array $bedrooms = null,
        public ?array $bathrooms = null,
        public ?string $furnishing = null,
        public ?array $features = null,
        public ?bool $featuredOnly = null,
        public ?bool $verifiedOnly = null,
        public string $sortBy = 'relevance',
        public int $perPage = 12,
        public int $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            term: $data['term'] ?? $data['q'] ?? null,
            listingType: $data['listing_type'] ?? null,
            propertyTypeId: isset($data['property_type_id']) ? (int) $data['property_type_id'] : null,
            stateId: isset($data['state_id']) ? (int) $data['state_id'] : null,
            cityId: isset($data['city_id']) ? (int) $data['city_id'] : null,
            areaId: isset($data['area_id']) ? (int) $data['area_id'] : null,
            minPrice: isset($data['min_price']) ? (int) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (int) $data['max_price'] : null,
            bedrooms: $data['bedrooms'] ?? null,
            bathrooms: $data['bathrooms'] ?? null,
            furnishing: $data['furnishing'] ?? null,
            features: $data['features'] ?? null,
            featuredOnly: isset($data['featured_only']) ? (bool) $data['featured_only'] : null,
            verifiedOnly: isset($data['verified_only']) ? (bool) $data['verified_only'] : null,
            sortBy: $data['sort'] ?? $data['sort_by'] ?? 'relevance',
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 12,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->all());
    }

    public function toArray(): array
    {
        return array_filter([
            'term' => $this->term,
            'listing_type' => $this->listingType,
            'property_type_id' => $this->propertyTypeId,
            'state_id' => $this->stateId,
            'city_id' => $this->cityId,
            'area_id' => $this->areaId,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'furnishing' => $this->furnishing,
            'features' => $this->features,
            'featured_only' => $this->featuredOnly,
            'verified_only' => $this->verifiedOnly,
            'sort_by' => $this->sortBy,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ], fn ($value) => $value !== null);
    }

    public function hasFilters(): bool
    {
        return $this->listingType !== null
            || $this->propertyTypeId !== null
            || $this->stateId !== null
            || $this->cityId !== null
            || $this->areaId !== null
            || $this->minPrice !== null
            || $this->maxPrice !== null
            || $this->bedrooms !== null
            || $this->bathrooms !== null
            || $this->furnishing !== null
            || $this->features !== null
            || $this->featuredOnly !== null
            || $this->verifiedOnly !== null;
    }
}
