<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertySearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Search API Routes
Route::prefix('search')->group(function () {
    Route::get('suggestions', [PropertySearchController::class, 'suggestions'])->name('api.search.suggestions');
    Route::get('properties', [PropertySearchController::class, 'search'])->name('api.search.properties');
    Route::get('filters', [PropertySearchController::class, 'getFilters'])->name('api.search.filters');
    Route::get('locations', [PropertySearchController::class, 'locationSuggestions'])->name('api.search.locations');
});