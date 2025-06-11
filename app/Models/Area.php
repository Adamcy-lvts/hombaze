<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Area extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'city_id',
        'type',
        'description',
        'latitude',
        'longitude',
        'is_active',
        'sort_order',
        'amenities'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'amenities' => 'array'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($area) {
            if (!$area->slug) {
                $area->slug = Str::slug($area->name);
            }
        });

        static::updating(function ($area) {
            if ($area->isDirty('name') && !$area->isDirty('slug')) {
                $area->slug = Str::slug($area->name);
            }
        });
    }

    /**
     * Relationship: City this area belongs to
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relationship: State through city
     */
    public function state(): BelongsTo
    {
        return $this->city()->state();
    }

    /**
     * Relationship: Properties in this area
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Relationship: Agencies in this area
     */
    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class);
    }

    /**
     * Relationship: User profiles in this area
     */
    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    /**
     * Scope: Get only active areas
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order areas by sort order then name
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Filter by city
     */
    public function scopeByCity(Builder $query, int $cityId): Builder
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Get the full name with city and state
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name}, {$this->city->name}, {$this->city->state->name}";
    }

    /**
     * Get the properties count for this area
     */
    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    /**
     * Check if area has specific amenity
     */
    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities ?? []);
    }

    /**
     * Get available area types
     */
    public static function getTypes(): array
    {
        return [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'mixed' => 'Mixed Use'
        ];
    }

    /**
     * Get available amenities
     */
    public static function getAvailableAmenities(): array
    {
        return [
            'schools' => 'Schools',
            'hospitals' => 'Hospitals/Clinics',
            'markets' => 'Markets/Shopping',
            'banks' => 'Banks/ATMs',
            'restaurants' => 'Restaurants/Eateries',
            'parks' => 'Parks/Recreation',
            'transport' => 'Public Transport',
            'fuel_stations' => 'Fuel Stations',
            'police' => 'Police Station',
            'fire_station' => 'Fire Station',
            'post_office' => 'Post Office',
            'religious' => 'Religious Centers',
            'cinema' => 'Cinema/Entertainment',
            'pharmacy' => 'Pharmacy'
        ];
    }

    /**
     * Get popular Lagos areas for seeding
     */
    public static function getPopularAreas(): array
    {
        return [
            // Victoria Island areas
            ['name' => 'Victoria Island', 'city_name' => 'Victoria Island', 'type' => 'commercial'],
            ['name' => 'Oniru', 'city_name' => 'Victoria Island', 'type' => 'residential'],
            ['name' => 'Banana Island', 'city_name' => 'Victoria Island', 'type' => 'residential'],
            
            // Lekki areas
            ['name' => 'Lekki Phase 1', 'city_name' => 'Lekki', 'type' => 'residential'],
            ['name' => 'Lekki Phase 2', 'city_name' => 'Lekki', 'type' => 'residential'],
            ['name' => 'Ajah', 'city_name' => 'Lekki', 'type' => 'mixed'],
            ['name' => 'Sangotedo', 'city_name' => 'Lekki', 'type' => 'residential'],
            
            // Ikoyi areas
            ['name' => 'Ikoyi', 'city_name' => 'Ikoyi', 'type' => 'residential'],
            ['name' => 'Old Ikoyi', 'city_name' => 'Ikoyi', 'type' => 'residential'],
            
            // Lagos Island areas
            ['name' => 'Lagos Island', 'city_name' => 'Lagos', 'type' => 'commercial'],
            ['name' => 'Marina', 'city_name' => 'Lagos', 'type' => 'commercial'],
            
            // Ikeja areas
            ['name' => 'GRA Ikeja', 'city_name' => 'Ikeja', 'type' => 'residential'],
            ['name' => 'Allen Avenue', 'city_name' => 'Ikeja', 'type' => 'commercial'],
            ['name' => 'Computer Village', 'city_name' => 'Ikeja', 'type' => 'commercial'],
            ['name' => 'Maryland', 'city_name' => 'Ikeja', 'type' => 'mixed'],
            
            // Surulere areas
            ['name' => 'Surulere', 'city_name' => 'Surulere', 'type' => 'residential'],
            ['name' => 'National Stadium', 'city_name' => 'Surulere', 'type' => 'mixed'],
            
            // Abuja areas
            ['name' => 'Garki District', 'city_name' => 'Garki', 'type' => 'mixed'],
            ['name' => 'Wuse 2', 'city_name' => 'Wuse', 'type' => 'commercial'],
            ['name' => 'Maitama District', 'city_name' => 'Maitama', 'type' => 'residential']
        ];
    }
}
