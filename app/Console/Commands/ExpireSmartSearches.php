<?php

namespace App\Console\Commands;

use App\Models\SmartSearch;
use App\Notifications\SmartSearchExpired;
use App\Notifications\SmartSearchNoMatchExtension;
use Illuminate\Console\Command;

class ExpireSmartSearches extends Command
{
    protected $signature = 'smartsearch:expire-searches';

    protected $description = 'Mark expired SmartSearch searches and send notifications';

    public function handle(): int
    {
        $this->info('Processing expired SmartSearch searches...');

        $expiredCount = 0;
        $noMatchCount = 0;

        // Find all searches that have passed their expiry date and haven't been marked as expired
        $expiredSearches = SmartSearch::where('is_expired', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with('user')
            ->get();

        foreach ($expiredSearches as $search) {
            $search->update(['is_expired' => true]);
            $expiredCount++;

            // Check if search received no matches - offer free extension
            if (!$search->hasReceivedMatches()) {
                $metadata = $search->additional_filters ?? [];

                // Only offer extension if not already offered/granted
                if (!isset($metadata['no_match_extension_offered']) && !isset($metadata['no_match_extension_granted'])) {
                    $metadata['no_match_extension_offered'] = now()->toIso8601String();
                    $search->update(['additional_filters' => $metadata]);

                    // Send no-match extension offer
                    if ($search->user) {
                        $search->user->notify(new SmartSearchNoMatchExtension($search));
                        $noMatchCount++;
                    }
                }
            } else {
                // Search had matches, just notify it expired
                if ($search->user) {
                    $search->user->notify(new SmartSearchExpired($search));
                }
            }
        }

        $this->info("Expired {$expiredCount} searches. Sent {$noMatchCount} no-match extension offers.");

        return self::SUCCESS;
    }
}
