<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Property;
use App\Models\SavedSearch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Services\SavedSearchMatcher;
use Illuminate\Queue\SerializesModels;
use App\Notifications\SavedSearchMatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessSavedSearchMatches implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $matchCount = 0;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?int $propertyId = null,
        public ?int $savedSearchId = null,
        public bool $newPropertiesOnly = false
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('🚀 SAVEDSEARCH MATCHING JOB STARTED', [
            'job_type' => $this->getJobType(),
            'property_id' => $this->propertyId,
            'saved_search_id' => $this->savedSearchId,
            'new_properties_only' => $this->newPropertiesOnly,
            'timestamp' => now()
        ]);


        $matcher = new SavedSearchMatcher();
        $result = false;

        try {
            if ($this->propertyId) {
                $result = $this->processSpecificProperty($matcher);
            } elseif ($this->savedSearchId) {
                $result = $this->processSpecificSearch($matcher);
            } elseif ($this->newPropertiesOnly) {
                $result = $this->processNewProperties($matcher);
            } else {
                $result = $this->processAllMatches($matcher);
            }

            Log::info('🏁 SAVEDSEARCH MATCHING JOB COMPLETED', [
                'job_type' => $this->getJobType(),
                'result' => $result ? 'SUCCESS' : 'FAILURE',
                'timestamp' => now()
            ]);


        } catch (\Exception $e) {
            Log::error('❌ SAVEDSEARCH MATCHING JOB FAILED', [
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
        if ($this->savedSearchId) return 'specific_search';
        if ($this->newPropertiesOnly) return 'new_properties';
        return 'all_matches';
    }

    private function processSpecificProperty(SavedSearchMatcher $matcher): bool
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

        $matches = $matcher->findMatchesForProperty($property);
        return $this->sendNotifications($matches);
    }

    private function processSpecificSearch(SavedSearchMatcher $matcher): bool
    {
        $search = SavedSearch::find($this->savedSearchId);

        if (!$search) {
            Log::error("SavedSearch with ID {$this->savedSearchId} not found");
            return false;
        }


        $matches = $matcher->findMatchesForSavedSearch($search);
        $this->matchCount = $matches->count();


        return $this->sendNotifications($matches);
    }

    private function processNewProperties(SavedSearchMatcher $matcher): bool
    {
        $cutoff = Carbon::now()->subDay();
        $properties = Property::where('is_published', true)
            ->where('status', 'available')
            ->where('created_at', '>=', $cutoff)
            ->get();

        Log::info('📅 Processing new properties', [
            'cutoff_time' => $cutoff,
            'properties_found' => $properties->count()
        ]);

        $totalMatches = collect();
        foreach ($properties as $property) {
            $matches = $matcher->findMatchesForProperty($property);
            $totalMatches = $totalMatches->merge($matches);
        }

        return $this->sendNotifications($totalMatches);
    }

    private function processAllMatches(SavedSearchMatcher $matcher): bool
    {
        $properties = Property::where('is_published', true)
            ->where('status', 'available')
            ->get();

        $totalMatches = collect();
        foreach ($properties as $property) {
            $matches = $matcher->findMatchesForProperty($property);
            $totalMatches = $totalMatches->merge($matches);
        }

        return $this->sendNotifications($totalMatches);
    }

    private function sendNotifications(Collection $matches): bool
    {
        if ($matches->isEmpty()) {
            Log::info('📭 No matches found to send notifications for');
            return true;
        }

        $sentCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($matches as $match) {
            try {
                $user = $match['saved_search']->user;
                $notification = new SavedSearchMatch(
                    $match['saved_search'],
                    \Illuminate\Database\Eloquent\Collection::make([$match['property']]),
                    $match['score']
                );

                // Check if this exact notification has already been sent recently (within last 24 hours)
                $uniqueId = $notification->uniqueId();
                $recentNotification = $user->notifications()
                    ->where('type', SavedSearchMatch::class)
                    ->where('created_at', '>', now()->subDay())
                    ->whereJsonContains('data->unique_id', $uniqueId)
                    ->exists();

                if ($recentNotification) {
                    $skippedCount++;
                    Log::info('⏭️ Skipping duplicate notification', [
                        'user_id' => $user->id,
                        'search_id' => $match['saved_search']->id,
                        'property_id' => $match['property']->id,
                        'unique_id' => $uniqueId
                    ]);
                    continue;
                }

                $user->notify($notification);
                $sentCount++;

                Log::info('📧 Notification sent', [
                    'user_id' => $user->id,
                    'search_id' => $match['saved_search']->id,
                    'property_id' => $match['property']->id,
                    'score' => $match['score'],
                    'unique_id' => $uniqueId
                ]);

            } catch (\Exception $e) {
                $errorCount++;
                Log::error('❌ Failed to send notification', [
                    'search_id' => $match['saved_search']->id,
                    'property_id' => $match['property']->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('📊 Notification Summary', [
            'total_matches' => $matches->count(),
            'sent_successfully' => $sentCount,
            'skipped_duplicates' => $skippedCount,
            'errors' => $errorCount
        ]);

        return $errorCount === 0;
    }

}
