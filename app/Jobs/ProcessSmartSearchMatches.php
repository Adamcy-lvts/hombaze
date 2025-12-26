<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Models\Property;
use App\Models\SmartSearch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Services\SmartSearchMatcher;
use App\Services\SmartSearchCascadeService;
use Illuminate\Queue\SerializesModels;
use App\Notifications\SmartSearchMatch as SmartSearchMatchNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessSmartSearchMatches implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $matchCount = 0;

    /**
     * Create a new job instance.
     *
     * @param int|null $propertyId - Process matches for a specific property (uses cascade)
     * @param int|null $smartSearchId - Process matches for a specific search (immediate notification)
     * @param bool $newPropertiesOnly - Only process recently listed properties (uses cascade)
     */
    public function __construct(
        public ?int $propertyId = null,
        public ?int $smartSearchId = null,
        public bool $newPropertiesOnly = false
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SmartSearchMatcher $matcher, SmartSearchCascadeService $cascadeService): void
    {
        Log::info('SMARTSEARCH MATCHING JOB STARTED', [
            'job_type' => $this->getJobType(),
            'property_id' => $this->propertyId,
            'smart_search_id' => $this->smartSearchId,
            'new_properties_only' => $this->newPropertiesOnly,
            'timestamp' => now()
        ]);

        $result = false;

        try {
            if ($this->propertyId) {
                // New property listing - use cascade system for First Dibs
                $result = $this->processSpecificProperty($matcher, $cascadeService);
            } elseif ($this->smartSearchId) {
                // User created a new search - send immediate notifications
                $result = $this->processSpecificSearch($matcher);
            } elseif ($this->newPropertiesOnly) {
                // Scheduled job for new properties - use cascade
                $result = $this->processNewProperties($matcher, $cascadeService);
            } else {
                // Full rescan - use cascade for any new matches
                $result = $this->processAllMatches($matcher, $cascadeService);
            }

            Log::info('SMARTSEARCH MATCHING JOB COMPLETED', [
                'job_type' => $this->getJobType(),
                'result' => $result ? 'SUCCESS' : 'FAILURE',
                'timestamp' => now()
            ]);

        } catch (Exception $e) {
            Log::error('SMARTSEARCH MATCHING JOB FAILED', [
                'job_type' => $this->getJobType(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    private function getJobType(): string
    {
        if ($this->propertyId) return 'specific_property';
        if ($this->smartSearchId) return 'specific_search';
        if ($this->newPropertiesOnly) return 'new_properties';
        return 'all_matches';
    }

    /**
     * Process a specific property using the cascade system
     */
    private function processSpecificProperty(SmartSearchMatcher $matcher, SmartSearchCascadeService $cascadeService): bool
    {
        $property = Property::find($this->propertyId);

        if (!$property) {
            Log::error("Property with ID {$this->propertyId} not found");
            return false;
        }

        if (!$property->is_published || $property->status !== 'available') {
            Log::info("Skipping unpublished or unavailable property", [
                'property_id' => $property->id,
                'is_published' => $property->is_published,
                'status' => $property->status
            ]);
            return false;
        }

        // Use cascade system for new property listings (First Dibs)
        $cascadeService->startCascadeForProperty($property);

        return true;
    }

    /**
     * Process a specific search with immediate notifications
     * Used when a user creates a new search to show them existing matches
     */
    private function processSpecificSearch(SmartSearchMatcher $matcher): bool
    {
        $search = SmartSearch::find($this->smartSearchId);

        if (!$search) {
            Log::error("SmartSearch with ID {$this->smartSearchId} not found");
            return false;
        }

        $matches = $matcher->findMatchesForSmartSearch($search);
        $this->matchCount = $matches->count();

        // For user-initiated search, send immediate notifications (not cascade)
        // This is for showing existing properties that match their new search
        return $this->sendImmediateNotifications($matches);
    }

    /**
     * Process new properties using the cascade system
     */
    private function processNewProperties(SmartSearchMatcher $matcher, SmartSearchCascadeService $cascadeService): bool
    {
        $cutoff = Carbon::now()->subDay();
        $properties = Property::where('is_published', true)
            ->where('status', 'available')
            ->where('created_at', '>=', $cutoff)
            ->get();

        Log::info('Processing new properties with cascade', [
            'cutoff_time' => $cutoff,
            'properties_found' => $properties->count()
        ]);

        foreach ($properties as $property) {
            $cascadeService->startCascadeForProperty($property);
        }

        return true;
    }

    /**
     * Process all matches using the cascade system
     */
    private function processAllMatches(SmartSearchMatcher $matcher, SmartSearchCascadeService $cascadeService): bool
    {
        $properties = Property::where('is_published', true)
            ->where('status', 'available')
            ->get();

        Log::info('Processing all properties with cascade', [
            'properties_found' => $properties->count()
        ]);

        foreach ($properties as $property) {
            $cascadeService->startCascadeForProperty($property);
        }

        return true;
    }

    /**
     * Send immediate notifications for user-initiated search
     * Does not use cascade - shows all existing matches at once
     */
    private function sendImmediateNotifications(Collection $matches): bool
    {
        if ($matches->isEmpty()) {
            Log::info('No matches found to send notifications for');
            return true;
        }

        $sentCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($matches as $match) {
            try {
                $user = $match['smart_search']->user;
                $notification = new SmartSearchMatchNotification(
                    $match['smart_search'],
                    \Illuminate\Database\Eloquent\Collection::make([$match['property']]),
                    $match['score'],
                    false, // Not VIP exclusive for immediate notifications
                    null   // No exclusive window
                );

                // Check if this exact notification has already been sent recently (within last 24 hours)
                $uniqueId = $notification->uniqueId();
                $recentNotification = $user->notifications()
                    ->where('type', SmartSearchMatchNotification::class)
                    ->where('created_at', '>', now()->subDay())
                    ->whereJsonContains('data->unique_id', $uniqueId)
                    ->exists();

                if ($recentNotification) {
                    $skippedCount++;
                    Log::info('Skipping duplicate notification', [
                        'user_id' => $user->id,
                        'search_id' => $match['smart_search']->id,
                        'property_id' => $match['property']->id,
                        'unique_id' => $uniqueId
                    ]);
                    continue;
                }

                $user->notify($notification);
                $sentCount++;

                // Increment matches sent counter on the search
                $match['smart_search']->incrementMatchesSent();

                Log::info('Immediate notification sent', [
                    'user_id' => $user->id,
                    'search_id' => $match['smart_search']->id,
                    'tier' => $match['smart_search']->tier,
                    'property_id' => $match['property']->id,
                    'score' => $match['score'],
                    'unique_id' => $uniqueId
                ]);

            } catch (Exception $e) {
                $errorCount++;
                Log::error('Failed to send notification', [
                    'search_id' => $match['smart_search']->id,
                    'property_id' => $match['property']->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Immediate Notification Summary', [
            'total_matches' => $matches->count(),
            'sent_successfully' => $sentCount,
            'skipped_duplicates' => $skippedCount,
            'errors' => $errorCount
        ]);

        return $errorCount === 0;
    }
}
