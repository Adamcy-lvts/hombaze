<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PropertyFeature extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'is_active',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($propertyFeature) {
            if (!$propertyFeature->slug) {
                $propertyFeature->slug = Str::slug($propertyFeature->name);
            }
        });

        static::updating(function ($propertyFeature) {
            if ($propertyFeature->isDirty('name') && !$propertyFeature->isDirty('slug')) {
                $propertyFeature->slug = Str::slug($propertyFeature->name);
            }
        });
    }

    /**
     * Relationship: Properties that have this feature
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_feature_property')
            ->withTimestamps();
    }

    /**
     * Scope: Get only active features
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order features by sort order then name
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Get the properties count for this feature
     */
    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    /**
     * Get available feature categories
     */
    public static function getCategories(): array
    {
        return [
            'interior' => 'Interior Features',
            'exterior' => 'Exterior Features',
            'amenities' => 'Amenities',
            'utilities' => 'Utilities',
            'security' => 'Security Features'
        ];
    }

    /**
     * Get default property features for seeding
     */
    public static function getDefaultFeatures(): array
    {
        return [
            // Interior Features
            ['name' => 'Air Conditioning', 'category' => 'interior', 'icon' => 'heroicon-o-sun'],
            ['name' => 'Built-in Wardrobes', 'category' => 'interior', 'icon' => 'heroicon-o-archive-box'],
            ['name' => 'Fitted Kitchen', 'category' => 'interior', 'icon' => 'heroicon-o-wrench-screwdriver'],
            ['name' => 'Ceiling Fans', 'category' => 'interior', 'icon' => 'heroicon-o-cog'],
            ['name' => 'Tiled Floors', 'category' => 'interior', 'icon' => 'heroicon-o-squares-2x2'],
            
            // Exterior Features
            ['name' => 'Balcony', 'category' => 'exterior', 'icon' => 'heroicon-o-building-office-2'],
            ['name' => 'Garden', 'category' => 'exterior', 'icon' => 'heroicon-o-camera'],
            ['name' => 'Parking Space', 'category' => 'exterior', 'icon' => 'heroicon-o-truck'],
            ['name' => 'Garage', 'category' => 'exterior', 'icon' => 'heroicon-o-home'],
            ['name' => 'Fence', 'category' => 'exterior', 'icon' => 'heroicon-o-shield-check'],
            
            // Amenities
            ['name' => 'Swimming Pool', 'category' => 'amenities', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Gym', 'category' => 'amenities', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Children Playground', 'category' => 'amenities', 'icon' => 'heroicon-o-gift'],
            ['name' => 'Club House', 'category' => 'amenities', 'icon' => 'heroicon-o-building-storefront'],
            ['name' => 'Elevator', 'category' => 'amenities', 'icon' => 'heroicon-o-arrow-up'],
            
            // Utilities
            ['name' => 'Water Supply', 'category' => 'utilities', 'icon' => 'heroicon-o-beaker'],
            ['name' => 'Electricity', 'category' => 'utilities', 'icon' => 'heroicon-o-bolt'],
            ['name' => 'Internet Ready', 'category' => 'utilities', 'icon' => 'heroicon-o-wifi'],
            ['name' => 'Cable TV Ready', 'category' => 'utilities', 'icon' => 'heroicon-o-tv'],
            ['name' => 'Gas Supply', 'category' => 'utilities', 'icon' => 'heroicon-o-fire'],
            
            // Security Features
            ['name' => 'Security Guards', 'category' => 'security', 'icon' => 'heroicon-o-shield-check'],
            ['name' => 'CCTV', 'category' => 'security', 'icon' => 'heroicon-o-eye'],
            ['name' => 'Access Control', 'category' => 'security', 'icon' => 'heroicon-o-key'],
            ['name' => 'Intercom', 'category' => 'security', 'icon' => 'heroicon-o-phone'],
            ['name' => 'Gated Community', 'category' => 'security', 'icon' => 'heroicon-o-home-modern']
        ];
    }
}
