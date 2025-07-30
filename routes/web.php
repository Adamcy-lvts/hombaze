<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/properties', App\Livewire\PropertySearch::class)->name('properties.search');
Route::get('/property/{property:slug}', App\Livewire\PropertyDetails::class)->name('property.show');

// New navigation pages
Route::get('/agents', App\Livewire\AgentsPage::class)->name('agents');
Route::get('/agencies', App\Livewire\AgenciesPage::class)->name('agencies');
Route::get('/about', App\Livewire\AboutPage::class)->name('about');
Route::get('/contact', App\Livewire\ContactPage::class)->name('contact');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
