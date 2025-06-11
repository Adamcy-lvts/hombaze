<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'name',
        'code',
        'region',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'region' => 'string',
    ];

    // Relationships
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    // Helper methods
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    public static function getRegions(): array
    {
        return [
            'north_central' => 'North Central',
            'north_east' => 'North East',
            'north_west' => 'North West',
            'south_east' => 'South East',
            'south_south' => 'South South',
            'south_west' => 'South West',
        ];
    }
}
