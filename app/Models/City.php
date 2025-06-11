<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'state_id',
        'type',
        'postal_code',
        'description',
        'latitude',
        'longitude',
        'is_active',
        'sort_order'
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
        'sort_order' => 'integer'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($city) {
            if (!$city->slug) {
                $city->slug = Str::slug($city->name);
            }
        });

        static::updating(function ($city) {
            if ($city->isDirty('name') && !$city->isDirty('slug')) {
                $city->slug = Str::slug($city->name);
            }
        });
    }

    /**
     * Relationship: State this city belongs to
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relationship: Areas in this city
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Relationship: Properties in this city
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Relationship: Agencies in this city
     */
    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class);
    }

    /**
     * Relationship: User profiles in this city
     */
    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    /**
     * Scope: Get only active cities
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order cities by sort order then name
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Filter by state
     */
    public function scopeByState(Builder $query, int $stateId): Builder
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Get the full name with state
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name}, {$this->state->name}";
    }

    /**
     * Get the areas count for this city
     */
    public function getAreasCountAttribute(): int
    {
        return $this->areas()->count();
    }

    /**
     * Get available city types
     */
    public static function getTypes(): array
    {
        return [
            'city' => 'City',
            'town' => 'Town',
            'village' => 'Village'
        ];
    }

    /**
     * Get major Nigerian cities for seeding
     */
    public static function getMajorCities(): array
    {
        return [
            // Lagos State
            ['name' => 'Lagos', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Ikeja', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Surulere', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Victoria Island', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Lekki', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Ikoyi', 'state_code' => 'LA', 'type' => 'city'],
            
            // Abuja (FCT)
            ['name' => 'Abuja', 'state_code' => 'FC', 'type' => 'city'],
            ['name' => 'Garki', 'state_code' => 'FC', 'type' => 'city'],
            ['name' => 'Wuse', 'state_code' => 'FC', 'type' => 'city'],
            ['name' => 'Maitama', 'state_code' => 'FC', 'type' => 'city'],
            
            // Rivers State
            ['name' => 'Port Harcourt', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Obio-Akpor', 'state_code' => 'RI', 'type' => 'city'],
            
            // Kano State
            ['name' => 'Kano', 'state_code' => 'KN', 'type' => 'city'],
            
            // Oyo State
            ['name' => 'Ibadan', 'state_code' => 'OY', 'type' => 'city'],
            
            // Kaduna State
            ['name' => 'Kaduna', 'state_code' => 'KD', 'type' => 'city'],
            
            // Anambra State
            ['name' => 'Awka', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Onitsha', 'state_code' => 'AN', 'type' => 'city'],
            
            // Enugu State
            ['name' => 'Enugu', 'state_code' => 'EN', 'type' => 'city'],
            
            // Cross River State
            ['name' => 'Calabar', 'state_code' => 'CR', 'type' => 'city'],
            
            // Akwa Ibom State
            ['name' => 'Uyo', 'state_code' => 'AK', 'type' => 'city']
        ];
    }
}
