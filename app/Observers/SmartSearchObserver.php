<?php

namespace App\Observers;

use App\Models\SmartSearch;
use App\Jobs\ProcessSmartSearchMatches;
use Illuminate\Support\Facades\Log;

class SmartSearchObserver
{
    public function created(SmartSearch $smartSearch): void
    {
        // Only process if the search is active and not expired
        if (!$smartSearch->is_active || $smartSearch->is_expired) {
            return;
        }

        Log::info("🔍 SmartSearchObserver: New smart search created", [
            'search_id' => $smartSearch->id,
            'search_name' => $smartSearch->name,
            'user_id' => $smartSearch->user_id,
            'tier' => $smartSearch->tier,
            'categories' => $smartSearch->property_categories,
        ]);

        // Dispatch job to find existing properties that match this new search
        ProcessSmartSearchMatches::dispatch(
            propertyId: null, // No specific property, check all available properties
            smartSearchId: $smartSearch->id, // Specific search to check
            newPropertiesOnly: false // Check all existing properties, not just new ones
        );

        Log::info("✅ SmartSearchObserver: Job dispatched for new smart search", [
            'search_id' => $smartSearch->id,
        ]);
    }

    public function updated(SmartSearch $smartSearch): void
    {
        // If the search was activated, check for matches
        if ($smartSearch->wasChanged('is_active') && $smartSearch->is_active && !$smartSearch->is_expired) {
            Log::info("🔄 SmartSearchObserver: Smart search reactivated", [
                'search_id' => $smartSearch->id,
                'search_name' => $smartSearch->name,
            ]);

            // Dispatch job to find existing properties that match this reactivated search
            ProcessSmartSearchMatches::dispatch(
                propertyId: null,
                smartSearchId: $smartSearch->id,
                newPropertiesOnly: false
            );
        }
    }
}
