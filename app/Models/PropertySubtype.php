<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PropertySubtype extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'property_type_id',
        'description',
        'icon',
        'is_active',
        'sort_order',
        'typical_features',
        'typical_price_min',
        'typical_price_max'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'typical_features' => 'array',
        'typical_price_min' => 'decimal:2',
        'typical_price_max' => 'decimal:2'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($propertySubtype) {
            if (!$propertySubtype->slug) {
                $propertySubtype->slug = Str::slug($propertySubtype->name);
            }
        });

        static::updating(function ($propertySubtype) {
            if ($propertySubtype->isDirty('name') && !$propertySubtype->isDirty('slug')) {
                $propertySubtype->slug = Str::slug($propertySubtype->name);
            }
        });
    }

    /**
     * Relationship: Property type this subtype belongs to
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Relationship: Properties of this subtype
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Scope: Get only active subtypes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order subtypes by sort order then name
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Filter by property type
     */
    public function scopeByPropertyType(Builder $query, int $propertyTypeId): Builder
    {
        return $query->where('property_type_id', $propertyTypeId);
    }

    /**
     * Get the properties count for this subtype
     */
    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    /**
     * Get formatted typical price range
     */
    public function getTypicalPriceRangeAttribute(): string
    {
        if (!$this->typical_price_min && !$this->typical_price_max) {
            return 'Price varies';
        }

        $min = $this->typical_price_min ? '₦' . number_format($this->typical_price_min) : 'From';
        $max = $this->typical_price_max ? '₦' . number_format($this->typical_price_max) : 'upwards';

        return "{$min} - {$max}";
    }

    /**
     * Get default property subtypes for seeding
     */
    public static function getDefaultSubtypes(): array
    {
        return [
            // Residential subtypes
            'residential' => [
                ['name' => 'Studio Apartment', 'description' => 'Single room with kitchenette and bathroom'],
                ['name' => '1 Bedroom Apartment', 'description' => 'One bedroom with living room, kitchen and bathroom'],
                ['name' => '2 Bedroom Apartment', 'description' => 'Two bedrooms with living room, kitchen and bathroom(s)'],
                ['name' => '3 Bedroom Apartment', 'description' => 'Three bedrooms with living room, kitchen and bathroom(s)'],
                ['name' => 'Penthouse', 'description' => 'Luxury top-floor apartment with premium amenities'],
                ['name' => 'Duplex', 'description' => 'Two-story residential unit'],
                ['name' => 'Bungalow', 'description' => 'Single-story detached house'],
                ['name' => 'Detached House', 'description' => 'Standalone house with private compound'],
                ['name' => 'Semi-Detached House', 'description' => 'House sharing one wall with neighboring property'],
                ['name' => 'Terrace House', 'description' => 'Row house sharing walls on both sides'],
                ['name' => 'Mansion', 'description' => 'Large luxury residential property'],
                ['name' => 'Villa', 'description' => 'Upscale house with garden or grounds']
            ],
            
            // Commercial subtypes
            'commercial' => [
                ['name' => 'Office Space', 'description' => 'Professional workspace for businesses'],
                ['name' => 'Retail Shop', 'description' => 'Ground floor commercial space for retail'],
                ['name' => 'Shopping Mall Unit', 'description' => 'Commercial space within shopping complex'],
                ['name' => 'Restaurant Space', 'description' => 'Commercial kitchen and dining space'],
                ['name' => 'Bank Branch', 'description' => 'Financial services commercial space'],
                ['name' => 'Medical Center', 'description' => 'Healthcare facility space'],
                ['name' => 'Coworking Space', 'description' => 'Shared office environment'],
                ['name' => 'Event Center', 'description' => 'Large space for events and gatherings']
            ],
            
            // Industrial subtypes
            'industrial' => [
                ['name' => 'Warehouse', 'description' => 'Large storage and distribution facility'],
                ['name' => 'Factory', 'description' => 'Manufacturing facility'],
                ['name' => 'Cold Storage', 'description' => 'Temperature-controlled storage facility'],
                ['name' => 'Logistics Center', 'description' => 'Distribution and logistics hub']
            ],
            
            // Land subtypes
            'land' => [
                ['name' => 'Residential Land', 'description' => 'Zoned for residential development'],
                ['name' => 'Commercial Land', 'description' => 'Zoned for commercial development'],
                ['name' => 'Industrial Land', 'description' => 'Zoned for industrial development'],
                ['name' => 'Agricultural Land', 'description' => 'Farmland for agricultural purposes'],
                ['name' => 'Mixed-Use Land', 'description' => 'Multi-purpose development land']
            ]
        ];
    }

    /**
     * Get typical features for residential properties
     */
    public static function getTypicalResidentialFeatures(): array
    {
        return [
            'studio' => ['air_conditioning', 'fitted_kitchen', 'tiled_floors'],
            '1_bedroom' => ['air_conditioning', 'fitted_kitchen', 'tiled_floors', 'built_in_wardrobes'],
            '2_bedroom' => ['air_conditioning', 'fitted_kitchen', 'tiled_floors', 'built_in_wardrobes', 'balcony'],
            '3_bedroom' => ['air_conditioning', 'fitted_kitchen', 'tiled_floors', 'built_in_wardrobes', 'balcony', 'parking_space'],
            'duplex' => ['air_conditioning', 'fitted_kitchen', 'tiled_floors', 'built_in_wardrobes', 'balcony', 'parking_space', 'garden'],
            'bungalow' => ['air_conditioning', 'fitted_kitchen', 'tiled_floors', 'built_in_wardrobes', 'parking_space', 'garden', 'fence'],
            'detached' => ['air_conditioning', 'fitted_kitchen', 'tiled_floors', 'built_in_wardrobes', 'parking_space', 'garden', 'fence', 'garage']
        ];
    }
}
