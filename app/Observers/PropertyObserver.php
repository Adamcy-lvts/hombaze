<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\SavedSearch;
use App\Jobs\ProcessSavedSearchMatches;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PropertyObserver
{
    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        $this->triggerSavedSearchMatching($property, 'created');
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        // Only trigger matching if publication status or availability changed
        if ($property->wasChanged(['is_published', 'status'])) {
            $this->triggerSavedSearchMatching($property, 'updated');
        }
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        $this->handlePropertyRemoval($property, 'deleted');
    }

    /**
     * Handle the Property "trashed" event (soft delete).
     */
    public function trashed(Property $property): void
    {
        $this->handlePropertyRemoval($property, 'trashed');
    }

    /**
     * Handle property removal (both soft delete and hard delete)
     */
    private function handlePropertyRemoval(Property $property, string $event): void
    {
        Log::info("🗑️ PROPERTY OBSERVER - Property {$event} - Cleaning up cached matches", [
            'event' => $event,
            'property_id' => $property->id,
            'property_title' => $property->title ?? 'Unknown',
            'area' => $property->area->name ?? 'Unknown',
            'city' => $property->area->city->name ?? 'Unknown',
        ]);

        $this->clearSavedSearchMatchCache($property);

        Log::info('✅ Property removal cleanup completed', [
            'event' => $event,
            'property_id' => $property->id,
            'action' => 'Cache cleared for all saved search matches'
        ]);
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        $this->triggerSavedSearchMatching($property, 'restored');
    }

    /**
     * Clear saved search match cache when property is deleted
     */
    private function clearSavedSearchMatchCache(Property $property): void
    {
        try {
            // Method 1: Try to clear cache using pattern (if using Redis)
            $clearedCaches = $this->clearCacheByPattern();

            // Method 2: Fallback to individual cache clearing
            if ($clearedCaches === 0) {
                $clearedCaches = $this->clearCacheIndividually();
            }

            Log::info("🧹 Cache cleanup: Cleared {$clearedCaches} cached saved search results", [
                'property_id' => $property->id,
                'cleared_cache_entries' => $clearedCaches,
                'method' => $clearedCaches > 100 ? 'pattern_based' : 'individual_keys'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to clear saved search match cache after property deletion', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Clear cache using pattern matching (Redis only)
     */
    private function clearCacheByPattern(): int
    {
        try {
            // Check if we're using Redis cache
            if (config('cache.default') === 'redis') {
                $redis = Cache::getRedis();
                $keys = $redis->keys('*saved_search_matches_*');

                if (!empty($keys)) {
                    $redis->del($keys);
                    return count($keys);
                }
            }
        } catch (\Exception $e) {
            // Silently fail and fall back to individual clearing
            Log::debug('Pattern-based cache clearing failed, falling back to individual clearing', [
                'error' => $e->getMessage()
            ]);
        }

        return 0;
    }

    /**
     * Clear cache by iterating through saved searches individually
     */
    private function clearCacheIndividually(): int
    {
        $savedSearches = SavedSearch::active()->get();
        $clearedCaches = 0;

        foreach ($savedSearches as $search) {
            // Clear cache for different limits that might be used
            $limits = [10, 20, 50, 100]; // Common limits used in the system

            foreach ($limits as $limit) {
                $cacheKey = "saved_search_matches_{$search->id}_{$limit}";
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    $clearedCaches++;
                }
            }
        }

        return $clearedCaches;
    }

    /**
     * Trigger SavedSearch matching for properties that meet criteria
     */
    private function triggerSavedSearchMatching(Property $property, string $event): void
    {
        // Only process properties that are published and available
        if ($property->is_published && $property->status === 'available') {
            Log::info('🏠 PROPERTY OBSERVER - Triggering SavedSearch Matching', [
                'event' => $event,
                'property_id' => $property->id,
                'property_title' => $property->title,
                'property_status' => $property->status,
                'is_published' => $property->is_published,
                'property_price' => $property->price,
                'listing_type' => $property->listing_type,
                'area' => $property->area->name ?? 'Unknown',
                'city' => $property->area->city->name ?? 'Unknown',
                'created_via' => 'Observer'
            ]);

            ProcessSavedSearchMatches::dispatch($property->id);

            Log::info('✅ SavedSearch matching job dispatched from Observer', [
                'property_id' => $property->id,
                'event' => $event,
                'job_type' => 'ProcessSavedSearchMatches'
            ]);
        } else {
            Log::info('⏩ Property Observer - Skipping matching', [
                'event' => $event,
                'property_id' => $property->id,
                'property_title' => $property->title,
                'is_published' => $property->is_published,
                'status' => $property->status,
                'reason' => 'Property not published or not available'
            ]);
        }
    }
}
