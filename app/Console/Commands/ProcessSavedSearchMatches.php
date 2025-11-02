<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Property;
use App\Models\SavedSearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\SavedSearchMatcher;
use App\Notifications\SavedSearchMatch;

class ProcessSavedSearchMatches extends Command
{
    protected $signature = 'searches:process-matches
                            {--property= : Process matches for specific property ID}
                            {--search= : Process matches for specific saved search ID}
                            {--new-properties : Only process properties created in last 24 hours}
                            {--batch-size=50 : Number of items to process in each batch}';

    protected $description = 'Process saved search matches and send notifications';

    private SavedSearchMatcher $matcher;

    public function __construct(SavedSearchMatcher $matcher)
    {
        parent::__construct();
        $this->matcher = $matcher;
    }

    public function handle()
    {
        Log::info('🚀 SAVEDSEARCH MATCHING JOB STARTED', [
            'job_type' => $this->getJobType(),
            'options' => [
                'property' => $this->option('property'),
                'search' => $this->option('search'),
                'new-properties' => $this->option('new-properties'),
                'batch-size' => $this->option('batch-size')
            ],
            'timestamp' => now()
        ]);

        $this->info('Starting saved search matching process...');

        $result = null;
        if ($this->option('property')) {
            $result = $this->processSpecificProperty();
        } elseif ($this->option('search')) {
            $result = $this->processSpecificSearch();
        } elseif ($this->option('new-properties')) {
            $result = $this->processNewProperties();
        } else {
            $result = $this->processAllMatches();
        }

        Log::info('🏁 SAVEDSEARCH MATCHING JOB COMPLETED', [
            'job_type' => $this->getJobType(),
            'result' => $result === Command::SUCCESS ? 'SUCCESS' : 'FAILURE',
            'timestamp' => now()
        ]);

        return $result;
    }

    private function getJobType(): string
    {
        if ($this->option('property')) return 'specific_property';
        if ($this->option('search')) return 'specific_search';
        if ($this->option('new-properties')) return 'new_properties';
        return 'all_matches';
    }

    protected function processSpecificProperty()
    {
        $propertyId = $this->option('property');
        $property = Property::find($propertyId);

        if (!$property) {
            $this->error("Property with ID {$propertyId} not found");
            return Command::FAILURE;
        }

        $this->info("🏠 Processing matches for property: {$property->title} (ID: {$propertyId})");
        $this->info("   Status: {$property->status}");
        $this->info("   Published: " . ($property->is_published ? 'Yes' : 'No'));
        $this->info("   Price: ₦" . number_format($property->price));
        $this->info("   Location: " . ($property->area->name ?? 'N/A') . ', ' . ($property->city->name ?? 'N/A'));

        Log::info("Processing SavedSearch matches for property", [
            'property_id' => $property->id,
            'property_title' => $property->title,
            'property_status' => $property->status,
            'is_published' => $property->is_published,
            'timestamp' => now()
        ]);

        $matches = $this->matcher->findMatchesForProperty($property);

        if ($matches->isEmpty()) {
            $this->info('❌ No matches found for this property');
            Log::info("No SavedSearch matches found for property", [
                'property_id' => $property->id,
                'active_searches_count' => \App\Models\SavedSearch::active()->count()
            ]);
            return Command::SUCCESS;
        }

        $this->info("✅ Found {$matches->count()} potential matches");

        $sentCount = 0;
        foreach ($matches as $match) {
            if ($this->sendMatchNotification($match)) {
                $sentCount++;
            }
        }

        $this->info("📊 Summary: {$sentCount} notifications sent out of {$matches->count()} matches");
        return Command::SUCCESS;
    }

    protected function processSpecificSearch()
    {
        $searchId = $this->option('search');
        $search = SavedSearch::find($searchId);

        if (!$search) {
            $this->error("Saved search with ID {$searchId} not found");
            return Command::FAILURE;
        }

        $this->info("Processing matches for saved search: {$search->name}");

        $matches = $this->matcher->findMatchesForSavedSearch($search);

        if ($matches->isEmpty()) {
            $this->info('No matches found for this saved search');
            return Command::SUCCESS;
        }

        $this->info("Found {$matches->count()} matches");

        $user = $search->user;
        $properties = $matches->pluck('property');

        // Send batch notification
        if ($this->shouldSendNotification($search)) {
            $user->notify(new SavedSearchMatch($search, \Illuminate\Database\Eloquent\Collection::make($properties)));
            $this->info("Sent batch notification to {$user->name}");

            // Update last alerted timestamp
            $search->update(['last_alerted_at' => now()]);
        }

        return Command::SUCCESS;
    }

