<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Agent extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'license_number',
        'license_expiry_date',
        'bio',
        'specializations',
        'years_experience',
        'commission_rate',
        'languages',
        'service_areas',
        'rating',
        'total_reviews',
        'total_properties',
        'active_listings',
        'properties_sold',
        'properties_rented',
        'is_available',
        'is_verified',
        'is_featured',
        'accepts_new_clients',
        'verified_at',
        'last_active_at',
        'user_id',
        'agency_id',
    ];

    protected $casts = [
        'languages' => 'array',
        'service_areas' => 'array',
        'commission_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_available' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'accepts_new_clients' => 'boolean',
        'verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'license_expiry_date' => 'date',
    ];

    /**
     * Relationship: Agent's user account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Agent's agency
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Relationship: Agent's properties
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Relationship: Properties assigned to agent
     */
    public function assignedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'assigned_agent_id');
    }

    /**
     * Relationship: Agent's inquiries
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    /**
     * Relationship: Agent reviews
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Relationship: Areas the agent serves
     */
    public function serviceAreaModels(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'agent_service_areas', 'agent_id', 'area_id');
    }

    /**
     * Scope: Get available agents
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: Get verified agents
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Get featured agents
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Get agents accepting new clients
     */
    public function scopeAcceptingClients(Builder $query): Builder
    {
        return $query->where('accepts_new_clients', true);
    }

    /**
     * Scope: Filter by agency
     */
    public function scopeFromAgency(Builder $query, int $agencyId): Builder
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Scope: Filter by rating
     */
    public function scopeWithMinRating(Builder $query, float $minRating): Builder
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope: Filter by experience
     */
    public function scopeWithMinExperience(Builder $query, int $minYears): Builder
    {
        return $query->where('years_experience', '>=', $minYears);
    }

    /**
     * Scope: Filter by service area
     */
    public function scopeServingArea(Builder $query, int $areaId): Builder
    {
        return $query->whereJsonContains('service_areas', $areaId);
    }

    /**
     * Get agent's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->full_name : 'Unknown Agent';
    }

    /**
     * Get agent's name (alias for full_name for consistency)
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Get agent's specializations as array
     */
    public function getSpecializationsArrayAttribute(): array
    {
        if (!$this->specializations) {
            return [];
        }
        return explode(',', $this->specializations);
    }

    /**
     * Check if license is expired
     */
    public function isLicenseExpired(): bool
    {
        return $this->license_expiry_date < now()->toDateString();
    }

    /**
     * Get agent's rating color class
     */
    public function getRatingColorAttribute(): string
    {
        if ($this->rating >= 4.5) return 'success';
        if ($this->rating >= 4.0) return 'info';
        if ($this->rating >= 3.5) return 'warning';
        return 'danger';
    }

    /**
     * Get agent's experience level
     */
    public function getExperienceLevelAttribute(): string
    {
        if ($this->years_experience >= 10) return 'Senior';
        if ($this->years_experience >= 5) return 'Experienced';
        if ($this->years_experience >= 2) return 'Intermediate';
        return 'Junior';
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->total_properties === 0) return 0;
        return round(($this->properties_sold + $this->properties_rented) / $this->total_properties * 100, 2);
    }

    /**
     * Update last active timestamp
     */
    public function updateLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Check if agent is independent (not part of an agency)
     */
    public function isIndependent(): bool
    {
        return $this->agency_id === null;
    }

    /**
     * Get languages as formatted string
     */
    public function getLanguagesStringAttribute(): string
    {
        if (!$this->languages) {
            return 'English';
        }
        return implode(', ', $this->languages);
    }


    /**
     * Register media collections for agent files
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certifications')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        // No conversions needed for certifications (they're documents/PDFs)
    }

    /**
     * Get profile photo URL from associated user
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->user?->avatar;
    }

    /**
     * Get certifications
     */
    public function getCertificationsAttribute()
    {
        return $this->getMedia('certifications');
    }

    /**
     * Check if profile photo exists
     */
    public function hasProfilePhoto(): bool
    {
        return !empty($this->user?->avatar);
    }

    /**
     * Check if certifications exist
     */
    public function hasCertifications(): bool
    {
        return $this->hasMedia('certifications');
    }
}
