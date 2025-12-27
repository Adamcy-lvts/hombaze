<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Jobs\ProcessSavedSearchMatches;
use Laravel\Scout\Searchable;

class Property extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'listing_type',
        'listing_package',
        'status',
        'price',
        'price_period',
        'service_charge',
        'legal_fee',
        'agency_fee',
        'caution_deposit',
        'bedrooms',
        'bathrooms',
        'toilets',
        'size_sqm',
        'parking_spaces',
        'year_built',
        'furnishing_status',
        'compound_type',
        'address',
        'landmark',
        'latitude',
        'longitude',
        'property_type_id',
        'property_subtype_id',
        'state_id',
        'city_id',
        'area_id',
        'owner_id',
        'agent_id',
        'agency_id',
        'meta_title',
        'meta_description',
        'video_url',
        'virtual_tour_url',
        'view_count',
        'inquiry_count',
        'favorite_count',
        'last_viewed_at',
        'is_featured',
        'is_verified',
        'is_published',
        'listing_fee_amount',
        'listing_fee_status',
        'listing_paid_at',
        'listing_expires_at',
        'featured_until',
        'verified_at',
        'published_at',
        'plot_size_id',
        'custom_plot_size',
        'custom_plot_unit',
        'price_negotiable',
        'contact_phone',
        'contact_email',
        'viewing_instructions',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'listing_fee_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'legal_fee' => 'decimal:2',
        'agency_fee' => 'decimal:2',
        'caution_deposit' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'custom_plot_size' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
        'price_negotiable' => 'boolean',
        'last_viewed_at' => 'datetime',
        'listing_paid_at' => 'datetime',
        'listing_expires_at' => 'datetime',
        'featured_until' => 'datetime',
        'verified_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public const LISTING_FEE_UNPAID = 'unpaid';
    public const LISTING_FEE_PAID = 'paid';
    public const LISTING_FEE_WAIVED = 'waived';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title);

                // Ensure slug uniqueness
                $originalSlug = $property->slug;
                $counter = 1;

                while (static::where('slug', $property->slug)->exists()) {
                    $property->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            if (empty($property->published_at) && $property->is_published) {
                $property->published_at = now();
            }
        });

        // SavedSearch matching is now handled by PropertyObserver
    }

    // Relationships

    /**
     * Get the property type
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Get the property subtype
     */
    public function propertySubtype(): BelongsTo
    {
        return $this->belongsTo(PropertySubtype::class);
    }

    /**
     * Get the state
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the area
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get the plot size
     */
    public function plotSize(): BelongsTo
    {
        return $this->belongsTo(PlotSize::class);
    }

    /**
     * Get the property owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(PropertyOwner::class, 'owner_id');
    }

    /**
     * Get the user who created the property (through PropertyOwner.user_id)
     */
    public function creator()
    {
        return $this->hasOneThrough(User::class, PropertyOwner::class, 'id', 'id', 'owner_id', 'user_id');
    }

    /**
     * Get the managing agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the managing agency
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the property features
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(PropertyFeature::class, 'property_feature_property');
    }

    /**
     * Get property inquiries
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    /**
     * Get property viewings
     */
    public function viewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class);
    }

    /**
     * Get property reviews
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get users who saved this property
     */
    public function savedByUsers(): HasMany
    {
        return $this->hasMany(SavedProperty::class);
    }

    /**
     * Get property views (analytics)
     */
    public function views(): HasMany
    {
        return $this->hasMany(PropertyView::class);
    }

    // Helper Methods

    /**
     * Check if property is saved by a specific user
     */
    public function isSavedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->savedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if property is favorited by current user (alias for isSavedBy)
     */
    public function isFavoritedBy(?User $user): bool
    {
        return $this->isSavedBy($user);
    }

    // Scopes

    /**
     * Scope for published properties
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->whereIn('listing_fee_status', [self::LISTING_FEE_PAID, self::LISTING_FEE_WAIVED]);
    }

    /**
     * Scope for available properties
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for featured properties
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->whereIn('listing_fee_status', [self::LISTING_FEE_PAID, self::LISTING_FEE_WAIVED])
            ->where(function ($q) {
                $q->whereNull('featured_until')
                    ->orWhere('featured_until', '>', now());
            });
    }

    /**
     * Scope for verified properties
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for rent listings
     */
    public function scopeForRent($query)
    {
        return $query->where('listing_type', 'rent');
    }

    /**
     * Scope for sale listings
     */
    public function scopeForSale($query)
    {
        return $query->where('listing_type', 'sale');
    }

    /**
     * Scope for shortlet listings
     */
    public function scopeForShortlet($query)
    {
        return $query->where('listing_type', 'shortlet');
    }

    // Scout Searchable Methods

    /**
     * Get the indexable data array for the model (Meilisearch).
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'address' => $this->address,
            'landmark' => $this->landmark,

            // Relationships (denormalized for search)
            'city_name' => $this->city?->name,
            'state_name' => $this->state?->name,
            'area_name' => $this->area?->name,
            'property_type_name' => $this->propertyType?->name,
            'property_subtype_name' => $this->propertySubtype?->name,

            // Filterable attributes
            'listing_type' => $this->listing_type,
            'property_type_id' => $this->property_type_id,
            'property_subtype_id' => $this->property_subtype_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'price' => (float) $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'furnishing_status' => $this->furnishing_status,

            // Featured & ranking
            'is_featured' => $this->is_featured,
            'is_featured_active' => $this->isFeaturedActive(),
            'featured_until' => $this->featured_until?->timestamp,
            'is_verified' => $this->is_verified,
            'view_count' => $this->view_count ?? 0,
            'created_at' => $this->created_at?->timestamp,

            // Geo-search (for "properties near me")
            '_geo' => $this->latitude && $this->longitude ? [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ] : null,
        ];
    }

    /**
     * Determine if the model should be searchable.
     * Only index published and available properties.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->is_published
            && $this->status === 'available'
            && in_array($this->listing_fee_status, [self::LISTING_FEE_PAID, self::LISTING_FEE_WAIVED]);
    }

    // Accessors & Mutators

    /**
     * Get the formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price, 0);
    }

    public static function applyListingPackageData(array $data, ?self $property = null): array
    {
        if (empty($data['listing_package'])) {
            $data['listing_package'] = $property?->listing_package ?? 'basic';
        }

        $data['listing_fee_amount'] = $data['listing_fee_amount'] ?? 0;
        $data['listing_fee_status'] = $data['listing_fee_status'] ?? self::LISTING_FEE_UNPAID;
        $data['listing_paid_at'] = $data['listing_paid_at'] ?? null;

        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    /**
     * Get the price with period
     */
    public function getPriceWithPeriodAttribute(): string
    {
        $formatted = $this->formatted_price;

        if ($this->price_period) {
            $periods = [
                'per_month' => '/month',
                'per_year' => '/year',
                'per_night' => '/night',
                'total' => ''
            ];

            $formatted .= $periods[$this->price_period] ?? '';
        }

        return $formatted;
    }

    /**
     * Get the full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->area?->name,
            $this->city?->name,
            $this->state?->name,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get property summary for listings
     */
    public function getSummaryAttribute(): string
    {
        $parts = [];

        if ($this->bedrooms > 0) {
            $parts[] = $this->bedrooms . ' bed' . ($this->bedrooms > 1 ? 's' : '');
        }

        if ($this->bathrooms > 0) {
            $parts[] = $this->bathrooms . ' bath' . ($this->bathrooms > 1 ? 's' : '');
        }

        if ($this->size_sqm) {
            $parts[] = number_format($this->size_sqm) . ' sqm';
        }

        return implode(' • ', $parts);
    }

    // Helper Methods

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);
    }

    /**
     * Increment inquiry count
     */
    public function incrementInquiryCount(): void
    {
        $this->increment('inquiry_count');
    }

    /**
     * Increment favorite count
     */
    public function incrementFavoriteCount(): void
    {
        $this->increment('favorite_count');
    }

    /**
     * Decrement favorite count
     */
    public function decrementFavoriteCount(): void
    {
        $this->decrement('favorite_count');
    }

    /**
     * Check if property is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_published;
    }

    /**
     * Check if property is featured and active
     */
    public function isFeaturedActive(): bool
    {
        if (!$this->is_featured) {
            return false;
        }

        if ($this->featured_until) {
            return $this->featured_until->isFuture();
        }

        return true;
    }

    /**
     * Get the route key name for model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Define media collections for property
     */
    public function registerMediaCollections(): void
    {
        $resolutionConfig = getOptimalImageResolution();

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes($resolutionConfig['formats'])
            ->useDisk('public');

        $this->addMediaCollection('featured')
            ->acceptsMimeTypes($resolutionConfig['formats'])
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('floor_plans')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
            ->useDisk('public');

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
            ->useDisk('public');

        $this->addMediaCollection('videos')
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/ogg'])
            ->useDisk('public');
    }

    /**
     * Define media conversions for property images
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $resolutionConfig = getOptimalImageResolution();

        // High-quality web display - based on recommended resolution
        $this
            ->addMediaConversion('web')
            ->width($resolutionConfig['recommended_width'])
            ->height($resolutionConfig['recommended_height'])
            ->quality(90)
            ->fit(Fit::Max, $resolutionConfig['recommended_width'], $resolutionConfig['recommended_height'])
            ->performOnCollections('gallery', 'featured')
            ->nonQueued()
            ->optimize()
            ->sharpen(10);

        // Preview conversion - optimized 3:2 aspect ratio
        $this
            ->addMediaConversion('preview')
            ->width(600)
            ->height(400)
            ->quality(85)
            ->fit(Fit::Max, 600, 400)
            ->performOnCollections('gallery', 'featured')
            ->nonQueued()
            ->optimize()
            ->sharpen(10);

        // Thumbnail conversion - maintains 3:2 aspect ratio
        $this
            ->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->quality(80)
            ->fit(Fit::Max, 300, 200)
            ->performOnCollections('gallery', 'featured')
            ->nonQueued()
            ->optimize()
            ->sharpen(10);

        // Square thumbnail for grid displays (1:1 aspect ratio)
        $this
            ->addMediaConversion('square')
            ->width(200)
            ->height(200)
            ->quality(80)
            ->fit(Fit::Crop, 200, 200)
            ->performOnCollections('gallery', 'featured')
            ->nonQueued()
            ->optimize()
            ->sharpen(10);

        // Small thumbnail for lists and cards
        $this
            ->addMediaConversion('small')
            ->width(150)
            ->height(100)
            ->quality(75)
            ->fit(Fit::Crop, 150, 100)
            ->performOnCollections('gallery', 'featured')
            ->nonQueued()
            ->optimize()
            ->sharpen(5);
    }

    /**
     * Get featured image URL
     */
    public function getFeaturedImageUrl(string $conversion = ''): string
    {
        $media = $this->getFirstMedia('featured');

        if (!$media) {
            return asset('images/property-placeholder.svg');
        }

        // If conversion is requested, check if it exists
        if ($conversion) {
            try {
                return $media->getUrl($conversion);
            } catch (Exception $e) {
                // If conversion doesn't exist, return original
                return $media->getUrl();
            }
        }

        return $media->getUrl();
    }

    /**
     * Get gallery images
     */
    public function getGalleryImages(string $conversion = '')
    {
        return $this->getMedia('gallery')->map(function (Media $media) use ($conversion) {
            $url = $media->getUrl();
            
            // If conversion is requested, try to get it
            if ($conversion) {
                try {
                    $url = $media->getUrl($conversion);
                } catch (Exception $e) {
                    // If conversion doesn't exist, use original
                    $url = $media->getUrl();
                }
            }
            
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $url,
                'alt' => $media->getCustomProperty('alt', $this->title),
            ];
        });
    }

    /**
     * Get floor plan images
     */
    public function getFloorPlans()
    {
        return $this->getMedia('floor_plans')->map(function (Media $media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'type' => $media->mime_type,
                'size' => $media->size,
            ];
        });
    }

    /**
     * Get property documents
     */
    public function getDocuments()
    {
        return $this->getMedia('documents')->map(function (Media $media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'type' => $media->mime_type,
                'size' => $media->human_readable_size,
            ];
        });
    }

    /**
     * Get property videos
     */
    public function getVideos()
    {
        return $this->getMedia('videos')->map(function (Media $media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'type' => $media->mime_type,
                'duration' => $media->getCustomProperty('duration'),
            ];
        });
    }

    /**
     * Check if property has gallery images
     */
    public function hasGallery(): bool
    {
        return $this->getMedia('gallery')->isNotEmpty();
    }

    /**
     * Check if property has floor plans
     */
    public function hasFloorPlans(): bool
    {
        return $this->getMedia('floor_plans')->isNotEmpty();
    }

    /**
     * Check if property has documents
     */
    public function hasDocuments(): bool
    {
        return $this->getMedia('documents')->isNotEmpty();
    }

    /**
     * Get total media count
     */
    public function getTotalMediaCount(): int
    {
        return $this->getMedia()->count();
    }

    /**
     * Get compound type options
     */
    public static function getCompoundTypeOptions(): array
    {
        return [
            'single_house' => 'Single House/Standalone',
            'duplex_compound' => '2 Units in Compound',
            'triplex_compound' => '3 Units in Compound',
            'quad_compound' => '4 Units in Compound',
            'small_estate' => 'Small Estate (5-10 units)',
            'medium_estate' => 'Medium Estate (11-20 units)',
            'large_estate' => 'Large Estate (21+ units)',
            'apartment_complex' => 'Apartment Complex',
            'mini_estate' => 'Mini Estate (2-4 blocks)',
            'gated_community' => 'Gated Community/Estate',
        ];
    }

    /**
     * Get compound type label
     */
    public function getCompoundTypeLabel(): string
    {
        if (!$this->compound_type) {
            return 'Not specified';
        }

        $options = self::getCompoundTypeOptions();
        return $options[$this->compound_type] ?? ucfirst(str_replace('_', ' ', $this->compound_type));
    }

    /**
     * Get compound type attribute (accessor)
     */
    public function getCompoundTypeLabelAttribute(): string
    {
        return $this->getCompoundTypeLabel();
    }

    /**
     * Check if property type requires bedroom/bathroom fields
     */
    public function requiresResidentialFields(): bool
    {
        if (!$this->propertyType) {
            return false;
        }

        $residentialTypes = ['apartment', 'house'];
        return in_array($this->propertyType->slug, $residentialTypes);
    }

    /**
     * Check if property type is commercial/non-residential
     */
    public function isCommercialProperty(): bool
    {
        if (!$this->propertyType) {
            return false;
        }

        $commercialTypes = [
            'commercial', 'office-space', 'shop', 'warehouse', 'factory',
            'event-center', 'restaurant', 'hotel', 'shopping-mall',
            'gas-station', 'hospital-clinic', 'school'
        ];

        return in_array($this->propertyType->slug, $commercialTypes);
    }

    /**
     * Check if property type is land
     */
    public function isLandProperty(): bool
    {
        if (!$this->propertyType) {
            return false;
        }

        return $this->propertyType->slug === 'land';
    }

    /**
     * Get validation rules based on property type
     */
    public static function getValidationRulesForType(?string $propertyTypeSlug = null): array
    {
        $baseRules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'listing_type' => 'required|in:sale,rent,lease,shortlet',
            'price' => 'required|numeric|min:0',
            'address' => 'required|string',
            'property_type_id' => 'required|exists:property_types,id',
            'property_subtype_id' => 'required|exists:property_subtypes,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'owner_id' => 'required|exists:users,id',
        ];

        // Add residential-specific rules
        if ($propertyTypeSlug && in_array($propertyTypeSlug, ['apartment', 'house'])) {
            $baseRules['bedrooms'] = 'required|integer|min:0';
            $baseRules['bathrooms'] = 'required|integer|min:0';
            $baseRules['furnishing_status'] = 'required|in:furnished,semi_furnished,unfurnished';
        }

        return $baseRules;
    }

    /**
     * Get required fields based on property type
     */
    public static function getRequiredFieldsForType(?string $propertyTypeSlug = null): array
    {
        $baseFields = [
            'title', 'description', 'listing_type', 'price', 'address',
            'property_type_id', 'property_subtype_id', 'state_id', 'city_id', 'owner_id'
        ];

        // Add residential-specific fields
        if ($propertyTypeSlug && in_array($propertyTypeSlug, ['apartment', 'house'])) {
            $baseFields = array_merge($baseFields, ['bedrooms', 'bathrooms', 'furnishing_status']);
        }

        return $baseFields;
    }

    /**
     * Get hidden fields based on property type
     */
    public static function getHiddenFieldsForType(?string $propertyTypeSlug = null): array
    {
        $hiddenFields = [];

        // Hide residential fields for non-residential properties
        if ($propertyTypeSlug && !in_array($propertyTypeSlug, ['apartment', 'house'])) {
            $hiddenFields = array_merge($hiddenFields, ['bedrooms', 'bathrooms', 'furnishing_status']);
        }

        // Hide certain fields for land properties
        if ($propertyTypeSlug === 'land') {
            $hiddenFields = array_merge($hiddenFields, [
                'bedrooms', 'bathrooms', 'furnishing_status', 'parking_spaces', 'year_built'
            ]);
        }

        return $hiddenFields;
    }
}
