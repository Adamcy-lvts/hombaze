<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Property extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'listing_type',
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
        'featured_until',
        'verified_at',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'legal_fee' => 'decimal:2',
        'agency_fee' => 'decimal:2',
        'caution_deposit' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_published' => 'boolean',
        'last_viewed_at' => 'datetime',
        'featured_until' => 'datetime',
        'verified_at' => 'datetime',
        'published_at' => 'datetime',
    ];

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
     * Get the property owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(PropertyOwner::class, 'owner_id');
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

    // Scopes

    /**
     * Scope for published properties
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at');
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

    // Accessors & Mutators

    /**
     * Get the formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price, 0);
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
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');

        $this->addMediaCollection('featured')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
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
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('gallery', 'featured');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('gallery', 'featured');

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(900)
            ->sharpen(10)
            ->performOnCollections('gallery', 'featured');
    }

    /**
     * Get featured image URL
     */
    public function getFeaturedImageUrl(string $conversion = ''): string
    {
        $media = $this->getFirstMedia('featured');
        
        if (!$media) {
            return asset('images/property-placeholder.jpg');
        }

        return $conversion ? $media->getUrl($conversion) : $media->getUrl();
    }

    /**
     * Get gallery images
     */
    public function getGalleryImages(string $conversion = '')
    {
        return $this->getMedia('gallery')->map(function (Media $media) use ($conversion) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $conversion ? $media->getUrl($conversion) : $media->getUrl(),
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
}
