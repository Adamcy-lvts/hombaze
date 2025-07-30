<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
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
        'amenities',
        // Neighborhood data
        'education_facilities',
        'healthcare_facilities',
        'shopping_facilities', 
        'transport_facilities',
        'security_rating',
        'security_features',
        'crime_rate',
        'population',
        'average_rent',
        'walkability_score',
        'lifestyle_tags',
        'utilities',
        'road_condition',
        'electricity_supply',
        'water_supply'
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
        'amenities' => 'array',
        // Neighborhood data
        'education_facilities' => 'array',
        'healthcare_facilities' => 'array',
        'shopping_facilities' => 'array',
        'transport_facilities' => 'array',
        'security_rating' => 'decimal:1',
        'security_features' => 'array',
        'crime_rate' => 'decimal:2',
        'population' => 'integer',
        'average_rent' => 'decimal:2',
        'walkability_score' => 'decimal:1',
        'lifestyle_tags' => 'array',
        'utilities' => 'array',
        'road_condition' => 'array',
        'electricity_supply' => 'array',
        'water_supply' => 'array'
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
    public function state()
    {
        return $this->hasOneThrough(State::class, City::class, 'id', 'id', 'city_id', 'state_id');
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
     * Get security rating display
     */
    public function getSecurityRatingDisplayAttribute(): string
    {
        if (!$this->security_rating) return 'Not Rated';
        
        $rating = $this->security_rating;
        
        if ($rating >= 9.0) return 'Excellent';
        if ($rating >= 8.0) return 'Very Good';
        if ($rating >= 7.0) return 'Good';
        if ($rating >= 6.0) return 'Fair';
        if ($rating >= 5.0) return 'Average';
        return 'Below Average';
    }
    
    /**
     * Get security rating stars (out of 5)
     */
    public function getSecurityStarsAttribute(): int
    {
        if (!$this->security_rating) return 0;
        return (int) round(($this->security_rating / 10) * 5);
    }
    
    /**
     * Get formatted average rent
     */
    public function getFormattedAverageRentAttribute(): string
    {
        if (!$this->average_rent) return 'N/A';
        return '₦' . number_format($this->average_rent, 0);
    }
    
    /**
     * Get walkability score display
     */
    public function getWalkabilityDisplayAttribute(): string
    {
        if (!$this->walkability_score) return 'Not Rated';
        
        $score = $this->walkability_score;
        if ($score >= 9.0) return 'Very Walkable';
        if ($score >= 7.0) return 'Walkable';
        if ($score >= 5.0) return 'Somewhat Walkable';
        return 'Car Dependent';
    }
    
    /**
     * Get education facilities with distances
     */
    public function getEducationFacilitiesAttribute($value)
    {
        if (!$value) {
            return [
                ['name' => 'Green Valley Primary', 'distance' => '0.8km', 'type' => 'primary'],
                ['name' => 'Excellence Secondary', 'distance' => '1.2km', 'type' => 'secondary']
            ];
        }
        return json_decode($value, true);
    }
    
    /**
     * Get healthcare facilities with distances
     */
    public function getHealthcareFacilitiesAttribute($value)
    {
        if (!$value) {
            return [
                ['name' => 'General Hospital', 'distance' => '1.5km', 'type' => 'hospital'],
                ['name' => 'MediCare Clinic', 'distance' => '0.4km', 'type' => 'clinic']
            ];
        }
        return json_decode($value, true);
    }
    
    /**
     * Get shopping facilities with distances
     */
    public function getShoppingFacilitiesAttribute($value)
    {
        if (!$value) {
            return [
                ['name' => 'City Mall', 'distance' => '2.1km', 'type' => 'mall'],
                ['name' => 'Central Market', 'distance' => '0.5km', 'type' => 'market']
            ];
        }
        return json_decode($value, true);
    }
    
    /**
     * Get transport facilities with distances
     */
    public function getTransportFacilitiesAttribute($value)
    {
        if (!$value) {
            return [
                ['name' => 'Express Road', 'distance' => '0.3km', 'type' => 'road'],
                ['name' => 'BRT Station', 'distance' => '0.7km', 'type' => 'brt']
            ];
        }
        return json_decode($value, true);
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
