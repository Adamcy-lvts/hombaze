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
 * Process VIP Exclusive Window Expiry
 *
 * This job checks for VIP matches where the 3-hour exclusive window has expired
 * and advances the cascade to the next VIP user.
 *
 * Should be run every 5 minutes via scheduler.
 */
class ProcessVipExclusiveExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    /**
     * Execute the job.
     */
    public function handle(SmartSearchCascadeService $cascadeService): void
    {
        Log::info('[ProcessVipExclusiveExpiry] Starting exclusive window expiry check');

        $processed = $cascadeService->processExpiredExclusiveWindows();

        Log::info("[ProcessVipExclusiveExpiry] Processed {$processed} expired exclusive windows");
    }
}
