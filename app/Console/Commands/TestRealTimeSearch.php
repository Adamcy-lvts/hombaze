<?php

namespace App\Console\Commands;

use App\Events\SavedSearchJobStarted;
use App\Events\SavedSearchJobProgress;
use App\Events\SavedSearchJobCompleted;
use App\Models\User;
use App\Models\SavedSearch;
use App\Jobs\ProcessSavedSearchMatches;
use Illuminate\Console\Command;

class TestRealTimeSearch extends Command
{
    protected $signature = 'test:realtime-search {userId?} {searchId?}';

    protected $description = 'Test the real-time search progress system end-to-end';

    public function handle()
    {
        $userId = $this->argument('userId') ?? 1;
        $searchId = $this->argument('searchId') ?? 1;

        // Find the user and search
        $user = User::find($userId);
        $search = SavedSearch::find($searchId);

        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }

        if (!$search) {
            $this->error("SavedSearch with ID {$searchId} not found");
            return 1;
        }

        $this->info("🧪 Testing Real-time Search Progress System");
        $this->info("👤 User: {$user->name} (ID: {$user->id})");
        $this->info("🔍 Search: {$search->name} (ID: {$search->id})");
        $this->newLine();

        $this->info("📡 Broadcasting started event...");
        SavedSearchJobStarted::dispatch(
            $user->id,
            $search->id,
            'manual_test',
            'Testing real-time search progress system...'
        );

        sleep(1);

        $this->info("📊 Broadcasting progress events...");

        // Stage 1: Analyzing
        SavedSearchJobProgress::dispatch(
            $user->id,
            $search->id,
            'searching',
            1,
            4,
            'Analyzing your search criteria...'
        );
        sleep(1);

        // Stage 2: Searching
        SavedSearchJobProgress::dispatch(
            $user->id,
            $search->id,
            'searching',
            2,
            4,
            'Searching through available properties...'
        );
        sleep(1);

        // Stage 3: Matching
        SavedSearchJobProgress::dispatch(
            $user->id,
            $search->id,
            'matching',
            3,
            4,
            'Found potential matches, analyzing compatibility...'
        );
        sleep(1);

        // Stage 4: Notifying
        SavedSearchJobProgress::dispatch(
            $user->id,
            $search->id,
            'notifying',
            4,
            4,
            'Preparing notifications...'
        );
        sleep(1);

        $this->info("✅ Broadcasting completion event...");
        SavedSearchJobCompleted::dispatch(
            $user->id,
            $search->id,
            true,
            3,
            'Test completed! Found 3 matching properties.'
        );

        $this->newLine();
        $this->info("🎉 Real-time search test completed!");
        $this->info("💡 To see the real-time progress:");
        $this->info("   1. Login as user ID {$userId}");
        $this->info("   2. Navigate to any customer page");
        $this->info("   3. The progress monitor should have appeared");
        $this->newLine();
        $this->info("🔗 Test with actual job:");
        $this->info("   php artisan test:realtime-job {$userId} {$searchId}");

        return 0;
    }
}