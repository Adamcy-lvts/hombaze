<?php

namespace App\Jobs;

use App\Services\SmartSearchCascadeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process Claim Pause Expiry
 *
 * This job checks for VIP claims where the 24-hour pause has expired
 * and resumes the cascade to the next VIP user.
 *
 * Should be run every 15 minutes via scheduler.
 */
class ProcessClaimPauseExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    /**
     * Execute the job.
     */
    public function handle(SmartSearchCascadeService $cascadeService): void
    {
        Log::info('[ProcessClaimPauseExpiry] Starting claim pause expiry check');

        $processed = $cascadeService->processExpiredClaimPauses();

        Log::info("[ProcessClaimPauseExpiry] Processed {$processed} expired claim pauses");
    }
}
