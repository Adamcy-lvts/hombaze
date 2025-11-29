# Featured Listings System - Implementation Guide

## Overview

The Featured Listings system allows property owners and agents to **pay to boost their property visibility** through prominent placement in search results, homepage features, and visual badges. This is a **pay-per-property, time-based** monetization feature.

**Implementation Priority:** #2 (Quick Win)
**Estimated Time:** 1 week
**Revenue Potential:** ₦28.8M Year 1 (₦2.4M/month recurring)

**Key Advantage:** Fields `is_featured` and `featured_until` already exist in the properties table, making this the easiest monetization feature to implement.

---

## Table of Contents

1. [Database Schema](#database-schema)
2. [Backend Implementation](#backend-implementation)
3. [Frontend Components](#frontend-components)
4. [Payment Integration](#payment-integration)
5. [Business Logic](#business-logic)
6. [Testing Strategy](#testing-strategy)
7. [Deployment Checklist](#deployment-checklist)

---

## Database Schema

### 1. Existing `properties` Table Fields

**Already Exists:**
```php
$table->boolean('is_featured')->default(false);
$table->timestamp('featured_until')->nullable();
```

**These fields are already in use!** We just need to add payment tracking and automation.

---

### 2. Create `featured_listings` Table

```php
// Migration: create_featured_listings_table.php

Schema::create('featured_listings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('property_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who paid
    $table->string('package_type'); // '24_hour', '7_day', '30_day', '90_day'
    $table->integer('duration_days'); // 1, 7, 30, 90
    $table->decimal('price_paid', 10, 2);
    $table->string('payment_method')->default('paystack');
    $table->string('payment_reference')->unique();
    $table->string('payment_status')->default('pending'); // 'pending', 'success', 'failed', 'refunded'
    $table->text('payment_metadata')->nullable(); // JSON
    $table->timestamp('featured_start')->nullable(); // When featuring began
    $table->timestamp('featured_end')->nullable(); // When featuring expires
    $table->boolean('is_active')->default(true);
    $table->boolean('auto_renewed')->default(false);
    $table->timestamp('paid_at')->nullable();
    $table->timestamp('expired_at')->nullable();
    $table->timestamp('refunded_at')->nullable();
    $table->string('refund_reason')->nullable();
    $table->timestamps();

    $table->index(['property_id', 'is_active']);
    $table->index(['featured_end', 'is_active']);
    $table->index('payment_reference');
});
```

---

### 3. Create `featured_packages` Configuration Table

```php
// Migration: create_featured_packages_table.php

Schema::create('featured_packages', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique(); // '24_hour', '7_day', '30_day', '90_day'
    $table->string('name'); // "24-Hour Boost", "7-Day Featured"
    $table->text('description')->nullable();
    $table->integer('duration_days'); // 1, 7, 30, 90
    $table->decimal('price', 10, 2);
    $table->decimal('price_per_day', 10, 2); // Calculated
    $table->integer('discount_percentage')->default(0);
    $table->json('features')->nullable(); // ['homepage_carousel', 'search_top', 'badge', 'priority_support']
    $table->string('badge_color')->default('blue'); // For UI styling
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

**Seeder Data:**

```php
// Database/Seeders/FeaturedPackageSeeder.php

DB::table('featured_packages')->insert([
    [
        'slug' => '24_hour',
        'name' => '24-Hour Boost',
        'description' => 'Get instant visibility at the top of search results for 24 hours',
        'duration_days' => 1,
        'price' => 2000,
        'price_per_day' => 2000,
        'discount_percentage' => 0,
        'features' => json_encode(['search_top', 'badge']),
        'badge_color' => 'yellow',
        'is_active' => true,
        'sort_order' => 1,
    ],
    [
        'slug' => '7_day',
        'name' => '7-Day Featured',
        'description' => 'Homepage carousel + search priority + featured badge for 7 days',
        'duration_days' => 7,
        'price' => 10000,
        'price_per_day' => 1429, // ~₦1,429/day
        'discount_percentage' => 29,
        'features' => json_encode(['homepage_carousel', 'search_top', 'badge', 'social_sharing']),
        'badge_color' => 'blue',
        'is_active' => true,
        'sort_order' => 2,
    ],
    [
        'slug' => '30_day',
        'name' => '30-Day Premium',
        'description' => 'Maximum visibility everywhere for 30 days',
        'duration_days' => 30,
        'price' => 35000,
        'price_per_day' => 1167, // ~₦1,167/day
        'discount_percentage' => 42,
        'features' => json_encode(['homepage_carousel', 'search_top', 'badge', 'social_sharing', 'email_campaigns', 'analytics']),
        'badge_color' => 'purple',
        'is_active' => true,
        'sort_order' => 3,
    ],
    [
        'slug' => '90_day',
        'name' => '90-Day Enterprise',
        'description' => 'Long-term maximum exposure + priority support',
        'duration_days' => 90,
        'price' => 100000,
        'price_per_day' => 1111, // ~₦1,111/day
        'discount_percentage' => 44,
        'features' => json_encode(['homepage_carousel', 'search_top', 'badge', 'social_sharing', 'email_campaigns', 'analytics', 'priority_support', 'dedicated_manager']),
        'badge_color' => 'gold',
        'is_active' => true,
        'sort_order' => 4,
    ],
]);
```

---

### 4. Create `featured_analytics` Table (Optional)

```php
// Migration: create_featured_analytics_table.php

Schema::create('featured_analytics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('featured_listing_id')->constrained()->onDelete('cascade');
    $table->foreignId('property_id')->constrained()->onDelete('cascade');
    $table->date('date');
    $table->integer('views')->default(0);
    $table->integer('inquiries')->default(0);
    $table->integer('viewings_scheduled')->default(0);
    $table->integer('contacts')->default(0);
    $table->timestamps();

    $table->unique(['featured_listing_id', 'date']);
    $table->index(['property_id', 'date']);
});
```

**Purpose:** Track daily performance of featured listings to show ROI to users.

---

## Backend Implementation

### 1. Models

#### FeaturedListing Model

```php
// app/Models/FeaturedListing.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedListing extends Model
{
    protected $fillable = [
        'property_id',
        'user_id',
        'package_type',
        'duration_days',
        'price_paid',
        'payment_method',
        'payment_reference',
        'payment_status',
        'payment_metadata',
        'featured_start',
        'featured_end',
        'is_active',
        'auto_renewed',
        'paid_at',
        'expired_at',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'payment_metadata' => 'array',
        'featured_start' => 'datetime',
        'featured_end' => 'datetime',
        'is_active' => 'boolean',
        'auto_renewed' => 'boolean',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(FeaturedPackage::class, 'package_type', 'slug');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('featured_end', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('is_active', true)
            ->where('featured_end', '<=', now());
    }

    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', 'success');
    }

    // Helper Methods
    public function activate(array $metadata = []): void
    {
        $start = now();
        $end = $start->copy()->addDays($this->duration_days);

        $this->update([
            'payment_status' => 'success',
            'paid_at' => now(),
            'featured_start' => $start,
            'featured_end' => $end,
            'is_active' => true,
            'payment_metadata' => $metadata,
        ]);

        // Update property
        $this->property->update([
            'is_featured' => true,
            'featured_until' => $end,
        ]);
    }

    public function expire(): void
    {
        $this->update([
            'is_active' => false,
            'expired_at' => now(),
        ]);

        // Check if property has other active featured listings
        $hasOtherActive = FeaturedListing::where('property_id', $this->property_id)
            ->active()
            ->where('id', '!=', $this->id)
            ->exists();

        if (!$hasOtherActive) {
            $this->property->update([
                'is_featured' => false,
                'featured_until' => null,
            ]);
        }
    }

    public function refund(string $reason): void
    {
        $this->update([
            'payment_status' => 'refunded',
            'refunded_at' => now(),
            'refund_reason' => $reason,
            'is_active' => false,
        ]);

        $this->property->update([
            'is_featured' => false,
            'featured_until' => null,
        ]);
    }

    public function canBeRefunded(): bool
    {
        if ($this->payment_status !== 'success' || $this->refunded_at) {
            return false;
        }

        // Refund only if featured for less than 24 hours
        if (!$this->featured_start) {
            return true; // Not yet started
        }

        return $this->featured_start->diffInHours(now()) < 24;
    }

    public function getRemainingDays(): int
    {
        if (!$this->is_active || !$this->featured_end) {
            return 0;
        }

        return max(0, now()->diffInDays($this->featured_end, false));
    }

    public function getProgressPercentage(): int
    {
        if (!$this->featured_start || !$this->featured_end) {
            return 0;
        }

        $total = $this->featured_start->diffInHours($this->featured_end);
        $elapsed = $this->featured_start->diffInHours(now());

        return min(100, (int) (($elapsed / $total) * 100));
    }
}
```

---

#### FeaturedPackage Model

```php
// app/Models/FeaturedPackage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedPackage extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'duration_days',
        'price',
        'price_per_day',
        'discount_percentage',
        'features',
        'badge_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // Helper Methods
    public function getFormattedPrice(): string
    {
        return '₦' . number_format($this->price, 0);
    }

    public function getFormattedPricePerDay(): string
    {
        return '₦' . number_format($this->price_per_day, 0);
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getSavingsText(): string
    {
        if ($this->discount_percentage === 0) {
            return '';
        }

        return "Save {$this->discount_percentage}%";
    }
}
```

---

#### Property Model Updates

```php
// app/Models/Property.php

class Property extends Model
{
    // Add relationship
    public function featuredListings()
    {
        return $this->hasMany(FeaturedListing::class);
    }

    public function activeFeaturedListing()
    {
        return $this->hasOne(FeaturedListing::class)
            ->where('is_active', true)
            ->where('featured_end', '>', now())
            ->latest('featured_end');
    }

    // Helper methods
    public function isFeatured(): bool
    {
        return $this->is_featured && $this->featured_until && $this->featured_until->isFuture();
    }

    public function getFeaturedRemainingDays(): int
    {
        if (!$this->isFeatured()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->featured_until, false));
    }

    public function canBeFeatured(): bool
    {
        // Can always buy additional featuring (they stack)
        return true;
    }
}
```

---

### 2. Services

#### FeaturedListingService

```php
// app/Services/FeaturedListingService.php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\FeaturedPackage;
use App\Models\FeaturedListing;

class FeaturedListingService
{
    /**
     * Initiate featured listing purchase
     */
    public function initiatePurchase(Property $property, string $packageSlug): FeaturedListing
    {
        $package = FeaturedPackage::where('slug', $packageSlug)
            ->where('is_active', true)
            ->firstOrFail();

        return FeaturedListing::create([
            'property_id' => $property->id,
            'user_id' => $property->user_id,
            'package_type' => $package->slug,
            'duration_days' => $package->duration_days,
            'price_paid' => $package->price,
            'payment_method' => 'paystack',
            'payment_reference' => $this->generateReference(),
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Activate featured listing after payment
     */
    public function activateFeaturedListing(FeaturedListing $featuredListing, array $metadata = []): void
    {
        $featuredListing->activate($metadata);

        // Send confirmation email
        // $featuredListing->user->notify(new FeaturedListingActivated($featuredListing));
    }

    /**
     * Expire featured listings (called by scheduler)
     */
    public function expireExpiredListings(): int
    {
        $expired = FeaturedListing::expired()->get();

        foreach ($expired as $listing) {
            $listing->expire();
        }

        return $expired->count();
    }

    /**
     * Get recommended package based on property type
     */
    public function getRecommendedPackage(Property $property): ?FeaturedPackage
    {
        // High-value properties: 30-day
        if ($property->price_total > 50000000) {
            return FeaturedPackage::where('slug', '30_day')->first();
        }

        // Medium-value: 7-day
        if ($property->price_total > 10000000) {
            return FeaturedPackage::where('slug', '7_day')->first();
        }

        // Budget: 24-hour boost
        return FeaturedPackage::where('slug', '24_hour')->first();
    }

    /**
     * Calculate ROI metrics for featured listing
     */
    public function calculateROI(FeaturedListing $featuredListing): array
    {
        $property = $featuredListing->property;

        // Get views/inquiries before and during featuring
        $beforeViews = $property->views()
            ->where('created_at', '<', $featuredListing->featured_start)
            ->count();

        $duringViews = $property->views()
            ->whereBetween('created_at', [$featuredListing->featured_start, now()])
            ->count();

        $beforeInquiries = $property->inquiries()
            ->where('created_at', '<', $featuredListing->featured_start)
            ->count();

        $duringInquiries = $property->inquiries()
            ->whereBetween('created_at', [$featuredListing->featured_start, now()])
            ->count();

        return [
            'views_increase' => $duringViews - $beforeViews,
            'inquiries_increase' => $duringInquiries - $beforeInquiries,
            'views_increase_percentage' => $beforeViews > 0 ? (($duringViews - $beforeViews) / $beforeViews * 100) : 0,
            'cost_per_view' => $duringViews > 0 ? $featuredListing->price_paid / $duringViews : 0,
            'cost_per_inquiry' => $duringInquiries > 0 ? $featuredListing->price_paid / $duringInquiries : 0,
        ];
    }

    /**
     * Generate unique payment reference
     */
    private function generateReference(): string
    {
        return 'FEATURED_' . strtoupper(uniqid());
    }
}
```

---

### 3. Commands

#### ExpireFeaturedListingsCommand

```php
// app/Console/Commands/ExpireFeaturedListingsCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FeaturedListingService;

class ExpireFeaturedListingsCommand extends Command
{
    protected $signature = 'featured:expire';
    protected $description = 'Expire featured listings that have reached their end date';

    public function __construct(
        private FeaturedListingService $featuredListingService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking for expired featured listings...');

        $count = $this->featuredListingService->expireExpiredListings();

        $this->info("Expired {$count} featured listings.");

        return Command::SUCCESS;
    }
}
```

**Schedule in** `routes/console.php`:

```php
use App\Console\Commands\ExpireFeaturedListingsCommand;

Schedule::command('featured:expire')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping();
```

---

### 4. Controllers

#### FeaturedListingController

```php
// app/Http/Controllers/FeaturedListingController.php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\FeaturedPackage;
use App\Models\FeaturedListing;
use App\Services\FeaturedListingService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class FeaturedListingController extends Controller
{
    public function __construct(
        private FeaturedListingService $featuredListingService,
        private PaystackService $paystackService
    ) {}

    /**
     * Show pricing modal for a property
     */
    public function pricing(Property $property)
    {
        $this->authorize('update', $property);

        $packages = FeaturedPackage::active()->get();
        $recommended = $this->featuredListingService->getRecommendedPackage($property);
        $activeFeatured = $property->activeFeaturedListing;

        return view('featured.pricing', compact('property', 'packages', 'recommended', 'activeFeatured'));
    }

    /**
     * Initiate featured listing purchase
     */
    public function purchase(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $request->validate([
            'package' => 'required|exists:featured_packages,slug',
        ]);

        $featuredListing = $this->featuredListingService->initiatePurchase(
            $property,
            $request->package
        );

        // Initialize Paystack payment
        $paymentData = $this->paystackService->initializePayment(
            email: auth()->user()->email,
            amount: $featuredListing->price_paid * 100,
            reference: $featuredListing->payment_reference,
            metadata: [
                'featured_listing_id' => $featuredListing->id,
                'property_id' => $property->id,
                'package_type' => $featuredListing->package_type,
            ]
        );

        return redirect($paymentData['authorization_url']);
    }

    /**
     * Payment callback
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('properties.index')
                ->with('error', 'Payment reference not found');
        }

        $verification = $this->paystackService->verifyPayment($reference);

        if ($verification['status'] === 'success') {
            $featuredListing = FeaturedListing::where('payment_reference', $reference)->firstOrFail();
            $this->featuredListingService->activateFeaturedListing($featuredListing, $verification['data']);

            return redirect()->route('properties.show', $featuredListing->property)
                ->with('success', "Your property is now featured for {$featuredListing->duration_days} days!");
        }

        return redirect()->route('properties.index')
            ->with('error', 'Payment verification failed. Please contact support.');
    }

    /**
     * Show analytics for featured listing
     */
    public function analytics(Property $property)
    {
        $this->authorize('view', $property);

        $featuredListing = $property->activeFeaturedListing ?? $property->featuredListings()->latest()->first();

        if (!$featuredListing) {
            return redirect()->route('properties.show', $property)
                ->with('error', 'This property has never been featured.');
        }

        $roi = $this->featuredListingService->calculateROI($featuredListing);

        return view('featured.analytics', compact('property', 'featuredListing', 'roi'));
    }

    /**
     * Webhook handler
     */
    public function webhook(Request $request)
    {
        if (!$this->paystackService->verifyWebhookSignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'charge.success') {
            $reference = $data['reference'];
            $featuredListing = FeaturedListing::where('payment_reference', $reference)->first();

            if ($featuredListing && $featuredListing->payment_status === 'pending') {
                $this->featuredListingService->activateFeaturedListing($featuredListing, $data);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
```

---

## Frontend Components

### 1. Feature Property Button

```blade
{{-- resources/views/components/feature-property-button.blade.php --}}

@props(['property'])

<div class="mt-4">
    @if($property->isFeatured())
        {{-- Currently Featured --}}
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold">✨ Currently Featured</p>
                    <p class="text-xs opacity-90 mt-1">
                        {{ $property->getFeaturedRemainingDays() }} days remaining
                    </p>
                </div>
                <a href="{{ route('featured.analytics', $property) }}"
                   class="bg-white text-blue-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100">
                    View Analytics
                </a>
            </div>
            <div class="mt-3 bg-white bg-opacity-20 rounded-full h-2">
                <div class="bg-white rounded-full h-2"
                     style="width: {{ $property->activeFeaturedListing?->getProgressPercentage() ?? 0 }}%"></div>
            </div>
        </div>

        {{-- Can extend or add new featuring --}}
        <div class="mt-4">
            <a href="{{ route('featured.pricing', $property) }}"
               class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
                Extend or Add More Featuring
            </a>
        </div>
    @else
        {{-- Not Featured - Show CTA --}}
        <a href="{{ route('featured.pricing', $property) }}"
           class="block text-center bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform hover:scale-105 transition">
            <span class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Feature This Property
            </span>
            <span class="text-xs opacity-90 mt-1 block">Get 10x more views - From ₦2,000</span>
        </a>
    @endif
</div>
```

---

### 2. Pricing Modal/Page

```blade
{{-- resources/views/featured/pricing.blade.php --}}

<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Property Preview --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center">
                <img src="{{ $property->featured_image_url }}" alt="{{ $property->title }}" class="w-24 h-24 object-cover rounded-lg">
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-900">{{ $property->title }}</h2>
                    <p class="text-gray-600">{{ $property->area->name }}, {{ $property->city->name }}</p>
                    <p class="text-blue-600 font-bold mt-1">{{ $property->formatted_price }}</p>
                </div>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Feature This Property</h1>
        <p class="text-gray-600 mb-8">Boost visibility and get more inquiries with premium placement</p>

        @if($activeFeatured)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <p class="text-sm text-blue-800">
                This property is currently featured until <strong>{{ $activeFeatured->featured_end->format('M d, Y') }}</strong>.
                You can extend or add additional featuring packages below.
            </p>
        </div>
        @endif

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($packages as $package)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden {{ $recommended && $recommended->id === $package->id ? 'ring-2 ring-blue-500' : 'border border-gray-200' }}">
                @if($recommended && $recommended->id === $package->id)
                <div class="bg-blue-500 text-white text-center text-xs font-bold py-2">
                    ⭐ RECOMMENDED FOR YOU
                </div>
                @endif

                <div class="p-6">
                    <div class="text-center">
                        <div class="inline-block px-3 py-1 rounded-full text-xs font-bold mb-3"
                             style="background-color: {{ $package->badge_color === 'gold' ? '#FDB022' : '' }}{{ $package->badge_color === 'blue' ? '#3B82F6' : '' }}{{ $package->badge_color === 'purple' ? '#8B5CF6' : '' }}{{ $package->badge_color === 'yellow' ? '#EAB308' : '' }}; color: white;">
                            {{ strtoupper($package->slug) }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
                        <p class="text-sm text-gray-600 mt-2">{{ $package->description }}</p>
                    </div>

                    <div class="mt-6 text-center">
                        <span class="text-4xl font-bold text-gray-900">{{ $package->getFormattedPrice() }}</span>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $package->getFormattedPricePerDay() }} per day
                        </p>
                    </div>

                    @if($package->discount_percentage > 0)
                    <div class="mt-2 text-center">
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">
                            {{ $package->getSavingsText() }} vs 24-hour rate
                        </span>
                    </div>
                    @endif

                    <ul class="mt-6 space-y-3">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Featured for {{ $package->duration_days }} {{ $package->duration_days === 1 ? 'day' : 'days' }}</span>
                        </li>
                        @foreach($package->features ?? [] as $feature)
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $feature)) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="p-6 pt-0">
                    <form action="{{ route('featured.purchase', $property) }}" method="POST">
                        @csrf
                        <input type="hidden" name="package" value="{{ $package->slug }}">
                        <button type="submit"
                                class="w-full font-bold py-3 px-4 rounded-lg transition
                                       {{ $recommended && $recommended->id === $package->id
                                          ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                          : 'bg-gray-800 hover:bg-gray-900 text-white' }}">
                            Choose {{ $package->name }}
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Benefits Section --}}
        <div class="mt-16 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Why Feature Your Property?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">10x More Views</h3>
                    <p class="text-sm text-gray-600">Featured properties get seen by 10 times more potential buyers and renters</p>
                </div>
                <div class="text-center">
                    <div class="bg-purple-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Instant Visibility</h3>
                    <p class="text-sm text-gray-600">Appear at the top of search results and homepage immediately</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Faster Sales</h3>
                    <p class="text-sm text-gray-600">Featured properties sell or rent 3x faster on average</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

### 3. Featured Badge Component

```blade
{{-- resources/views/components/featured-badge.blade.php --}}

@props(['property', 'size' => 'md'])

@if($property->isFeatured())
<div class="inline-flex items-center
            {{ $size === 'sm' ? 'px-2 py-1 text-xs' : 'px-3 py-1 text-sm' }}
            bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-full shadow-lg">
    <svg class="{{ $size === 'sm' ? 'w-3 h-3' : 'w-4 h-4' }} mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
    </svg>
    Featured
</div>
@endif
```

---

## Business Logic

### Key Rules

1. **Stacking:**
   - Multiple featured listings can be active simultaneously for same property
   - If user buys 7-day then 30-day, both run concurrently
   - Property stays featured until the longest duration expires

2. **Expiration:**
   - Automated daily cron job expires listings
   - Property `is_featured` flag removed when NO active featured listings remain
   - Email notification sent 3 days before expiration

3. **Refund Policy:**
   - Full refund if not yet activated
   - Full refund if featured <24 hours
   - Pro-rated refund if <50% duration elapsed
   - No refund after 50% duration

4. **Analytics:**
   - Track views, inquiries, viewings before vs during featuring
   - Calculate ROI (cost per view, cost per inquiry)
   - Show comparison to non-featured performance

---

## Testing Strategy

### Unit Tests

```php
// tests/Unit/FeaturedListingTest.php

class FeaturedListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_featured_listing_activates_property()
    {
        $property = Property::factory()->create(['is_featured' => false]);
        $featured = FeaturedListing::factory()->for($property)->create();

        $featured->activate();

        $this->assertTrue($property->fresh()->is_featured);
        $this->assertNotNull($property->fresh()->featured_until);
    }

    public function test_featured_listing_expires_correctly()
    {
        $property = Property::factory()->create(['is_featured' => true]);
        $featured = FeaturedListing::factory()->for($property)->create([
            'is_active' => true,
            'featured_end' => now()->subDay(),
        ]);

        $featured->expire();

        $this->assertFalse($featured->fresh()->is_active);
        $this->assertFalse($property->fresh()->is_featured);
    }

    // Add more tests...
}
```

---

## Deployment Checklist

- [ ] Run migrations
- [ ] Seed featured packages
- [ ] Test payment flow (test mode)
- [ ] Test expiration cron job
- [ ] Configure webhook URL in Paystack
- [ ] Add featured badge to property cards
- [ ] Add "Feature Property" button to property management
- [ ] Test featured sorting in search results
- [ ] Test homepage carousel
- [ ] Configure email notifications
- [ ] Set up analytics tracking
- [ ] Test refund logic
- [ ] Document for support team

---

**Implementation Status:** Ready for Development
**Estimated Timeline:** 1 week
**Test Mode Duration:** Until 2,000 active users