    protected function processNewProperties()
    {
        $this->info('Processing matches for new properties (last 24 hours)...');

        $newProperties = Property::where('created_at', '>=', Carbon::now()->subDay())
            ->where('status', 'active')
            ->get();

        if ($newProperties->isEmpty()) {
            $this->info('No new properties found');
            return Command::SUCCESS;
        }

        $this->info("Processing {$newProperties->count()} new properties");

        $totalMatches = 0;
        $totalNotifications = 0;

        foreach ($newProperties as $property) {
            $matches = $this->matcher->findMatchesForProperty($property);
            $totalMatches += $matches->count();

            foreach ($matches as $match) {
                if ($this->sendMatchNotification($match)) {
                    $totalNotifications++;
                }
            }

            $this->line("✓ Processed {$property->title} - {$matches->count()} matches");
        }

        $this->info("\nProcessing completed:");
        $this->info("- Total matches found: {$totalMatches}");
        $this->info("- Notifications sent: {$totalNotifications}");

        return Command::SUCCESS;
    }

    protected function processAllMatches()
    {
        $this->info('Processing all saved search matches...');

        $batchSize = $this->option('batch-size');
        $activeSearches = SavedSearch::active()->with('user')->get();

        if ($activeSearches->isEmpty()) {
            $this->info('No active saved searches found');
            return Command::SUCCESS;
        }

        $this->info("Processing {$activeSearches->count()} active saved searches");

        $totalMatches = 0;
        $totalNotifications = 0;

        $bar = $this->output->createProgressBar($activeSearches->count());
        $bar->start();

        foreach ($activeSearches as $search) {
            // Skip if recently alerted (within last 4 hours for daily digest)
            if ($search->last_alerted_at && $search->last_alerted_at->isAfter(Carbon::now()->subHours(4))) {
                $bar->advance();
                continue;
            }

            $matches = $this->matcher->findMatchesForSavedSearch($search, 10);
            $totalMatches += $matches->count();

            if ($matches->isNotEmpty() && $this->shouldSendNotification($search)) {
                $user = $search->user;
                $properties = $matches->pluck('property');

                try {
                    $user->notify(new SavedSearchMatch($search, \Illuminate\Database\Eloquent\Collection::make($properties)));
                    $totalNotifications++;

                    // Update last alerted timestamp
                    $search->update(['last_alerted_at' => now()]);

                } catch (\Exception $e) {
                    $this->error("Failed to send notification for search {$search->id}: {$e->getMessage()}");
                }
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("Processing completed:");
        $this->info("- Total matches found: {$totalMatches}");
        $this->info("- Notifications sent: {$totalNotifications}");

        return Command::SUCCESS;
    }

    protected function sendMatchNotification(array $match): bool
    {
        $search = $match['saved_search'];
        $property = $match['property'];
        $user = $search->user;

        $this->info("🎯 MATCH FOUND!");
        $this->info("   Search: {$search->name} (ID: {$search->id})");
        $this->info("   Property: {$property->title} (ID: {$property->id})");
        $this->info("   Score: {$match['score']}");
        $this->info("   User: {$user->name} ({$user->email})");

        if (!$this->shouldSendNotification($search)) {
            $this->warn("   ⚠️  Notification skipped - user preferences or frequency limits");
            return false;
        }

        try {
            $this->info("   📧 Attempting to send notification...");
            $user->notify(new SavedSearchMatch($search, \Illuminate\Database\Eloquent\Collection::make([$property]), $match['score']));

            // Update last alerted timestamp
            $search->update(['last_alerted_at' => now()]);

            $this->info("   ✅ Notification sent successfully!");
            Log::info("SavedSearch Match Notification Sent", [
                'search_id' => $search->id,
                'search_name' => $search->name,
                'property_id' => $property->id,
                'property_title' => $property->title,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'match_score' => $match['score'],
                'timestamp' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            $this->error("   ❌ Failed to send notification: {$e->getMessage()}");
            Log::error("SavedSearch Match Notification Failed", [
                'search_id' => $search->id,
                'property_id' => $property->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            return false;
        }
    }

    protected function shouldSendNotification(SavedSearch $search): bool
    {
        // Check if user has notifications enabled
        $notificationSettings = $search->notification_settings ?? [];

        if (!($notificationSettings['email'] ?? true)) {
            return false; // Email notifications disabled
        }

        // Check alert frequency
        $alertFrequency = $search->alert_frequency ?? 'daily';
        $lastAlerted = $search->last_alerted_at;

        if (!$lastAlerted) {
            return true; // Never alerted before
        }

        switch ($alertFrequency) {
            case 'instant':
                return true; // Always send

            case 'daily':
                return $lastAlerted->isBefore(Carbon::now()->subDay());

            case 'weekly':
                return $lastAlerted->isBefore(Carbon::now()->subWeek());

            case 'disabled':
                return false;

            default:
                return $lastAlerted->isBefore(Carbon::now()->subDay()); // Default to daily
        }
    }
}
