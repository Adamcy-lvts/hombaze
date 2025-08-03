<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\TenantInvitationController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/properties', App\Livewire\PropertySearch::class)->name('properties.search');
Route::get('/property/{property:slug}', App\Livewire\PropertyDetails::class)->name('property.show');

// New navigation pages
Route::get('/agents', App\Livewire\AgentsPage::class)->name('agents');
Route::get('/agencies', App\Livewire\AgenciesPage::class)->name('agencies');
Route::get('/about', App\Livewire\AboutPage::class)->name('about');
Route::get('/contact', App\Livewire\ContactPage::class)->name('contact');

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

// PDF download routes
Route::middleware(['auth'])->group(function () {
    Route::get('/landlord/lease/{lease}/download-pdf', [App\Http\Controllers\PdfDownloadController::class, 'downloadLeasePdf'])->name('landlord.lease.download-pdf');
    Route::get('/landlord/lease/{lease}/view-with-template', [App\Http\Controllers\PdfDownloadController::class, 'viewLeaseWithTemplate'])->name('landlord.lease.view-with-template');
    Route::get('/landlord/payment/{payment}/download-receipt', [App\Http\Controllers\PdfDownloadController::class, 'downloadReceiptPdf'])->name('landlord.payment.download-receipt');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
