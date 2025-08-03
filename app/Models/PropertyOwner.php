<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PropertyOwner extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'tax_id',
        'user_id', // Optional link to platform user
        'agency_id', // Agency that manages this property owner
        'agent_id', // Independent agent that manages this property owner
        'notes',
        'is_active',
        // New profile completion fields
        'state_id',
        'city_id',
        'area_id',
        'date_of_birth',
        'preferred_communication',
        'profile_photo',
        'id_document',
        'proof_of_address',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'date_of_birth' => 'date',
        'verified_at' => 'datetime',
    ];

    // Owner types
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COMPANY = 'company';
    const TYPE_TRUST = 'trust';
    const TYPE_GOVERNMENT = 'government';

    public static function getTypes(): array
    {
        return [
            self::TYPE_INDIVIDUAL => 'Individual',
            self::TYPE_COMPANY => 'Company/Corporation',
            self::TYPE_TRUST => 'Trust/Estate',
            self::TYPE_GOVERNMENT => 'Government Entity',
        ];
    }

    /**
     * Get the full name or company name
     */
    public function getNameAttribute(): string
    {
        if ($this->type === self::TYPE_INDIVIDUAL) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        
        return $this->company_name ?: 'Unnamed Owner';
    }

    /**
     * Get the display name with type indicator
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        $typeLabel = self::getTypes()[$this->type] ?? 'Unknown';
        
        return "{$name} ({$typeLabel})";
    }

    /**
     * Properties owned by this owner
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    /**
     * Get the managing agency
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the managing agent (for independent agents)
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Optional link to platform user (if owner has an account)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Geographic relationships
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Scope for active owners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for individual owners
     */
    public function scopeIndividuals($query)
    {
        return $query->where('type', self::TYPE_INDIVIDUAL);
    }

    /**
     * Scope for company owners
     */
    public function scopeCompanies($query)
    {
        return $query->where('type', self::TYPE_COMPANY);
    }

    /**
     * Media Library Collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('id_document')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']);

        $this->addMediaCollection('proof_of_address')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']);
    }

    /**
     * Media Library Conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('profile_photo');

        $this->addMediaConversion('preview')
            ->width(500)
            ->height(500)
            ->sharpen(10)
            ->performOnCollections('id_document', 'proof_of_address');
    }
}
