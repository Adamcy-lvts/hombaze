<?php

namespace App\Observers;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use App\Models\Property;
use App\Models\SmartSearch;
use App\Models\SmartSearchMatch;
use App\Jobs\ProcessSmartSearchMatches;
use App\Services\SmartSearchCascadeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PropertyObserver
{
    /**
     * Handle the Property "updating" event.
     *
     * @throws ValidationException
     */
    public function updating(Property $property): void
    {
        if ($property->isDirty('is_published') && $property->is_published) {
            // Check if property has any images (gallery or featured)
            if (!$property->hasGallery() && $property->getMedia('featured')->isEmpty()) {
                throw ValidationException::withMessages([
                    'is_published' => 'Property cannot be published to be visible until property images are uploaded.',
                ]);
            }
        }

        // If property is being published, re-evaluate moderation status
        // Only if moderation_status wasn't manually set (e.g. by admin)
        if ($property->isDirty('is_published') && $property->is_published && !$property->isDirty('moderation_status')) {
            if ($property->requiresModeration()) {
                $property->moderation_status = 'pending';
            } else {
                 // If verified, we can auto-approve
                $property->moderation_status = 'approved';
            }
        }
    }
    /**
     * Handle the Property "creating" event.
     * 
     * Note: We no longer force is_published = false here because:
     * 1. The CreateProperty page now checks for images in form data before creation
     * 2. This allows properties with images to be published directly for moderation
     * 3. The updating event still validates images are present before allowing publish
     */
    public function creating(Property $property): void
    {
        // Only show draft notification if property would have been published but has no images
        // This is now handled by CreateProperty, but we keep the notification logic for 
        // cases where properties are created without images (API, tinker, etc.)
        if (!$property->is_published) {
            // Property is already marked as draft, optionally show notification
            // Skip notification if it was intentionally set to false by image check logic
            return;
        }
        
        // Note: We can't check images here because Spatie Media Library hasn't attached them yet
        // So we'll let it through and trust the updating event to validate on publish attempts

        // Auto-set moderation status based on verification
        if (!$property->isDirty('moderation_status')) {
            $property->moderation_status = $property->requiresModeration() ? 'pending' : 'approved';
        }
    }

    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        $this->triggerSmartSearchMatching($property, 'created');
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        // Only trigger matching if publication status or availability changed
        if ($property->wasChanged(['is_published', 'status'])) {
            $this->triggerSmartSearchMatching($property, 'updated');
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
        Log::info("PROPERTY OBSERVER - Property {$event} - Cleaning up cached matches", [
            'event' => $event,
            'property_id' => $property->id,
            'property_title' => $property->title ?? 'Unknown',
            'area' => $property->area->name ?? 'Unknown',
            'city' => $property->area->city->name ?? 'Unknown',
        ]);

        // Skip remaining cascade matches for this property
        SmartSearchMatch::forProperty($property->id)
            ->whereIn('status', [
                SmartSearchMatch::STATUS_PENDING,
                SmartSearchMatch::STATUS_QUEUED,
            ])
            ->update(['status' => SmartSearchMatch::STATUS_SKIPPED]);

        $this->clearSmartSearchMatchCache($property);

        Log::info('Property removal cleanup completed', [
            'event' => $event,
            'property_id' => $property->id,
            'action' => 'Cache cleared and cascade matches skipped'
        ]);
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        $this->triggerSmartSearchMatching($property, 'restored');
    }

    /**
     * Clear smart search match cache when property is deleted
     */
    private function clearSmartSearchMatchCache(Property $property): void
    {
        try {
            // Method 1: Try to clear cache using pattern (if using Redis)
            $clearedCaches = $this->clearCacheByPattern();

            // Method 2: Fallback to individual cache clearing
            if ($clearedCaches === 0) {
                $clearedCaches = $this->clearCacheIndividually();
            }

            Log::info("Cache cleanup: Cleared {$clearedCaches} cached smart search results", [
                'property_id' => $property->id,
                'cleared_cache_entries' => $clearedCaches,
                'method' => $clearedCaches > 100 ? 'pattern_based' : 'individual_keys'
            ]);

        } catch (Exception $e) {
            Log::error('Failed to clear smart search match cache after property deletion', [
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
                $keys = $redis->keys('*smart_search_matches_*');

                if (!empty($keys)) {
                    $redis->del($keys);
                    return count($keys);
                }
            }
        } catch (Exception $e) {
            // Silently fail and fall back to individual clearing
            Log::debug('Pattern-based cache clearing failed, falling back to individual clearing', [
                'error' => $e->getMessage()
            ]);
        }

        return 0;
    }

    /**
     * Clear cache by iterating through smart searches individually
     */
    private function clearCacheIndividually(): int
    {
        $smartSearches = SmartSearch::active()->get();
        $clearedCaches = 0;

        foreach ($smartSearches as $search) {
            // Clear cache for different limits that might be used
            $limits = [10, 20, 50, 100]; // Common limits used in the system

            foreach ($limits as $limit) {
                $cacheKey = "smart_search_matches_{$search->id}_{$limit}";
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    $clearedCaches++;
                }
            }
        }

        return $clearedCaches;
    }

    /**
     * Trigger SmartSearch matching for properties that meet criteria
     */
    private function triggerSmartSearchMatching(Property $property, string $event): void
    {
        // Only process properties that are published and available
        if ($property->is_published && $property->status === 'available') {
            Log::info('PROPERTY OBSERVER - Triggering SmartSearch Matching', [
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

            // Dispatch matching job which will trigger the cascade
            ProcessSmartSearchMatches::dispatch($property->id);

            Log::info('SmartSearch matching job dispatched from Observer', [
                'property_id' => $property->id,
                'event' => $event,
                'job_type' => 'ProcessSmartSearchMatches'
            ]);
        } else {
            Log::info('Property Observer - Skipping matching', [
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
