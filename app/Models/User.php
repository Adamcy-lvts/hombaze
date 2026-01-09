<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
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
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasTenants, HasName, FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
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
        'profile_completed',
        'profile_completed_at',
        'profile_completion_steps',
        'profile_completion_percentage',
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
            'profile_completed_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'profile_completed' => 'boolean',
            'preferences' => 'array',
            'profile_completion_steps' => 'array',
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
     * Relationship: User's property owner profile
     */
    public function propertyOwnerProfile(): HasOne
    {
        return $this->hasOne(PropertyOwner::class);
    }

    /**
     * Relationship: User's customer profile
     */
    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    /**
     * Relationship: User's saved searches (legacy - use smartSearches() instead)
     * @deprecated Use smartSearches() instead
     */
    public function savedSearches(): HasMany
    {
        return $this->hasMany(SmartSearch::class);
    }

    /**
     * Relationship: User's active saved searches (legacy - use activeSmartSearches() instead)
     * @deprecated Use activeSmartSearches() instead
     */
    public function activeSearches(): HasMany
    {
        return $this->smartSearches()->active();
    }

    // =========================================
    // SMARTSEARCH RELATIONSHIPS & METHODS
    // =========================================

    /**
     * Relationship: User's SmartSearches
     */
    public function smartSearches(): HasMany
    {
        return $this->hasMany(SmartSearch::class);
    }

    /**
     * Relationship: User's active SmartSearches
     */
    public function activeSmartSearches(): HasMany
    {
        return $this->smartSearches()->active();
    }

    /**
     * Relationship: User's SmartSearch subscriptions
     */
    public function smartSearchSubscriptions(): HasMany
    {
        return $this->hasMany(SmartSearchSubscription::class);
    }

    /**
     * Relationship: User's SmartSearch matches
     */
    public function smartSearchMatches(): HasMany
    {
        return $this->hasMany(SmartSearchMatch::class);
    }

    /**
     * Get the user's active SmartSearch subscription for a specific tier (or any tier)
     */
    public function activeSmartSearchSubscription(?string $tier = null): ?SmartSearchSubscription
    {
        return $this->smartSearchSubscriptions()
            ->where('payment_status', 'paid')
            ->where('expires_at', '>', now())
            ->when($tier, fn($q) => $q->where('tier', $tier))
            ->latest('expires_at')
            ->first();
    }

    /**
     * Check if user can create a SmartSearch (has active subscription with remaining searches)
     */
    public function canCreateSmartSearch(): bool
    {
        $subscription = $this->activeSmartSearchSubscription();
        return $subscription && $subscription->canCreateSearch();
    }

    /**
     * Get the user's current SmartSearch tier (highest active tier)
     */
    public function getSmartSearchTier(): ?string
    {
        $subscription = $this->activeSmartSearchSubscription();
        return $subscription?->tier;
    }

    /**
     * Get remaining SmartSearches the user can create
     */
    public function getRemainingSmartSearches(): int
    {
        $subscription = $this->activeSmartSearchSubscription();
        return $subscription?->getRemainingSearches() ?? 0;
    }

    /**
     * Check if user has any active SmartSearch subscription
     */
    public function hasActiveSmartSearchSubscription(): bool
    {
        return $this->activeSmartSearchSubscription() !== null;
    }

    /**
     * Check if user has VIP SmartSearch subscription
     */
    public function hasVipSmartSearch(): bool
    {
        return $this->activeSmartSearchSubscription(SmartSearch::TIER_VIP) !== null;
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
     * Scope: Get only customer users
     */
    public function scopeCustomers(Builder $query): Builder
    {
        return $query->where('user_type', 'customer');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer(): bool
    {
        return $this->user_type === 'customer';
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
        if (!$this->avatar) {
            return null;
        }

        if (
            str_starts_with($this->avatar, 'http://') ||
            str_starts_with($this->avatar, 'https://') ||
            str_starts_with($this->avatar, '/storage/')
        ) {
            return $this->avatar;
        }

        if (Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        if (Storage::disk('local')->exists($this->avatar)) {
            Storage::disk('public')->put(
                $this->avatar,
                Storage::disk('local')->get($this->avatar)
            );

            return Storage::disk('public')->url($this->avatar);
        }

        return Storage::disk('public')->url($this->avatar);
    }

    // =================================================================
    // SLUG GENERATION
    // =================================================================

    /**
     * Boot the model and set up model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->slug = $user->generateSlug();
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty('name') && empty($user->slug)) {
                $user->slug = $user->generateSlug();
            }
        });
    }

    /**
     * Generate a unique slug for the user
     */
    protected function generateSlug(): string
    {
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key name for model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // =================================================================
    // PROFILE COMPLETION TRACKING
    // =================================================================

    /**
     * Get profile completion steps for the user's type
     */
    public function getRequiredProfileSteps(): array
    {
        return match($this->user_type) {
            'agent' => [
                'basic_info' => 'Complete basic information',
                'professional_details' => 'Add professional details',
                'certifications' => 'Upload certifications',
                'service_areas' => 'Define service areas',
                'profile_photo' => 'Upload profile photo',
            ],
            'property_owner' => [
                'basic_info' => 'Complete basic information', 
                'contact_details' => 'Verify contact details',
                'address' => 'Add address information',
                'id_verification' => 'Upload ID verification',
                'profile_photo' => 'Upload profile photo',
            ],
            'agency_owner' => [
                'basic_info' => 'Complete basic information',
                'agency_details' => 'Add agency information',
                'business_registration' => 'Upload business documents',
                'team_setup' => 'Set up team members',
                'profile_photo' => 'Upload profile photo',
            ],
            'tenant' => [
                'basic_info' => 'Complete basic information',
                'preferences' => 'Set property preferences',
                'employment_info' => 'Add employment details',
                'references' => 'Provide references',
                'profile_photo' => 'Upload profile photo',
            ],
            default => ['basic_info' => 'Complete basic information'],
        };
    }

    /**
     * Calculate profile completion percentage
     */
    public function calculateProfileCompletion(): int
    {
        $requiredSteps = $this->getRequiredProfileSteps();
        $completedSteps = $this->profile_completion_steps ?? [];
        
        if (empty($requiredSteps)) {
            return 100;
        }

        $completedCount = count(array_intersect(array_keys($requiredSteps), $completedSteps));
        return (int) round(($completedCount / count($requiredSteps)) * 100);
    }

    /**
     * Mark a profile completion step as completed
     */
    public function markStepCompleted(string $step): void
    {
        $completedSteps = $this->profile_completion_steps ?? [];
        
        if (!in_array($step, $completedSteps)) {
            $completedSteps[] = $step;
            $this->profile_completion_steps = $completedSteps;
        }

        $this->profile_completion_percentage = $this->calculateProfileCompletion();
        
        // Check if profile is fully completed
        if ($this->profile_completion_percentage >= 100) {
            $this->profile_completed = true;
            $this->profile_completed_at = now();
        }

        $this->save();
    }

    /**
     * Check if a specific step is completed
     */
    public function isStepCompleted(string $step): bool
    {
        $completedSteps = $this->profile_completion_steps ?? [];
        return in_array($step, $completedSteps);
    }

    /**
     * Get remaining profile steps
     */
    public function getRemainingSteps(): array
    {
        $requiredSteps = $this->getRequiredProfileSteps();
        $completedSteps = $this->profile_completion_steps ?? [];
        
        return array_diff_key($requiredSteps, array_flip($completedSteps));
    }

    /**
     * Check if profile completion is required for panel access
     */
    public function requiresProfileCompletion(): bool
    {
        return !$this->profile_completed && in_array($this->user_type, [
            'agent', 'property_owner', 'agency_owner', 'tenant'
        ]);
    }

    /**
     * Initialize basic profile completion steps after registration
     */
    public function getPanelProfileUrl(): string
    {
        try {
            return match($this->user_type) {
                'agent', 'independent_agent' => route('filament.agent.auth.profile'),
                'property_owner' => route('filament.landlord.auth.profile'),
                'agency_owner' => $this->getAgencyProfileUrl(),
                'tenant' => route('filament.tenant.auth.profile'),
                'admin' => route('filament.admin.auth.profile'),
                default => route('profile'),
            };
        } catch (\Exception $e) {
            // Fallback to default profile if route doesn't exist
            return route('profile');
        }
    }

    protected function getAgencyProfileUrl(): string
    {
         $agency = $this->ownedAgencies()->first();
         return $agency ? route('filament.agency.auth.profile', ['tenant' => $agency]) : route('profile');
    }

    public function getPanelDashboardUrl(): string
    {
        try {
            return match($this->user_type) {
                'agent', 'independent_agent' => route('filament.agent.pages.dashboard'),
                'property_owner' => route('filament.landlord.pages.dashboard'),
                'agency_owner' => $this->getAgencyDashboardUrl(),
                'tenant' => route('filament.tenant.pages.dashboard'),
                'admin' => route('filament.admin.pages.dashboard'),
                default => route('dashboard'),
            };
        } catch (\Exception $e) {
            return route('dashboard');
        }
    }

    protected function getAgencyDashboardUrl(): string
    {
        $agency = $this->ownedAgencies()->first();
        // Assuming AgencyDashboard logic
        return $agency ? route('filament.agency.pages.agency-dashboard', ['tenant' => $agency]) : route('dashboard');
    }
}
