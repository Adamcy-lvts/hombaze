<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\UnifiedLoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    // Unified Login System
    Route::get('login', [UnifiedLoginController::class, 'show'])->name('login');
    Route::post('login', [UnifiedLoginController::class, 'login']);
    
    // Unified Registration System (excludes tenants - they register via landlord invitations)
    Route::get('register', [App\Http\Controllers\Auth\UnifiedRegistrationController::class, 'show'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\UnifiedRegistrationController::class, 'register'])->name('unified.register');
    
    // Premium Password Reset
    Route::get('forgot-password', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    // Unified Logout
    Route::post('logout', [UnifiedLoginController::class, 'logout'])->name('logout');
    
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
