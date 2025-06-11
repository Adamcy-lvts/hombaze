<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;

Route::view('/', 'welcome');
Route::get('/search', [LandingController::class, 'search'])->name('properties.search');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
