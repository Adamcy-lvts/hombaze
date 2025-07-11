<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasTenants, HasName, FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'user_type',
        'is_verified',
        'phone_verified_at',
        'avatar',
        'is_active',
        'preferences',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'preferences' => 'array',
        ];
    }

    /**
     * Relationship: User profile
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Relationship: User's agencies (if agency owner)
     */
    public function ownedAgencies(): HasMany
    {
        return $this->hasMany(Agency::class, 'owner_id');
    }

    /**
     * Relationship: User's agent profile
     */
    public function agentProfile(): HasOne
    {
        return $this->hasOne(Agent::class);
    }

    /**
     * Relationship: User's tenant profile
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * Many-to-many relationship with agencies (for Filament tenancy)
     */
    public function agencies(): BelongsToMany
    {
        return $this->belongsToMany(Agency::class, 'agency_user')
            ->withPivot(['role', 'is_active', 'permissions', 'joined_at', 'left_at'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    // get me the fist agency the user belongs to
    public function firstAgency(): ?Agency
    {
        return $this->agencies()->first();
    }

    /**
     * Get all agencies including inactive ones
     */
    public function allAgencies(): BelongsToMany
    {
        return $this->belongsToMany(Agency::class, 'agency_user')
            ->withPivot(['role', 'is_active', 'permissions', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /**
     * Get the tenants that the user belongs to (for Filament tenancy)
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->agencies;
    }

    /**
     * Check if the user can access the given tenant (for Filament tenancy)
     */
    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->user_type === 'super_admin') {
            return true; // Super admin can access all tenants
        }

        return $this->agencies()->where('agencies.id', $tenant->id)->exists();
    }

    /**
     * Check if user has a specific role in an agency
     */
    public function hasRoleInAgency(Agency $agency, string $role): bool
    {
        return $this->agencies()
            ->where('agencies.id', $agency->id)
            ->wherePivot('role', $role)
            ->exists();
    }

    /**
     * Check if user is owner of a specific agency
     */
    public function isOwnerOfAgency(Agency $agency): bool
    {
        return $this->hasRoleInAgency($agency, 'owner');
    }

    /**
     * Check if user is admin of an agency
     */
    public function isAgencyAdmin(Agency $agency): bool
    {
        return $this->hasRoleInAgency($agency, 'admin');
    }

    /**
     * Check if user is agent of an agency
     */
    public function isAgencyAgent(Agency $agency): bool
    {
        return $this->hasRoleInAgency($agency, 'agent');
    }

    /**
     * Relationship: User's properties (if property owner)
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    /**
     * Relationship: User's property inquiries
     */
    public function propertyInquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class, 'inquirer_id');
    }

    /**
     * Relationship: User's property viewings
     */
    public function propertyViewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class, 'inquirer_id');
    }

    /**
     * Relationship: Property viewings as agent
     */
    public function agentViewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class, 'agent_id');
    }

    /**
     * Relationship: Inquiry responses
     */
    public function inquiryResponses(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class, 'responded_by');
    }

    /**
     * Relationship: User's saved properties
     */
    public function savedProperties(): HasMany
    {
        return $this->hasMany(SavedProperty::class);
    }

    /**
     * Relationship: User's saved searches
     */
    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    /**
     * Relationship: User's reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope: Filter by user type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('user_type', $type);
    }

    /**
     * Scope: Get only active users
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get only verified users
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if user is agency owner
     */
    public function isAgencyOwner(): bool
    {
        return $this->user_type === 'agency_owner';
    }

    /**
     * Check if user is agent
     */
    public function isAgent(): bool
    {
        return $this->user_type === 'agent';
    }

    /**
     * Check if user is property owner
     */
    public function isPropertyOwner(): bool
    {
        return $this->user_type === 'property_owner';
    }

    /**
     * Check if user is tenant
     */
    public function isTenant(): bool
    {
        return $this->user_type === 'tenant';
    }

    /**
     * Get user's full name from profile or default to name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->profile && $this->profile->first_name && $this->profile->last_name) {
            return "{$this->profile->first_name} {$this->profile->last_name}";
        }
        return $this->name;
    }

    /**
     * Get available user types
     */
    public static function getUserTypes(): array
    {
        return [
            'admin' => 'Administrator',
            'agency_owner' => 'Agency Owner',
            'agent' => 'Real Estate Agent',
            'property_owner' => 'Property Owner',
            'tenant' => 'Tenant/Buyer'
        ];
    }

    // =================================================================
    // FILAMENT INTERFACE IMPLEMENTATIONS
    // =================================================================

    /**
     * Get the user's name for Filament (HasName interface)
     */
    public function getFilamentName(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Check if user can access a specific Filament panel (FilamentUser interface)
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Super admin can access all panels
        if ($this->user_type === 'super_admin') {
            return true;
        }

        // Check panel-specific access based on user type
        return match ($panel->getId()) {
            'admin' => in_array($this->user_type, ['super_admin', 'admin']),
            'agency' => in_array($this->user_type, ['agency_owner', 'agent']) && $this->agencies()->exists(),
            'agent' => $this->user_type === 'agent',
            'landlord' => $this->user_type === 'property_owner',
            'tenant' => $this->user_type === 'tenant',
            default => false,
        };
    }

    /**
     * Get the user's avatar URL for Filament (HasAvatar interface)
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar;
    }
}
