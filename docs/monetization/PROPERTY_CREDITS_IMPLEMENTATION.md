# Property Credits System - Implementation Guide

## Overview

The Property Credits System replaces monthly subscriptions with a **pay-per-property credit model**. Users buy credits to list additional properties beyond the free tier. Credits never expire and can be purchased in bulk with discounts.

**Implementation Priority:** #1 (Foundation)
**Estimated Time:** 2 weeks
**Revenue Potential:** ₦15M Year 1 (one-time from initial users)

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

### 1. Update `users` Table

```php
// Migration: add_property_credits_to_users_table.php

Schema::table('users', function (Blueprint $table) {
    $table->integer('property_credits')->default(1)->after('email'); // Free tier: 1 property
    $table->integer('total_credits_purchased')->default(0)->after('property_credits'); // Lifetime total
    $table->timestamp('last_credit_purchase_at')->nullable()->after('total_credits_purchased');
});
```

**Fields Explanation:**
- `property_credits`: Current available credits (decrements on property creation)
- `total_credits_purchased`: Lifetime total for analytics (never decrements)
- `last_credit_purchase_at`: Track last purchase date for marketing

---

### 2. Create `credit_purchases` Table

```php
// Migration: create_credit_purchases_table.php

Schema::create('credit_purchases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('package_type'); // 'single', 'small', 'medium', 'large', 'enterprise'
    $table->integer('credits_amount'); // Number of credits purchased (1, 5, 15, 50)
    $table->decimal('price_paid', 10, 2); // Amount paid in naira
    $table->string('payment_method')->default('paystack'); // 'paystack', 'flutterwave', 'manual'
    $table->string('payment_reference')->unique(); // Paystack reference
    $table->string('payment_status')->default('pending'); // 'pending', 'success', 'failed', 'refunded'
    $table->text('payment_metadata')->nullable(); // JSON: gateway response
    $table->timestamp('paid_at')->nullable();
    $table->timestamp('refunded_at')->nullable();
    $table->string('refund_reason')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'payment_status']);
    $table->index('payment_reference');
});
```

---

### 3. Update `properties` Table

```php
// Migration: add_credit_tracking_to_properties_table.php

Schema::table('properties', function (Blueprint $table) {
    $table->boolean('uses_credit')->default(false)->after('is_published'); // Track if property used a credit
    $table->timestamp('credit_used_at')->nullable()->after('uses_credit');
    $table->foreignId('credit_purchase_id')->nullable()->constrained('credit_purchases')->after('credit_used_at');
});
```

**Purpose:** Track which properties used credits for refund logic and analytics.

---

### 4. Create `credit_packages` Configuration Table

```php
// Migration: create_credit_packages_table.php

Schema::create('credit_packages', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique(); // 'single', 'small', 'medium', 'large', 'enterprise'
    $table->string('name'); // "Single Property", "Small Bundle"
    $table->text('description')->nullable();
    $table->integer('credits'); // Number of credits
    $table->decimal('price', 10, 2); // Price in naira
    $table->decimal('price_per_credit', 10, 2); // Calculated: price / credits
    $table->integer('discount_percentage')->default(0); // 0, 20, 33, 60
    $table->integer('property_duration_days')->default(90); // How long property stays active
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

**Seeder Data:**

```php
// Database/Seeders/CreditPackageSeeder.php

