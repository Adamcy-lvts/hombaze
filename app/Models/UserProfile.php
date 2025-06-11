<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'bio',
        'occupation',
        'annual_income',
        'state_id',
        'city_id',
        'area_id',
        'address',
        'postal_code',
        'alternate_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'budget_min',
        'budget_max',
        'preferred_property_types',
        'preferred_locations',
        'preferred_features',
        'preferred_bedrooms_min',
        'preferred_bedrooms_max',
        'id_type',
        'id_number',
        'is_id_verified',
        'id_verified_at',
        'linkedin_url',
        'twitter_url',
        'facebook_url',
        'website_url',
        'is_complete'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'annual_income' => 'decimal:2',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'preferred_property_types' => 'array',
        'preferred_locations' => 'array',
        'preferred_features' => 'array',
        'preferred_bedrooms_min' => 'integer',
        'preferred_bedrooms_max' => 'integer',
        'is_id_verified' => 'boolean',
        'id_verified_at' => 'datetime',
        'is_complete' => 'boolean'
    ];

    /**
     * Relationship: User this profile belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: State
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relationship: City
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relationship: Area
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Scope: Get verified profiles
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_id_verified', true);
    }

    /**
     * Scope: Get complete profiles
     */
    public function scopeComplete(Builder $query): Builder
    {
        return $query->where('is_complete', true);
    }

    /**
     * Scope: Filter by location
     */
    public function scopeByLocation(Builder $query, ?int $stateId = null, ?int $cityId = null, ?int $areaId = null): Builder
    {
        if ($stateId) {
            $query->where('state_id', $stateId);
        }
        if ($cityId) {
            $query->where('city_id', $cityId);
        }
        if ($areaId) {
            $query->where('area_id', $areaId);
        }
        return $query;
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->area?->name,
            $this->city?->name,
            $this->state?->name
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Check if profile is complete
     */
    public function checkCompleteness(): bool
    {
        $requiredFields = [
            'first_name', 'last_name', 'date_of_birth', 'gender',
            'state_id', 'city_id', 'occupation'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Update completion status
        $this->update(['is_complete' => true]);
        return true;
    }

    /**
     * Get preferred property types with names
     */
    public function getPreferredPropertyTypesWithNamesAttribute(): array
    {
        if (!$this->preferred_property_types) {
            return [];
        }

        return PropertyType::whereIn('id', $this->preferred_property_types)
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get available ID types
     */
    public static function getIdTypes(): array
    {
        return [
            'nin' => 'National Identification Number (NIN)',
            'bvn' => 'Bank Verification Number (BVN)',
            'drivers_license' => "Driver's License",
            'voters_card' => "Voter's Registration Card",
            'passport' => 'International Passport'
        ];
    }

    /**
     * Get budget range formatted
     */
    public function getBudgetRangeAttribute(): string
    {
        if (!$this->budget_min && !$this->budget_max) {
            return 'Not specified';
        }

        $min = $this->budget_min ? '₦' . number_format($this->budget_min) : 'No minimum';
        $max = $this->budget_max ? '₦' . number_format($this->budget_max) : 'No maximum';

        return "{$min} - {$max}";
    }
}
