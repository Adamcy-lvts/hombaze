<?php

namespace App\Observers;

use App\Models\SavedSearch;
use App\Jobs\ProcessSavedSearchMatches;
use Illuminate\Support\Facades\Log;

class SavedSearchObserver
{
    public function created(SavedSearch $savedSearch): void
    {
        // Only process if the search is active
        if (!$savedSearch->is_active) {
            return;
        }

        Log::info("🔍 SavedSearchObserver: New saved search created", [
            'search_id' => $savedSearch->id,
            'search_name' => $savedSearch->name,
            'user_id' => $savedSearch->user_id,
            'categories' => $savedSearch->property_categories,
        ]);

        // Dispatch job to find existing properties that match this new search
        ProcessSavedSearchMatches::dispatch(
            propertyId: null, // No specific property, check all available properties
            savedSearchId: $savedSearch->id, // Specific search to check
            newPropertiesOnly: false // Check all existing properties, not just new ones
        );

        Log::info("✅ SavedSearchObserver: Job dispatched for new saved search", [
            'search_id' => $savedSearch->id,
        ]);
    }

    public function updated(SavedSearch $savedSearch): void
    {
        // If the search was activated, check for matches
        if ($savedSearch->wasChanged('is_active') && $savedSearch->is_active) {
            Log::info("🔄 SavedSearchObserver: Saved search reactivated", [
                'search_id' => $savedSearch->id,
                'search_name' => $savedSearch->name,
            ]);

            // Dispatch job to find existing properties that match this reactivated search
            ProcessSavedSearchMatches::dispatch(
                propertyId: null,
                savedSearchId: $savedSearch->id,
                newPropertiesOnly: false
            );
        }
    }
}