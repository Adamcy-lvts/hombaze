<?php

namespace App\Search;

class Suggestion
{
    public const TYPE_RECENT = 'recent';
    public const TYPE_PROPERTY = 'property';
    public const TYPE_LOCATION = 'location';
    public const TYPE_PROPERTY_TYPE = 'property_type';

    public function __construct(
        public string $text,
        public string $type,
        public string $icon,
        public string $category,
        public ?string $subtitle = null,
        public ?array $meta = null,
    ) {}

    public static function recent(string $text, ?int $resultCount = null): self
    {
        return new self(
            text: $text,
            type: self::TYPE_RECENT,
            icon: 'clock',
            category: 'Recent Searches',
            subtitle: $resultCount ? "{$resultCount} results" : null,
        );
    }

    public static function property(string $title, string $location, string $slug): self
    {
        return new self(
            text: $title,
            type: self::TYPE_PROPERTY,
            icon: 'home',
            category: 'Properties',
            subtitle: $location,
            meta: ['slug' => $slug],
        );
    }

    public static function location(string $name, string $type, ?string $parent = null): self
    {
        $categoryMap = [
            'city' => 'Cities',
            'area' => 'Areas',
            'state' => 'States',
        ];

        return new self(
            text: $name,
            type: self::TYPE_LOCATION,
            icon: 'location-dot',
            category: $categoryMap[$type] ?? 'Locations',
            subtitle: $parent,
            meta: ['location_type' => $type],
        );
    }

    public static function propertyType(string $name, ?int $count = null): self
    {
        return new self(
            text: $name,
            type: self::TYPE_PROPERTY_TYPE,
            icon: 'building',
            category: 'Property Types',
            subtitle: $count ? "{$count} listings" : null,
        );
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'type' => $this->type,
            'icon' => $this->icon,
            'category' => $this->category,
            'subtitle' => $this->subtitle,
            'meta' => $this->meta,
        ];
    }
}
