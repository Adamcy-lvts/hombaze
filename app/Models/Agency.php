<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class Agency extends Model implements HasTenants
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'license_number',
        'license_expiry_date',
        'email',
        'phone',
        'website',
        'address',
        'latitude',
        'longitude',
        'logo',
        'social_media',
        'specializations',
        'years_in_business',
        'rating',
        'total_reviews',
        'total_properties',
        'total_agents',
        'is_verified',
        'is_featured',
        'is_active',
        'verified_at',
        'owner_id',
        'state_id',
        'city_id',
        'area_id',
    ];

    protected $casts = [
        'address' => 'array',
        'social_media' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'license_expiry_date' => 'date',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agency) {
            if (empty($agency->slug)) {
                $agency->slug = Str::slug($agency->name);
            }
        });
    }

    /**
     * Relationship: Agency owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relationship: Agency state
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relationship: Agency city
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relationship: Agency area
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relationship: Agency agents
     */
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    /**
     * Relationship: Agency properties
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Relationship: Agency reviews
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Scope: Get active agencies
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get verified agencies
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Get featured agencies
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Filter by location
     */
    public function scopeInLocation(Builder $query, ?int $stateId = null, ?int $cityId = null, ?int $areaId = null): Builder
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
     * Scope: Filter by rating
     */
    public function scopeWithMinRating(Builder $query, float $minRating): Builder
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Get agency's address as string
     */
    public function getAddressStringAttribute(): string
    {
        if (!$this->address) {
            return '';
        }

        $parts = [];
        if (!empty($this->address['street'])) {
            $parts[] = $this->address['street'];
        }
        if (!empty($this->address['area']) && $this->area) {
            $parts[] = $this->area->name;
        }
        if ($this->city) {
            $parts[] = $this->city->name;
        }
        if ($this->state) {
            $parts[] = $this->state->name;
        }

        return implode(', ', $parts);
    }

    /**
     * Get agency's specializations as array
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
     * Get agency's rating color class
     */
    public function getRatingColorAttribute(): string
    {
        if ($this->rating >= 4.5) return 'success';
        if ($this->rating >= 4.0) return 'info';
        if ($this->rating >= 3.5) return 'warning';
        return 'danger';
    }

    /**
     * Get active agents count
     */
    public function getActiveAgentsCountAttribute(): int
    {
        return $this->agents()->whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->count();
    }

    /**
     * Get the tenants that the user belongs to.
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->users;
    }

    /**
     * Check if the user can access the given tenant.
     */
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->users()->where('user_id', Auth::id())->exists();
    }

    /**
     * Get the tenant name for display.
     */
    public function getTenantName(): string
    {
        return $this->name;
    }

    /**
     * Many-to-many relationship with users (for Filament tenancy)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agency_user')
                    ->withPivot(['role', 'is_active', 'permissions', 'joined_at', 'left_at'])
                    ->withTimestamps()
                    ->wherePivot('is_active', true);
    }

    /**
     * Relationship: Agency roles (for Filament Shield)
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'agency_id');
    }

    /**
     * Get all users including inactive ones
     */
    public function allUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agency_user')
                    ->withPivot(['role', 'is_active', 'permissions', 'joined_at', 'left_at'])
                    ->withTimestamps();
    }

    /**
     * Get agency owners
     */
    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /**
     * Get agency admins
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * Get agency agents
     */
    public function agencyAgents(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'agent');
    }

    /**
     * Get agency managers
     */
    public function managers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'manager');
    }

    /**
     * Get the super admin user for this agency
     */
    public function getSuperAdmin(): ?User
    {
        // First try to get from agency_user pivot table with 'owner' role
        $owner = $this->owners()->first();
        if ($owner) {
            return $owner;
        }

        // If no owner found, try to get user with super_admin role for this agency
        $superAdmin = User::role('super_admin')
            ->whereHas('agencies', function ($query) {
                $query->where('agency_id', $this->id);
            })
            ->first();

        if ($superAdmin) {
            return $superAdmin;
        }

        // Fallback to agency owner_id relationship
        return $this->owner;
    }
}