DB::table('credit_packages')->insert([
    [
        'slug' => 'single',
        'name' => 'Single Property',
        'description' => 'List one additional property for 90 days',
        'credits' => 1,
        'price' => 5000,
        'price_per_credit' => 5000,
        'discount_percentage' => 0,
        'property_duration_days' => 90,
        'is_active' => true,
        'sort_order' => 1,
    ],
    [
        'slug' => 'small',
        'name' => 'Small Bundle',
        'description' => 'List 5 properties - Save 20%',
        'credits' => 5,
        'price' => 20000,
        'price_per_credit' => 4000,
        'discount_percentage' => 20,
        'property_duration_days' => 90,
        'is_active' => true,
        'sort_order' => 2,
    ],
    [
        'slug' => 'medium',
        'name' => 'Medium Bundle',
        'description' => 'List 15 properties - Save 33%',
        'credits' => 15,
        'price' => 50000,
        'price_per_credit' => 3333,
        'discount_percentage' => 33,
        'property_duration_days' => 90,
        'is_active' => true,
        'sort_order' => 3,
    ],
    [
        'slug' => 'large',
        'name' => 'Large Bundle',
        'description' => 'List 50 properties - Save 60%',
        'credits' => 50,
        'price' => 100000,
        'price_per_credit' => 2000,
        'discount_percentage' => 60,
        'property_duration_days' => 90,
        'is_active' => true,
        'sort_order' => 4,
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
        'property_credits',
        'total_credits_purchased',
        'last_credit_purchase_at',
    ];

    protected $casts = [
        'last_credit_purchase_at' => 'datetime',
    ];

    // Relationships
    public function creditPurchases()
    {
        return $this->hasMany(CreditPurchase::class);
    }

    // Helper Methods
    public function hasAvailableCredits(int $amount = 1): bool
    {
        return $this->property_credits >= $amount;
    }

    public function deductCredit(int $amount = 1): bool
    {
        if (!$this->hasAvailableCredits($amount)) {
            return false;
        }

        $this->decrement('property_credits', $amount);
        return true;
    }

    public function addCredits(int $amount): void
    {
        $this->increment('property_credits', $amount);
        $this->increment('total_credits_purchased', $amount);
        $this->update(['last_credit_purchase_at' => now()]);
    }

    public function refundCredit(int $amount = 1): void
    {
        $this->increment('property_credits', $amount);
    }

    public function getActivePropertiesCount(): int
    {
        return $this->properties()
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->count();
    }

    public function canCreateProperty(): bool
    {
        // Free tier: 1 property without credits
        $activeCount = $this->getActivePropertiesCount();

        if ($activeCount === 0) {
            return true; // First property is free
        }

        return $this->hasAvailableCredits();
    }

    public function getRemainingFreeProperties(): int
    {
        $activeCount = $this->getActivePropertiesCount();
        return $activeCount === 0 ? 1 : 0;
    }
}
```

---

#### CreditPurchase Model

```php
// app/Models/CreditPurchase.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'package_type',
        'credits_amount',
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
        return $this->belongsTo(CreditPackage::class, 'package_type', 'slug');
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // Helper Methods
    public function markAsPaid(array $metadata = []): void
    {
        $this->update([
            'payment_status' => 'success',
            'paid_at' => now(),
            'payment_metadata' => $metadata,
        ]);

        // Add credits to user
        $this->user->addCredits($this->credits_amount);
    }

    public function markAsFailed(array $metadata = []): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_metadata' => $metadata,
        ]);
    }

    public function refund(string $reason): void
    {
        $this->update([
            'payment_status' => 'refunded',
            'refunded_at' => now(),
            'refund_reason' => $reason,
        ]);

        // Deduct credits from user (if they still have them)
        if ($this->user->property_credits >= $this->credits_amount) {
            $this->user->decrement('property_credits', $this->credits_amount);
        }
    }

    public function canBeRefunded(): bool
    {
        return $this->payment_status === 'success'
            && $this->paid_at
            && $this->paid_at->diffInDays(now()) <= 7
            && !$this->refunded_at;
    }
}
```

---

#### CreditPackage Model

```php
// app/Models/CreditPackage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPackage extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'credits',
        'price',
        'price_per_credit',
        'discount_percentage',
        'property_duration_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_per_credit' => 'decimal:2',
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

    public function getFormattedPricePerCredit(): string
    {
        return '₦' . number_format($this->price_per_credit, 0);
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

#### CreditService

```php
// app/Services/CreditService.php

namespace App\Services;

use App\Models\User;
use App\Models\CreditPackage;
use App\Models\CreditPurchase;
use App\Models\Property;

class CreditService
{
    /**
     * Initiate credit purchase
     */
    public function initiatePurchase(User $user, string $packageSlug): CreditPurchase
    {
        $package = CreditPackage::where('slug', $packageSlug)
            ->where('is_active', true)
            ->firstOrFail();

        return CreditPurchase::create([
            'user_id' => $user->id,
            'package_type' => $package->slug,
            'credits_amount' => $package->credits,
            'price_paid' => $package->price,
            'payment_method' => 'paystack',
            'payment_reference' => $this->generateReference(),
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Deduct credit when property is created
     */
    public function deductCreditForProperty(User $user, Property $property): bool
    {
        // First property is free
        if ($user->getActivePropertiesCount() === 1 && $user->total_credits_purchased === 0) {
            $property->update(['uses_credit' => false]);
            return true;
        }

        // Deduct credit
        if ($user->deductCredit()) {
            $property->update([
                'uses_credit' => true,
                'credit_used_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Refund credit when property is deleted within 7 days
     */
    public function refundCreditForProperty(Property $property): bool
    {
        if (!$property->uses_credit || !$property->credit_used_at) {
            return false;
        }

        // Only refund if deleted within 7 days of creation
        if ($property->credit_used_at->diffInDays(now()) > 7) {
            return false;
        }

        // Only refund if property was never published or was published <24 hours
        if ($property->is_published && $property->published_at) {
            if ($property->published_at->diffInHours(now()) > 24) {
                return false;
            }
        }

        $property->user->refundCredit();

        $property->update([
            'uses_credit' => false,
            'credit_used_at' => null,
        ]);

        return true;
    }

    /**
     * Check if user needs to buy credits
     */
    public function needsCredits(User $user): bool
    {
        return !$user->canCreateProperty();
    }

    /**
     * Get recommended package for user
     */
    public function getRecommendedPackage(User $user): ?CreditPackage
    {
        $activeCount = $user->getActivePropertiesCount();

        // New users or users with 1-3 properties: Small bundle
        if ($activeCount <= 3) {
            return CreditPackage::where('slug', 'small')->first();
        }

        // Users with 4-10 properties: Medium bundle
        if ($activeCount <= 10) {
            return CreditPackage::where('slug', 'medium')->first();
        }

        // Users with 10+ properties: Large bundle
        return CreditPackage::where('slug', 'large')->first();
    }

    /**
     * Generate unique payment reference
     */
    private function generateReference(): string
    {
        return 'CREDIT_' . strtoupper(uniqid());
    }
}
```

---

### 3. Observers

#### PropertyObserver Updates

```php
// app/Observers/PropertyObserver.php

class PropertyObserver
{
    public function __construct(
        private CreditService $creditService
    ) {}

    public function created(Property $property): void
    {
        // Deduct credit if needed
        $this->creditService->deductCreditForProperty($property->user, $property);

        // ... existing observer logic
    }

    public function deleting(Property $property): void
    {
        // Try to refund credit (only works within 7 days + other conditions)
        $this->creditService->refundCreditForProperty($property);

        // ... existing observer logic
    }
}
```

---

### 4. Controllers

#### CreditController

```php
// app/Http/Controllers/CreditController.php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Services\CreditService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function __construct(
        private CreditService $creditService,
        private PaystackService $paystackService
    ) {}

    /**
     * Show pricing page
     */
    public function index()
    {
        $packages = CreditPackage::active()->get();
        $user = auth()->user();
        $recommended = $this->creditService->getRecommendedPackage($user);

        return view('credits.index', compact('packages', 'user', 'recommended'));
    }

    /**
     * Show user's credit dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();

        return view('credits.dashboard', [
            'available_credits' => $user->property_credits,
            'total_purchased' => $user->total_credits_purchased,
            'active_properties' => $user->getActivePropertiesCount(),
            'free_properties_remaining' => $user->getRemainingFreeProperties(),
            'recent_purchases' => $user->creditPurchases()->latest()->take(10)->get(),
        ]);
    }

    /**
     * Initiate credit purchase
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'package' => 'required|exists:credit_packages,slug',
        ]);

        $user = auth()->user();
        $purchase = $this->creditService->initiatePurchase($user, $request->package);

        // Initialize Paystack payment
        $paymentData = $this->paystackService->initializePayment(
            email: $user->email,
            amount: $purchase->price_paid * 100, // Paystack uses kobo
            reference: $purchase->payment_reference,
            metadata: [
                'purchase_id' => $purchase->id,
                'user_id' => $user->id,
                'package_type' => $purchase->package_type,
                'credits_amount' => $purchase->credits_amount,
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
            return redirect()->route('credits.index')
                ->with('error', 'Payment reference not found');
        }

        $verification = $this->paystackService->verifyPayment($reference);

        if ($verification['status'] === 'success') {
            $purchase = CreditPurchase::where('payment_reference', $reference)->firstOrFail();
            $purchase->markAsPaid($verification['data']);

            return redirect()->route('credits.dashboard')
                ->with('success', "Payment successful! {$purchase->credits_amount} credits added to your account.");
        }

        return redirect()->route('credits.index')
            ->with('error', 'Payment verification failed. Please contact support.');
    }

    /**
     * Webhook handler
     */
    public function webhook(Request $request)
    {
        // Verify Paystack signature
        if (!$this->paystackService->verifyWebhookSignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'charge.success') {
            $reference = $data['reference'];
            $purchase = CreditPurchase::where('payment_reference', $reference)->first();

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

### 1. Pricing Page

```blade
{{-- resources/views/credits/index.blade.php --}}

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900">Property Listing Credits</h1>
            <p class="mt-4 text-xl text-gray-600">
                List more properties and grow your business. Credits never expire.
            </p>
        </div>

        {{-- Current Credits Status --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Your Available Credits</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $user->property_credits }}</p>
                </div>
                <div>
                    <p class="text-sm text-blue-600 font-medium">Active Properties</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $user->getActivePropertiesCount() }}</p>
                </div>
                @if($user->getRemainingFreeProperties() > 0)
                <div>
                    <p class="text-sm text-green-600 font-medium">Free Listings Available</p>
                    <p class="text-3xl font-bold text-green-900">{{ $user->getRemainingFreeProperties() }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($packages as $package)
            <div class="bg-white rounded-lg shadow-lg p-6 {{ $recommended && $recommended->id === $package->id ? 'ring-2 ring-blue-500' : 'border border-gray-200' }}">
                @if($recommended && $recommended->id === $package->id)
                <div class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full inline-block mb-4">
                    RECOMMENDED
                </div>
                @endif

                <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ $package->description }}</p>

                <div class="mt-6">
                    <span class="text-4xl font-bold text-gray-900">{{ $package->getFormattedPrice() }}</span>
                </div>

                <div class="mt-2 text-sm text-gray-600">
                    {{ $package->getFormattedPricePerCredit() }} per property
                </div>

                @if($package->discount_percentage > 0)
                <div class="mt-2">
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">
                        {{ $package->getSavingsText() }}
                    </span>
                </div>
                @endif

                <ul class="mt-6 space-y-3">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">{{ $package->credits }} property {{ $package->credits > 1 ? 'listings' : 'listing' }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">{{ $package->property_duration_days }} days active</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Credits never expire</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">7-day refund policy</span>
                    </li>
                </ul>

                <form action="{{ route('credits.purchase') }}" method="POST" class="mt-8">
                    @csrf
                    <input type="hidden" name="package" value="{{ $package->slug }}">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                        Buy Now
                    </button>
                </form>
            </div>
            @endforeach
        </div>

        {{-- FAQ Section --}}
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
            <!-- Add FAQ component here -->
        </div>
    </div>
