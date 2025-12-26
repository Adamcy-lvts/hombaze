<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantInvitationController;
use App\Http\Controllers\ReceiptViewController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\ListingBundleController;
use App\Http\Controllers\SmartSearchPaymentController;

Route::get('/', App\Livewire\LandingPage::class)->name('landing');
Route::get('/properties', App\Livewire\PropertySearch::class)->name('properties.search');
Route::get('/property/{property:slug}', App\Livewire\PropertyDetails::class)->name('property.show');
Route::get('/pricing', App\Livewire\PricingPage::class)->name('pricing');

// New navigation pages
Route::get('/agents', App\Livewire\AgentsPage::class)->name('agents');
Route::get('/agent/{agent:slug}', App\Livewire\AgentProfile::class)->name('agent.profile');
Route::get('/agencies', App\Livewire\AgenciesPage::class)->name('agencies');
Route::get('/agency/{agency:slug}', App\Livewire\AgencyProfile::class)->name('agency.show');
// Route::get('/about', App\Livewire\AboutPage::class)->name('about');
// Route::get('/contact', App\Livewire\ContactPage::class)->name('contact');

// Agent review routes
Route::middleware(['auth'])->group(function () {
    Route::get('/agent/{agent:slug}/review', [App\Http\Controllers\AgentReviewController::class, 'create'])->name('agent.review.create');
    Route::post('/agent/{agent:slug}/review', [App\Http\Controllers\AgentReviewController::class, 'store'])->name('agent.review.store');
    Route::patch('/agent/{agent:slug}/review', [App\Http\Controllers\AgentReviewController::class, 'update'])->name('agent.review.update');

    Route::post('/payments/rent/{payment}/paystack', [PaystackController::class, 'initializeRentPayment'])
        ->name('payments.rent.paystack');

    Route::post('/billing/listing-bundles/{type}/{slug}', [ListingBundleController::class, 'purchase'])
        ->name('listing-bundles.purchase');
});

// Tenant invitation routes
Route::prefix('tenant/invitation')->name('tenant.invitation.')->group(function () {
    Route::get('/{token}', [TenantInvitationController::class, 'show'])->name('show');
    Route::get('/{token}/accept', [TenantInvitationController::class, 'show'])->name('accept');
    Route::post('/{token}/register', [TenantInvitationController::class, 'register'])->name('register');
    Route::post('/{token}/login', [TenantInvitationController::class, 'login'])->name('login');
});

// Tenant association requirement
Route::get('/tenant/no-landlord', function () {
    return view('tenant.no-landlord');
})->name('tenant.no-landlord');


// Universal receipt view route for QR codes
Route::get('/receipt/{receiptId}', [ReceiptViewController::class, 'view'])->name('receipt.view');

// PDF download routes
Route::middleware(['auth'])->group(function () {
    Route::get('/landlord/lease/{lease}/download-pdf', [App\Http\Controllers\PdfDownloadController::class, 'downloadLeasePdf'])->name('landlord.lease.download-pdf');
    Route::get('/landlord/lease/{lease}/view-with-template', [App\Http\Controllers\PdfDownloadController::class, 'viewLeaseWithTemplate'])->name('landlord.lease.view-with-template');
    Route::get('/landlord/payment/{payment}/download-receipt', [App\Http\Controllers\PdfDownloadController::class, 'downloadReceiptPdf'])->name('landlord.payment.download-receipt');
});

Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Customer\Dashboard::class)->name('dashboard');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Customer-specific routes
Route::middleware(['auth', 'customer'])->prefix('user')->name('customer.')->group(function () {
    Route::get('/saved-properties', App\Livewire\Customer\SavedProperties::class)->name('saved-properties');

    Route::get('/inquiries', App\Livewire\Customer\Inquiries::class)->name('inquiries');

    Route::get('/viewings', function () {
        return view('customer.viewings');
    })->name('viewings');

    Route::get('/settings', App\Livewire\Customer\Settings::class)->name('settings');

    Route::get('/preferences', App\Livewire\Customer\SettingsPreferences::class)->name('preferences');

    // Search management routes
    Route::prefix('searches')->name('searches.')->group(function () {
        Route::get('/', App\Livewire\Customer\SearchManager::class)->name('index');
        Route::get('/create', App\Livewire\Customer\CreateSearch::class)->name('create');
        Route::get('/{search}/edit', App\Livewire\Customer\EditSearch::class)->name('edit');
    });

    Route::get('/profile-completion', function () {
        return view('customer.profile-completion');
    })->name('profile-completion');

    Route::get('/activity', function () {
        return view('customer.activity');
    })->name('activity');
});

// WhatsApp webhook routes
Route::prefix('api/whatsapp')->name('whatsapp.')->group(function () {
    Route::get('/webhook', [WhatsAppWebhookController::class, 'verify'])->name('webhook.verify');
    Route::post('/webhook', [WhatsAppWebhookController::class, 'handleWebhook'])->name('webhook.handle');
});

Route::get('/payments/paystack/callback', [PaystackController::class, 'handleCallback'])
    ->name('paystack.callback');

Route::get('/billing/listing-bundles/callback', [ListingBundleController::class, 'callback'])
    ->name('listing-bundles.callback');

// SmartSearch routes
Route::prefix('smartsearch')->name('smartsearch.')->group(function () {
    Route::get('/pricing', [SmartSearchPaymentController::class, 'showPricing'])->name('pricing');
    Route::get('/payment/callback', [SmartSearchPaymentController::class, 'handleCallback'])->name('payment.callback');

    Route::middleware(['auth'])->group(function () {
        Route::post('/purchase/{tier}', [SmartSearchPaymentController::class, 'purchase'])->name('purchase');
        Route::post('/extend/{search}', [SmartSearchPaymentController::class, 'extendForNoMatch'])->name('extend');
    });
});

require __DIR__.'/auth.php';
