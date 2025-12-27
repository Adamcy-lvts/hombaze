<?php

namespace App\Search;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchResult
{
    public function __construct(
        public Collection $properties,
        public int $total,
        public array $facets = [],
        public int $processingTimeMs = 0,
        public int $featuredCount = 0,
        public int $page = 1,
        public int $perPage = 12,
    ) {}

    public function hasFeaturedResults(): bool
    {
        return $this->featuredCount > 0;
    }

    public function toPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            items: $this->properties,
            total: $this->total,
            perPage: $this->perPage,
            currentPage: $this->page,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function toArray(): array
    {
        return [
            'data' => $this->properties->toArray(),
            'meta' => [
                'total' => $this->total,
                'per_page' => $this->perPage,
                'current_page' => $this->page,
                'last_page' => (int) ceil($this->total / $this->perPage),
                'featured_count' => $this->featuredCount,
                'processing_time_ms' => $this->processingTimeMs,
            ],
            'facets' => $this->facets,
        ];
    }
}