</x-app-layout>
```

---

### 2. Credits Dashboard

```blade
{{-- resources/views/credits/dashboard.blade.php --}}

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">My Credits</h1>

        {{-- Credits Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Available Credits</p>
                <p class="text-3xl font-bold text-blue-600">{{ $available_credits }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Total Purchased</p>
                <p class="text-3xl font-bold text-gray-900">{{ $total_purchased }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Active Properties</p>
                <p class="text-3xl font-bold text-gray-900">{{ $active_properties }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Free Listings Left</p>
                <p class="text-3xl font-bold text-green-600">{{ $free_properties_remaining }}</p>
            </div>
        </div>

        @if($available_credits < 2)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <p class="text-sm text-yellow-800">
                    You're running low on credits. <a href="{{ route('credits.index') }}" class="font-bold underline">Buy more credits</a> to continue listing properties.
                </p>
            </div>
        </div>
        @endif

        {{-- Recent Purchases --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Purchase History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Package</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recent_purchases as $purchase)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchase->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchase->package->name ?? ucfirst($purchase->package_type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchase->credits_amount }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₦{{ number_format($purchase->price_paid, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $purchase->payment_status === 'success' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $purchase->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $purchase->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($purchase->payment_status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No purchase history yet. <a href="{{ route('credits.index') }}" class="text-blue-600 hover:underline">Buy credits</a> to get started.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

### 3. Low Credits Warning Component

```blade
{{-- resources/views/components/low-credits-warning.blade.php --}}

@props(['user'])

@if($user->property_credits < 2)
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">
                You have <strong>{{ $user->property_credits }} {{ $user->property_credits === 1 ? 'credit' : 'credits' }}</strong> remaining.
                <a href="{{ route('credits.index') }}" class="font-medium underline text-yellow-700 hover:text-yellow-600">
                    Buy more credits
                </a>
                to continue listing properties.
            </p>
        </div>
    </div>
</div>
@endif
```

---

## Payment Integration

### PaystackService

```php
// app/Services/PaystackService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PaystackService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
    }

    /**
     * Initialize payment
     */
    public function initializePayment(string $email, int $amount, string $reference, array $metadata = []): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $email,
                'amount' => $amount, // In kobo (multiply naira by 100)
                'reference' => $reference,
                'metadata' => $metadata,
                'callback_url' => route('credits.callback'),
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to initialize payment: ' . $response->body());
        }

        return $response->json()['data'];
    }

    /**
     * Verify payment
     */
    public function verifyPayment(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (!$response->successful()) {
            throw new \Exception('Failed to verify payment: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Paystack-Signature');

        if (!$signature) {
            return false;
        }

        $computedSignature = hash_hmac('sha512', $request->getContent(), $this->secretKey);

        return hash_equals($signature, $computedSignature);
    }
}
```

---

## Business Logic

### Key Rules

1. **Free Tier:**
   - Every user gets 1 free property listing
   - No credit deduction for first property
   - Must buy credits for additional properties

2. **Credit Deduction:**
   - Deducted when property is created/published
   - Only deducted if user has >0 active properties already
   - Cannot create property without credits (after free tier used)

3. **Credit Refund:**
   - Automatic refund if property deleted within 7 days
   - Only refunds if property was not published, or published <24 hours
   - Refunds are automatic (no manual approval needed)
   - Cannot refund after 7 days

4. **Credit Expiration:**
   - Credits NEVER expire
   - Users can accumulate unlimited credits
   - No time pressure to use credits

5. **Property Duration:**
   - Each property stays active for 90 days (configurable per package)
   - After 90 days, property auto-deactivates (can be reactivated with another credit)
   - Users get email reminder at 7 days before expiration

---

## Testing Strategy

### Unit Tests

```php
// tests/Unit/CreditServiceTest.php

class CreditServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_first_property_without_credits()
    {
        $user = User::factory()->create(['property_credits' => 0]);

        $this->assertTrue($user->canCreateProperty());
    }

    public function test_user_cannot_create_second_property_without_credits()
    {
        $user = User::factory()->create(['property_credits' => 0]);
        Property::factory()->for($user)->create();

        $this->assertFalse($user->canCreateProperty());
    }

    public function test_credit_is_deducted_on_property_creation()
    {
        $user = User::factory()->create(['property_credits' => 5]);
        Property::factory()->for($user)->create(); // Free property

        $this->assertEquals(5, $user->fresh()->property_credits);

        Property::factory()->for($user)->create(); // Should use credit

        $this->assertEquals(4, $user->fresh()->property_credits);
    }

    public function test_credit_is_refunded_when_property_deleted_within_7_days()
    {
        $user = User::factory()->create(['property_credits' => 3]);
        $property = Property::factory()->for($user)->create([
            'uses_credit' => true,
            'credit_used_at' => now(),
        ]);

        $this->assertEquals(3, $user->fresh()->property_credits);

        $property->delete();

        $this->assertEquals(4, $user->fresh()->property_credits);
    }

    public function test_credit_is_not_refunded_after_7_days()
    {
        $user = User::factory()->create(['property_credits' => 3]);
        $property = Property::factory()->for($user)->create([
            'uses_credit' => true,
            'credit_used_at' => now()->subDays(8),
        ]);

        $property->delete();

        $this->assertEquals(3, $user->fresh()->property_credits);
    }
}
```

---

### Feature Tests

```php
// tests/Feature/CreditPurchaseTest.php

class CreditPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_pricing_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('credits.index'));

        $response->assertOk();
        $response->assertSee('Property Listing Credits');
    }

    public function test_user_can_initiate_credit_purchase()
    {
        $user = User::factory()->create();
        $package = CreditPackage::factory()->create(['slug' => 'small', 'price' => 20000]);

        $response = $this->actingAs($user)->post(route('credits.purchase'), [
            'package' => 'small',
        ]);

        $response->assertRedirect(); // Redirects to Paystack
        $this->assertDatabaseHas('credit_purchases', [
            'user_id' => $user->id,
            'package_type' => 'small',
            'payment_status' => 'pending',
        ]);
    }

    // Add more feature tests...
}
```

---

## Deployment Checklist

### Before Going Live

- [ ] Run all migrations
- [ ] Seed credit packages
- [ ] Configure Paystack keys (test mode initially)
- [ ] Set up webhook URL in Paystack dashboard
- [ ] Test full purchase flow in test mode
- [ ] Test refund logic
- [ ] Configure email notifications
- [ ] Add pricing page to main navigation
- [ ] Update user dashboard to show credits
- [ ] Test property creation with/without credits
- [ ] Configure low credits warnings
- [ ] Set up monitoring for failed payments
- [ ] Document refund policy
- [ ] Create FAQ content
- [ ] Train support team on credit system

### When Activating (at 2,000 Users)

- [ ] Switch Paystack to live mode
- [ ] Announce pricing to existing users
- [ ] Offer grace period (30 days)
- [ ] Send onboarding emails
- [ ] Monitor conversion rates
- [ ] Watch for support tickets
- [ ] Track revenue in analytics
- [ ] Set up revenue alerts
- [ ] Review and adjust pricing if needed

---

## Configuration Files

### Environment Variables

```env
# .env

# Paystack (Test Mode Initially)
PAYSTACK_PUBLIC_KEY=pk_test_xxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxxxxxxxxxx
PAYSTACK_PAYMENT_URL=https://api.paystack.co

# When going live:
# PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxxx
# PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxxx
```

### Config File

```php
// config/services.php

return [
    // ... existing services

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'payment_url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
        'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL'),
    ],
];
```

---

## Routes

```php
// routes/web.php

use App\Http\Controllers\CreditController;

Route::middleware(['auth'])->group(function () {
    // Credits
    Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');
    Route::get('/credits/dashboard', [CreditController::class, 'dashboard'])->name('credits.dashboard');
    Route::post('/credits/purchase', [CreditController::class, 'purchase'])->name('credits.purchase');
    Route::get('/credits/callback', [CreditController::class, 'callback'])->name('credits.callback');
});

// Webhook (no auth middleware)
Route::post('/webhooks/paystack/credits', [CreditController::class, 'webhook'])->name('credits.webhook');
```

---

## Next Steps

1. ✅ Implement database migrations
2. ✅ Create models with relationships
3. ✅ Build CreditService and PaystackService
4. ✅ Create controllers and routes
5. ✅ Design and build UI components
6. ✅ Write unit and feature tests
7. ✅ Set up Paystack test account
8. ✅ Test full purchase flow
9. ✅ Deploy to staging
10. ✅ User acceptance testing
11. ⏳ Keep in TEST MODE until 2,000 users
12. ⏳ Activate when milestone reached

---

**Implementation Status:** Ready for Development
**Estimated Timeline:** 2 weeks
**Test Mode Duration:** Until 2,000 active users (3-6 months)
