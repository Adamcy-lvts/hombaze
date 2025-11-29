# Saved Search Unlocks System - Implementation Guide

## Overview

The Saved Search Unlocks system monetizes the sophisticated saved search feature by showing **preview-only match notifications** to free users and requiring payment to see full property details. Users can pay per property unlock or buy unlimited access once.

**Implementation Priority:** #3 (High Engagement Feature)
**Estimated Time:** 2 weeks
**Revenue Potential:** ₦8M Year 1 (one-time from active searchers)

**Key Strategy:** NOT a subscription, but a **pay-per-match or one-time unlimited access** model that respects the uncertain timeline of property searching.

---

## Table of Contents

1. [Database Schema](#database-schema)
2. [Backend Implementation](#backend-implementation)
3. [Frontend Components](#frontend-components)
4. [Notification Changes](#notification-changes)
5. [Payment Integration](#payment-integration)
6. [Business Logic](#business-logic)
7. [Testing Strategy](#testing-strategy)
8. [Deployment Checklist](#deployment-checklist)

---

## Database Schema

### 1. Update `users` Table

```php
// Migration: add_search_unlock_fields_to_users_table.php

Schema::table('users', function (Blueprint $table) {
    $table->boolean('has_unlimited_match_access')->default(false)->after('property_credits');
    $table->timestamp('unlimited_access_purchased_at')->nullable()->after('has_unlimited_match_access');
    $table->decimal('unlimited_access_price_paid', 10, 2)->nullable()->after('unlimited_access_purchased_at');
});
```

**Purpose:** Track users who bought unlimited access (one-time ₦30,000 payment).

---

### 2. Create `unlocked_matches` Table

```php
// Migration: create_unlocked_matches_table.php

Schema::create('unlocked_matches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('property_id')->constrained()->onDelete('cascade');
    $table->foreignId('saved_search_id')->nullable()->constrained()->onDelete('set null');
    $table->string('unlock_method'); // 'single', 'bulk', 'unlimited', 'free_preview'
    $table->decimal('price_paid', 10, 2)->default(0); // 0 for unlimited access users
    $table->string('payment_reference')->nullable();
    $table->timestamps();

    $table->unique(['user_id', 'property_id']); // Prevent duplicate unlocks
    $table->index(['user_id', 'created_at']);
    $table->index('property_id');
});
```

**Purpose:** Track which users unlocked which properties and how they unlocked them.

---

### 3. Create `match_unlock_purchases` Table

```php
// Migration: create_match_unlock_purchases_table.php

Schema::create('match_unlock_purchases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('package_type'); // 'single', 'bulk_5', 'bulk_10', 'bulk_20', 'unlimited'
    $table->integer('unlock_credits')->nullable(); // 1, 5, 10, 20, or null for unlimited
    $table->decimal('price_paid', 10, 2);
    $table->string('payment_method')->default('paystack');
    $table->string('payment_reference')->unique();
    $table->string('payment_status')->default('pending'); // 'pending', 'success', 'failed', 'refunded'
    $table->text('payment_metadata')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->timestamp('refunded_at')->nullable();
    $table->string('refund_reason')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'payment_status']);
    $table->index('payment_reference');
});
```

**Purpose:** Track purchases of unlock credits and unlimited access.

---

### 4. Update `users` Table for Credits (Alternative to Unlimited)

```php
// Migration: add_unlock_credits_to_users_table.php

Schema::table('users', function (Blueprint $table) {
    $table->integer('unlock_credits')->default(0)->after('has_unlimited_match_access');
    // Users can have credits OR unlimited access (not both)
});
```

**Purpose:** Alternative pricing model - users can buy credits to unlock properties.

---

### 5. Create `unlock_packages` Configuration Table

```php
// Migration: create_unlock_packages_table.php

Schema::create('unlock_packages', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique(); // 'single', 'bulk_5', 'bulk_10', 'bulk_20', 'unlimited'
    $table->string('name'); // "Single Unlock", "5-Property Pack", "Unlimited Access"
    $table->text('description')->nullable();
    $table->integer('unlock_credits')->nullable(); // 1, 5, 10, 20, or null for unlimited
    $table->decimal('price', 10, 2);
    $table->decimal('price_per_unlock', 10, 2)->nullable(); // Calculated (null for unlimited)
    $table->integer('discount_percentage')->default(0);
    $table->boolean('is_unlimited')->default(false);
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

**Seeder Data:**

```php
// Database/Seeders/UnlockPackageSeeder.php

DB::table('unlock_packages')->insert([
    [
        'slug' => 'single',
        'name' => 'Single Property',
        'description' => 'Unlock 1 property to see full details',
        'unlock_credits' => 1,
        'price' => 2000,
        'price_per_unlock' => 2000,
        'discount_percentage' => 0,
        'is_unlimited' => false,
        'is_active' => true,
        'sort_order' => 1,
    ],
    [
        'slug' => 'bulk_5',
        'name' => '5-Property Pack',
        'description' => 'Unlock 5 properties - Save 20%',
        'unlock_credits' => 5,
        'price' => 8000,
        'price_per_unlock' => 1600,
        'discount_percentage' => 20,
        'is_unlimited' => false,
        'is_active' => true,
        'sort_order' => 2,
    ],
    [
        'slug' => 'bulk_10',
        'name' => '10-Property Pack',
        'description' => 'Unlock 10 properties - Save 25%',
        'unlock_credits' => 10,
        'price' => 15000,
        'price_per_unlock' => 1500,
        'discount_percentage' => 25,
        'is_unlimited' => false,
        'is_active' => true,
        'sort_order' => 3,
    ],
    [
        'slug' => 'bulk_20',
        'name' => '20-Property Pack',
        'description' => 'Unlock 20 properties - Save 37%',
        'unlock_credits' => 20,
        'price' => 25000,
        'price_per_unlock' => 1250,
        'discount_percentage' => 37,
        'is_unlimited' => false,
        'is_active' => true,
        'sort_order' => 4,
    ],
    [
        'slug' => 'unlimited',
        'name' => 'Unlimited Access',
        'description' => 'Unlock ALL matches forever - Best value',
        'unlock_credits' => null,
        'price' => 30000,
        'price_per_unlock' => null,
        'discount_percentage' => 0,
        'is_unlimited' => true,
        'is_active' => true,
        'sort_order' => 5,
    ],
]);
```

---

## Backend Implementation

### 1. Models

#### User Model Updates

```php
// app/Models/User.php

class User extends Authenticatable
{
    protected $fillable = [
        // ... existing fields
        'has_unlimited_match_access',
        'unlimited_access_purchased_at',
        'unlimited_access_price_paid',
        'unlock_credits',
    ];

    protected $casts = [
        'has_unlimited_match_access' => 'boolean',
        'unlimited_access_purchased_at' => 'datetime',
        'unlimited_access_price_paid' => 'decimal:2',
    ];

    // Relationships
    public function unlockedMatches()
    {
        return $this->hasMany(UnlockedMatch::class);
    }

    public function matchUnlockPurchases()
    {
        return $this->hasMany(MatchUnlockPurchase::class);
    }

    // Helper Methods
    public function hasUnlockedProperty(int $propertyId): bool
    {
        if ($this->has_unlimited_match_access) {
            return true;
        }

        return $this->unlockedMatches()
            ->where('property_id', $propertyId)
            ->exists();
    }

    public function canUnlockProperty(): bool
    {
        return $this->has_unlimited_match_access || $this->unlock_credits > 0;
    }

    public function unlockProperty(int $propertyId, string $method = 'single', float $pricePaid = 0, ?string $paymentReference = null, ?int $savedSearchId = null): void
    {
        // Check if already unlocked
        if ($this->hasUnlockedProperty($propertyId)) {
            return;
        }

        // Create unlock record
        UnlockedMatch::create([
            'user_id' => $this->id,
            'property_id' => $propertyId,
            'saved_search_id' => $savedSearchId,
            'unlock_method' => $method,
            'price_paid' => $pricePaid,
            'payment_reference' => $paymentReference,
        ]);

        // Deduct credit if using credits (not unlimited)
        if (!$this->has_unlimited_match_access && $this->unlock_credits > 0) {
            $this->decrement('unlock_credits');
        }
    }

    public function grantUnlimitedAccess(float $pricePaid, ?string $paymentReference = null): void
    {
        $this->update([
            'has_unlimited_match_access' => true,
            'unlimited_access_purchased_at' => now(),
            'unlimited_access_price_paid' => $pricePaid,
        ]);
    }

    public function addUnlockCredits(int $amount): void
    {
        $this->increment('unlock_credits', $amount);
    }

    public function getUnlockedPropertiesCount(): int
    {
        return $this->unlockedMatches()->count();
    }
}
```

---

#### UnlockedMatch Model

```php
// app/Models/UnlockedMatch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnlockedMatch extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'saved_search_id',
        'unlock_method',
        'price_paid',
        'payment_reference',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function savedSearch(): BelongsTo
    {
        return $this->belongsTo(SavedSearch::class);
    }

    // Scopes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
```

---

#### MatchUnlockPurchase Model

```php
// app/Models/MatchUnlockPurchase.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchUnlockPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'package_type',
        'unlock_credits',
        'price_paid',
        'payment_method',
        'payment_reference',
        'payment_status',
        'payment_metadata',
        'paid_at',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'payment_metadata' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(UnlockPackage::class, 'package_type', 'slug');
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', 'success');
    }

    // Helper Methods
    public function markAsPaid(array $metadata = []): void
    {
        $this->update([
            'payment_status' => 'success',
            'paid_at' => now(),
            'payment_metadata' => $metadata,
        ]);

        $package = $this->package;

        if ($package->is_unlimited) {
            // Grant unlimited access
            $this->user->grantUnlimitedAccess($this->price_paid, $this->payment_reference);
        } else {
            // Add credits
            $this->user->addUnlockCredits($this->unlock_credits);
        }
    }

    public function canBeRefunded(): bool
    {
        if ($this->payment_status !== 'success' || $this->refunded_at) {
            return false;
        }

        // For unlimited access: refund if <3 properties unlocked AND <7 days
        if ($this->package->is_unlimited) {
            $unlockedCount = $this->user->getUnlockedPropertiesCount();
            $daysSincePurchase = $this->paid_at->diffInDays(now());

            return $unlockedCount < 3 && $daysSincePurchase <= 7;
        }

        // For credits: no refunds (digital goods immediately consumed)
        return false;
    }
}
```

---

#### UnlockPackage Model

```php
// app/Models/UnlockPackage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlockPackage extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'unlock_credits',
        'price',
        'price_per_unlock',
        'discount_percentage',
        'is_unlimited',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_per_unlock' => 'decimal:2',
        'is_unlimited' => 'boolean',
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

    public function getFormattedPricePerUnlock(): string
    {
        if ($this->is_unlimited) {
            return 'Unlimited';
        }

        return '₦' . number_format($this->price_per_unlock, 0);
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

### 2. Services

#### MatchUnlockService

```php
// app/Services/MatchUnlockService.php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\UnlockPackage;
use App\Models\MatchUnlockPurchase;

class MatchUnlockService
{
    /**
     * Initiate unlock purchase
     */
    public function initiatePurchase(User $user, string $packageSlug): MatchUnlockPurchase
    {
        $package = UnlockPackage::where('slug', $packageSlug)
            ->where('is_active', true)
            ->firstOrFail();

        return MatchUnlockPurchase::create([
            'user_id' => $user->id,
            'package_type' => $package->slug,
            'unlock_credits' => $package->unlock_credits,
            'price_paid' => $package->price,
            'payment_method' => 'paystack',
            'payment_reference' => $this->generateReference(),
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Unlock a specific property using credits
     */
    public function unlockProperty(User $user, Property $property, ?int $savedSearchId = null): bool
    {
        // Check if user can unlock
        if ($user->hasUnlockedProperty($property->id)) {
            return true; // Already unlocked
        }

        if (!$user->canUnlockProperty()) {
            return false; // No credits or unlimited access
        }

        $user->unlockProperty(
            propertyId: $property->id,
            method: $user->has_unlimited_match_access ? 'unlimited' : 'credit',
            pricePaid: $user->has_unlimited_match_access ? 0 : 2000, // Assuming ₦2K per unlock
            savedSearchId: $savedSearchId
        );

        return true;
    }

    /**
     * Bulk unlock properties
     */
    public function bulkUnlockProperties(User $user, array $propertyIds, ?int $savedSearchId = null): int
    {
        $unlockedCount = 0;

        foreach ($propertyIds as $propertyId) {
            $property = Property::find($propertyId);

            if ($property && $this->unlockProperty($user, $property, $savedSearchId)) {
                $unlockedCount++;
            }
        }

        return $unlockedCount;
    }

    /**
     * Generate property preview data (for free tier)
     */
    public function generatePreview(Property $property): array
    {
        return [
            'id' => $property->id,
            'title' => $property->title,
            'listing_type' => $property->listing_type,
            'price' => $property->price_total,
            'price_period' => $property->price_period,
            'formatted_price' => $property->formatted_price,
            'area_name' => $property->area->name ?? 'N/A',
            'city_name' => $property->city->name ?? 'N/A',
            'state_name' => $property->state->name ?? 'N/A',
            'property_type' => $property->propertyType->name ?? 'N/A',
            'property_subtype' => $property->propertySubtype->name ?? 'N/A',
            'bedrooms' => $property->bedrooms,
            'bathrooms' => $property->bathrooms,
            'slug' => $property->slug,
            'featured_image_thumbnail' => $property->getFirstMediaUrl('featured', 'thumb'), // Blurred version
            // HIDE these from preview:
            // 'address' => LOCKED
            // 'full_description' => LOCKED
            // 'gallery' => LOCKED
            // 'agent_contact' => LOCKED
        ];
    }

    /**
     * Get full property details (for unlocked properties)
     */
    public function getFullDetails(Property $property): array
    {
        return $property->toArray(); // Full property data
    }

    /**
     * Check if user should see preview or full details
     */
    public function getPropertyDataForUser(User $user, Property $property): array
    {
        if ($user->hasUnlockedProperty($property->id)) {
            return $this->getFullDetails($property);
        }

        return $this->generatePreview($property);
    }

    /**
     * Generate unique payment reference
     */
    private function generateReference(): string
    {
        return 'UNLOCK_' . strtoupper(uniqid());
    }
}
```

---

### 3. Middleware

#### CheckPropertyAccess Middleware

```php
// app/Http/Middleware/CheckPropertyAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Services\MatchUnlockService;

class CheckPropertyAccess
{
    public function __construct(
        private MatchUnlockService $matchUnlockService
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $property = $request->route('property');

        if (!$property instanceof Property) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user) {
            // Guest users see public preview
            return $next($request);
        }

        // Inject access level into request
        $request->merge([
            'has_full_access' => $user->hasUnlockedProperty($property->id),
            'property_data' => $this->matchUnlockService->getPropertyDataForUser($user, $property),
        ]);

        return $next($request);
    }
}
```

---

### 4. Notification Changes

#### SavedSearchMatch Notification Updates

```php
// app/Notifications/SavedSearchMatch.php

public function toMail($notifiable)
{
    $matchUnlockService = app(MatchUnlockService::class);
    $properties = $this->properties;
    $savedSearch = $this->savedSearch;

    // Check if user has unlimited access
    $hasUnlimitedAccess = $notifiable->has_unlimited_match_access;

    $propertyData = $properties->map(function ($property) use ($notifiable, $matchUnlockService, $hasUnlimitedAccess) {
        if ($hasUnlimitedAccess || $notifiable->hasUnlockedProperty($property->id)) {
            // Show full details
            return [
                'property' => $property,
                'is_locked' => false,
                'unlock_url' => null,
            ];
        }

        // Show preview only
        return [
            'property' => $matchUnlockService->generatePreview($property),
            'is_locked' => true,
            'unlock_url' => route('unlocks.property', ['property' => $property->id, 'search' => $savedSearch->id]),
        ];
    });

    return (new MailMessage)
        ->subject("🏠 {$properties->count()} New " . Str::plural('Property', $properties->count()) . " Match Your Search!")
        ->greeting("Hi {$notifiable->name},")
        ->line("Great news! We found {$properties->count()} " . Str::plural('property', $properties->count()) . " that match your saved search: \"{$savedSearch->name}\"")
        ->view('emails.saved-search-match', [
            'properties' => $propertyData,
            'savedSearch' => $savedSearch,
            'hasUnlimitedAccess' => $hasUnlimitedAccess,
            'unlockPackages' => UnlockPackage::active()->get(),
        ]);
}
```

---

### 5. Controllers

#### MatchUnlockController

```php
// app/Http/Controllers/MatchUnlockController.php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\UnlockPackage;
use App\Models\MatchUnlockPurchase;
use App\Services\MatchUnlockService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class MatchUnlockController extends Controller
{
    public function __construct(
        private MatchUnlockService $matchUnlockService,
        private PaystackService $paystackService
    ) {}

    /**
     * Show unlock pricing page
     */
    public function index()
    {
        $user = auth()->user();
        $packages = UnlockPackage::active()->get();

        return view('unlocks.index', [
            'packages' => $packages,
            'hasUnlimitedAccess' => $user->has_unlimited_match_access,
            'unlockCredits' => $user->unlock_credits,
            'unlockedCount' => $user->getUnlockedPropertiesCount(),
        ]);
    }

    /**
     * Show property unlock modal
     */
    public function showProperty(Property $property, Request $request)
    {
        $user = auth()->user();
        $savedSearchId = $request->query('search');

        if ($user->hasUnlockedProperty($property->id)) {
            return redirect()->route('properties.show', $property);
        }

        $preview = $this->matchUnlockService->generatePreview($property);
        $packages = UnlockPackage::active()->get();

        return view('unlocks.property', compact('property', 'preview', 'packages', 'savedSearchId'));
    }

    /**
     * Unlock single property (using credits)
     */
    public function unlockProperty(Property $property, Request $request)
    {
        $user = auth()->user();
        $savedSearchId = $request->input('saved_search_id');

        if (!$user->canUnlockProperty()) {
            return redirect()->route('unlocks.index')
                ->with('error', 'You need to buy unlock credits or unlimited access first.');
        }

        if ($this->matchUnlockService->unlockProperty($user, $property, $savedSearchId)) {
            return redirect()->route('properties.show', $property)
                ->with('success', 'Property unlocked! You can now see full details.');
        }

        return back()->with('error', 'Failed to unlock property.');
    }

    /**
     * Purchase unlock package
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'package' => 'required|exists:unlock_packages,slug',
        ]);

        $user = auth()->user();
        $purchase = $this->matchUnlockService->initiatePurchase($user, $request->package);

        // Initialize Paystack payment
        $paymentData = $this->paystackService->initializePayment(
            email: $user->email,
            amount: $purchase->price_paid * 100,
            reference: $purchase->payment_reference,
            metadata: [
                'purchase_id' => $purchase->id,
                'user_id' => $user->id,
                'package_type' => $purchase->package_type,
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
            return redirect()->route('unlocks.index')
                ->with('error', 'Payment reference not found');
        }

        $verification = $this->paystackService->verifyPayment($reference);

        if ($verification['status'] === 'success') {
            $purchase = MatchUnlockPurchase::where('payment_reference', $reference)->firstOrFail();
            $purchase->markAsPaid($verification['data']);

            $message = $purchase->package->is_unlimited
                ? 'Unlimited access activated! You can now see all property matches.'
                : "{$purchase->unlock_credits} unlock credits added to your account!";

            return redirect()->route('customer.searches')
                ->with('success', $message);
        }

        return redirect()->route('unlocks.index')
            ->with('error', 'Payment verification failed. Please contact support.');
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
            $purchase = MatchUnlockPurchase::where('payment_reference', $reference)->first();

            if ($purchase && $purchase->payment_status === 'pending') {
                $purchase->markAsPaid($data);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
```

---

## Frontend Components

### 1. Unlock Pricing Page

```blade
{{-- resources/views/unlocks/index.blade.php --}}

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900">Unlock Property Matches</h1>
            <p class="mt-4 text-xl text-gray-600">
                See full details of properties that match your searches
            </p>
        </div>

        {{-- Current Status --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Unlock Credits</p>
                <p class="text-3xl font-bold text-blue-600">{{ $unlockCredits }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Properties Unlocked</p>
                <p class="text-3xl font-bold text-gray-900">{{ $unlockedCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                @if($hasUnlimitedAccess)
                <p class="text-sm text-green-600 font-medium">Status</p>
                <p class="text-2xl font-bold text-green-900">✓ Unlimited Access</p>
                @else
                <p class="text-sm text-gray-600">Status</p>
                <p class="text-xl font-bold text-gray-900">Pay Per Unlock</p>
                @endif
            </div>
        </div>

        @if($hasUnlimitedAccess)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <p class="text-green-800 text-center">
                🎉 You have unlimited access! All property matches are automatically unlocked for you.
            </p>
        </div>
        @else
        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($packages as $package)
            <div class="bg-white rounded-lg shadow-lg p-6 {{ $package->is_unlimited ? 'ring-2 ring-blue-500 relative' : 'border border-gray-200' }}">
                @if($package->is_unlimited)
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white text-xs font-bold px-4 py-1 rounded-full">
                    BEST VALUE
                </div>
                @endif

                <div class="text-center">
                    <h3 class="text-lg font-bold text-gray-900">{{ $package->name }}</h3>
                    <p class="text-sm text-gray-600 mt-2 h-12">{{ $package->description }}</p>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-3xl font-bold text-gray-900">{{ $package->getFormattedPrice() }}</span>
                    @if(!$package->is_unlimited)
                    <p class="text-sm text-gray-600 mt-1">{{ $package->getFormattedPricePerUnlock() }} each</p>
                    @else
                    <p class="text-sm text-green-600 mt-1 font-medium">One-time payment</p>
                    @endif
                </div>

                @if($package->discount_percentage > 0)
                <div class="mt-2 text-center">
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">
                        {{ $package->getSavingsText() }}
                    </span>
                </div>
                @endif

                <ul class="mt-6 space-y-2">
                    @if($package->is_unlimited)
                    <li class="flex items-start text-sm text-gray-700">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Unlimited property unlocks
                    </li>
                    <li class="flex items-start text-sm text-gray-700">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Access forever
                    </li>
                    <li class="flex items-start text-sm text-gray-700">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Best for active searchers
                    </li>
                    @else
                    <li class="flex items-start text-sm text-gray-700">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ $package->unlock_credits }} {{ $package->unlock_credits === 1 ? 'unlock' : 'unlocks' }}
                    </li>
                    <li class="flex items-start text-sm text-gray-700">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Credits never expire
                    </li>
                    @endif
                </ul>

                <form action="{{ route('unlocks.purchase') }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="package" value="{{ $package->slug }}">
                    <button type="submit" class="w-full {{ $package->is_unlimited ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-800 hover:bg-gray-900' }} text-white font-bold py-3 px-4 rounded-lg transition">
                        Buy Now
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        {{-- How It Works --}}
        <div class="mt-16 bg-gray-50 rounded-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Get Match Notifications</h3>
                    <p class="text-sm text-gray-600">Receive email/WhatsApp when properties match your saved searches</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">See Property Preview</h3>
                    <p class="text-sm text-gray-600">View title, location, price, and basic info for free</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Unlock to See Full Details</h3>
                    <p class="text-sm text-gray-600">Use credits or unlimited access to see photos, address, and contact agent</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

### 2. Property Preview/Lock Component

```blade
{{-- resources/views/components/property-preview-card.blade.php --}}

@props(['property', 'isLocked' => false, 'unlockUrl' => null])

<div class="bg-white rounded-lg shadow-md overflow-hidden {{ $isLocked ? 'relative' : '' }}">
    @if($isLocked)
    {{-- Locked Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-900 z-10 flex items-end justify-center p-6">
        <div class="text-center">
            <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <p class="text-white font-bold mb-3">Unlock to see full details</p>
            <a href="{{ $unlockUrl }}" class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold py-2 px-6 rounded-lg inline-block">
                Unlock Now - ₦2,000
            </a>
        </div>
    </div>
    @endif

    {{-- Property Image (blurred if locked) --}}
    <div class="h-48 overflow-hidden {{ $isLocked ? 'filter blur-sm' : '' }}">
        <img src="{{ $property['featured_image_thumbnail'] ?? '/images/placeholder.jpg' }}"
             alt="{{ $property['title'] }}"
             class="w-full h-full object-cover">
    </div>

    {{-- Property Info (partial if locked) --}}
    <div class="p-4 {{ $isLocked ? 'filter blur-sm' : '' }}">
        <h3 class="font-bold text-lg text-gray-900 truncate">{{ $property['title'] }}</h3>
        <p class="text-sm text-gray-600 mt-1">
            {{ $property['area_name'] }}, {{ $property['city_name'] }}
        </p>
        <p class="text-blue-600 font-bold mt-2">{{ $property['formatted_price'] }}</p>

        <div class="flex items-center mt-3 text-sm text-gray-600">
            @if($property['bedrooms'])
            <span class="mr-3">🛏 {{ $property['bedrooms'] }} bed</span>
            @endif
            @if($property['bathrooms'])
            <span>🚿 {{ $property['bathrooms'] }} bath</span>
            @endif
        </div>

        @if(!$isLocked)
        <a href="{{ route('properties.show', $property) }}" class="mt-4 block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
            View Full Details
        </a>
        @endif
    </div>
</div>
```

---

## Business Logic

### Key Rules

1. **Free Tier:**
   - 2 saved searches
   - Email notifications with preview only
   - Preview shows: title, location, price, bedrooms/bathrooms, property type
   - Preview HIDES: full address, gallery, description, agent contact

2. **Unlocking:**
   - Users can unlock individual properties (₦2,000 each)
   - OR buy bulk credits (5 for ₦8,000, 10 for ₦15,000, etc.)
   - OR buy unlimited access (₦30,000 one-time)
   - Credits never expire

3. **Unlimited Access:**
   - One-time payment of ₦30,000
   - All current AND future matches automatically unlocked
   - Forever (no expiration)
   - Refundable within 7 days if <3 properties unlocked

4. **Preview Generation:**
   - Automated blurring of featured image
   - Truncated descriptions
   - No contact information shown
   - "Unlock" CTA prominently displayed

---

## Testing Strategy

### Unit Tests

```php
// tests/Unit/MatchUnlockTest.php

class MatchUnlockTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_unlimited_access_can_unlock_any_property()
    {
        $user = User::factory()->create(['has_unlimited_match_access' => true]);
        $property = Property::factory()->create();

        $this->assertTrue($user->hasUnlockedProperty($property->id));
    }

    public function test_user_with_credits_can_unlock_property()
    {
        $user = User::factory()->create(['unlock_credits' => 5]);
        $property = Property::factory()->create();

        $user->unlockProperty($property->id, 'credit');

        $this->assertEquals(4, $user->fresh()->unlock_credits);
        $this->assertTrue($user->hasUnlockedProperty($property->id));
    }

    // Add more tests...
}
```

---

## Deployment Checklist

- [ ] Run migrations
- [ ] Seed unlock packages
- [ ] Update SavedSearchMatch notification email template
- [ ] Add preview/unlock UI to match notifications
- [ ] Test unlock flow (test mode)
- [ ] Configure Paystack webhook
- [ ] Test unlimited access purchase
- [ ] Test credit deduction
- [ ] Verify preview data doesn't leak sensitive info
- [ ] Add "Unlocked Properties" section to user dashboard
- [ ] Test refund logic
- [ ] Document for support team

---

**Implementation Status:** Ready for Development
**Estimated Timeline:** 2 weeks
**Test Mode Duration:** Until 2,000 active users (3-6 months)
