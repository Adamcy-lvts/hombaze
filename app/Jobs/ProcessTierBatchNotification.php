<?php

namespace App\Jobs;

use App\Models\SmartSearch;
use App\Models\SmartSearchMatch;
use App\Services\SmartSearchCascadeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process Tier Batch Notifications
 *
 * This job sends batch notifications to non-VIP tiers (Priority, Standard, Starter)
 * after the VIP cascade has completed for a property.
 *
 * Timing:
 * - Priority: Immediately after VIP cascade completes
 * - Standard: 24 hours after VIP cascade completes
 * - Starter: 48 hours after VIP cascade completes
 *
 * Should be run every 30 minutes via scheduler.
 */
class ProcessTierBatchNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    private ?int $propertyId;
    private ?string $tier;

    /**
     * Create a new job instance.
     */
    public function __construct(?int $propertyId = null, ?string $tier = null)
    {
        $this->propertyId = $propertyId;
        $this->tier = $tier;
    }

    /**
     * Execute the job.
     */
    public function handle(SmartSearchCascadeService $cascadeService): void
    {
        Log::info('[ProcessTierBatchNotification] Starting batch notification check');

        // If specific property and tier provided, process only that
        if ($this->propertyId && $this->tier) {
            $this->processSingleTier($cascadeService);
            return;
        }

        // Otherwise, find all properties with completed VIP cascades
        $this->processAllPendingBatches($cascadeService);
    }

    /**
     * Process a single tier for a specific property
     */
    private function processSingleTier(SmartSearchCascadeService $cascadeService): void
    {
        Log::info("[ProcessTierBatchNotification] Processing {$this->tier} tier for property {$this->propertyId}");

        $cascadeService->processNonVipBatch($this->propertyId);
    }

    /**
     * Process all pending batch notifications
     */
    private function processAllPendingBatches(SmartSearchCascadeService $cascadeService): void
    {
        // Find properties with pending non-VIP matches where VIP cascade is complete
        $pendingProperties = SmartSearchMatch::whereIn('tier', [
            SmartSearch::TIER_PRIORITY,
            SmartSearch::TIER_STANDARD,
            SmartSearch::TIER_STARTER,
        ])
            ->where('status', SmartSearchMatch::STATUS_PENDING)
            ->select('property_id')
            ->distinct()
            ->get()
            ->pluck('property_id');

        $processed = 0;

        foreach ($pendingProperties as $propertyId) {
            // Check if VIP cascade is complete for this property
            $hasActiveVip = SmartSearchMatch::forProperty($propertyId)
                ->vip()
                ->whereIn('status', [
                    SmartSearchMatch::STATUS_PENDING,
                    SmartSearchMatch::STATUS_NOTIFIED,
                ])
                ->where(function ($q) {
                    $q->whereNull('exclusive_until')
                        ->orWhere('exclusive_until', '>', now());
                })
                ->exists();

            $hasClaimPause = SmartSearchMatch::forProperty($propertyId)
                ->vip()
                ->where('status', SmartSearchMatch::STATUS_CLAIMED)
                ->where('claim_expires_at', '>', now())
                ->exists();

            if ($hasActiveVip || $hasClaimPause) {
                continue; // VIP cascade still active
            }

            // Get the time when VIP cascade completed
            $lastVipActivity = SmartSearchMatch::forProperty($propertyId)
                ->vip()
                ->whereIn('status', [
                    SmartSearchMatch::STATUS_EXPIRED,
                    SmartSearchMatch::STATUS_CLAIMED,
                    SmartSearchMatch::STATUS_COMPLETED,
                ])
                ->max('updated_at');

            if (!$lastVipActivity) {
                // No VIP matches, can process immediately
                $cascadeService->processNonVipBatch($propertyId);
                $processed++;
                continue;
            }

            $vipCompletedAt = \Carbon\Carbon::parse($lastVipActivity);

            // Check delays for each tier
            $this->processTierWithDelay(
                $cascadeService,
                $propertyId,
                SmartSearch::TIER_PRIORITY,
                $vipCompletedAt,
                SmartSearchCascadeService::PRIORITY_DELAY_HOURS
            );

            $this->processTierWithDelay(
                $cascadeService,
                $propertyId,
                SmartSearch::TIER_STANDARD,
                $vipCompletedAt,
                SmartSearchCascadeService::STANDARD_DELAY_HOURS
            );

            $this->processTierWithDelay(
                $cascadeService,
                $propertyId,
                SmartSearch::TIER_STARTER,
                $vipCompletedAt,
                SmartSearchCascadeService::STARTER_DELAY_HOURS
            );

            $processed++;
        }

        Log::info("[ProcessTierBatchNotification] Processed {$processed} properties");
    }

    /**
     * Process a tier if the delay has passed
     */
    private function processTierWithDelay(
        SmartSearchCascadeService $cascadeService,
        int $propertyId,
        string $tier,
        \Carbon\Carbon $vipCompletedAt,
        int $delayHours
    ): void {
        // Check if delay has passed
        if (now()->diffInHours($vipCompletedAt) < $delayHours) {
            return;
        }

        // Check if there are pending matches for this tier
        $hasPending = SmartSearchMatch::forProperty($propertyId)
            ->forTier($tier)
            ->where('status', SmartSearchMatch::STATUS_PENDING)
            ->exists();

        if (!$hasPending) {
            return;
        }

        Log::info("[ProcessTierBatchNotification] Processing {$tier} batch for property {$propertyId}");

        // Process this tier
        $cascadeService->processNonVipBatch($propertyId);
    }
}
