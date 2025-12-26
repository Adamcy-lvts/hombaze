<?php

namespace App\Services;

use App\Models\SmartSearch;
use App\Models\SmartSearchMatch;
use App\Models\Property;
use App\Notifications\SmartSearchMatch as SmartSearchMatchNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * SmartSearch Cascade Service
 *
 * Manages the "First Dibs" notification cascade system:
 * 1. VIP users get exclusive 3-hour window (one at a time, ordered by match score)
 * 2. After VIP cascade completes (all VIPs notified or claimed), Priority tier gets batch notification
 * 3. Then Standard tier (24hr delay after Priority)
 * 4. Finally Starter tier (48hr delay after VIP cascade)
 *
 * Claim Logic:
 * - A claim requires BOTH: viewing the property page + contacting the agent
 * - When claimed, cascade pauses for 24 hours
 * - If no activity after 24 hours, cascade resumes to next VIP
 */
class SmartSearchCascadeService
{
    // Cascade timing constants
    public const VIP_EXCLUSIVE_WINDOW_HOURS = 3;
    public const CLAIM_PAUSE_HOURS = 24;
    public const PRIORITY_DELAY_HOURS = 0; // Immediate after VIP cascade
    public const STANDARD_DELAY_HOURS = 24;
    public const STARTER_DELAY_HOURS = 48;

    private SmartSearchMatcher $matcher;

    public function __construct(SmartSearchMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * Start the cascade for a new property
     * Called when a new property is listed
     */
    public function startCascadeForProperty(Property $property): void
    {
        $this->log('info', "Starting cascade for property: {$property->title} (ID: {$property->id})");

        // Find all matching smart searches
        $matches = $this->matcher->findMatchesForProperty($property);

        if ($matches->isEmpty()) {
            $this->log('info', "No matches found for property {$property->id}");
            return;
        }

        $this->log('info', "Found {$matches->count()} matches for property {$property->id}");

        // Group matches by tier
        $vipMatches = $matches->filter(fn($m) => $m['tier'] === SmartSearch::TIER_VIP);
        $priorityMatches = $matches->filter(fn($m) => $m['tier'] === SmartSearch::TIER_PRIORITY);
        $standardMatches = $matches->filter(fn($m) => $m['tier'] === SmartSearch::TIER_STANDARD);
        $starterMatches = $matches->filter(fn($m) => $m['tier'] === SmartSearch::TIER_STARTER);

        // Create match records for all tiers
        $cascadePosition = 1;

        // VIP matches - each gets their own cascade position
        foreach ($vipMatches as $match) {
            $this->createMatchRecord($match, $cascadePosition++, SmartSearchMatch::STATUS_PENDING);
        }

        // Non-VIP matches - will be notified in batches after VIP cascade
        foreach ($priorityMatches as $match) {
            $this->createMatchRecord($match, $cascadePosition++, SmartSearchMatch::STATUS_PENDING);
        }

        foreach ($standardMatches as $match) {
            $this->createMatchRecord($match, $cascadePosition++, SmartSearchMatch::STATUS_PENDING);
        }

        foreach ($starterMatches as $match) {
            $this->createMatchRecord($match, $cascadePosition++, SmartSearchMatch::STATUS_PENDING);
        }

        // Start with first VIP if available, otherwise move to batch notifications
        if ($vipMatches->isNotEmpty()) {
            $this->notifyNextVip($property->id);
        } else {
            $this->processNonVipBatch($property->id);
        }
    }

    /**
     * Notify the next VIP user in the cascade
     */
    public function notifyNextVip(int $propertyId): bool
    {
        // Check if property is still available
        $property = Property::find($propertyId);
        if (!$property || $property->status !== 'available') {
            $this->skipRemainingMatches($propertyId, 'Property no longer available');
            return false;
        }

        // Check if there's an active exclusive window or claim pause
        $activeExclusive = SmartSearchMatch::forProperty($propertyId)
            ->vip()
            ->where(function ($q) {
                $q->exclusiveWindowActive()
                    ->orWhere('status', SmartSearchMatch::STATUS_CLAIMED);
            })
            ->first();

        if ($activeExclusive) {
            if ($activeExclusive->isClaimPauseActive()) {
                $this->log('info', "Cascade paused for property {$propertyId} - claim pause active");
                return false;
            }

            if ($activeExclusive->isInExclusiveWindow()) {
                $this->log('info', "Cascade paused for property {$propertyId} - exclusive window active");
                return false;
            }
        }

        // Find the next pending VIP match
        $nextVip = SmartSearchMatch::forProperty($propertyId)
            ->vip()
            ->pending()
            ->orderedByCascadePosition()
            ->first();

        if (!$nextVip) {
            $this->log('info', "No more VIP matches for property {$propertyId}, processing non-VIP batch");
            $this->processNonVipBatch($propertyId);
            return false;
        }

        // Send notification with exclusive window
        $this->sendVipNotification($nextVip, $property);

        return true;
    }

    /**
     * Send VIP notification with exclusive window
     */
    private function sendVipNotification(SmartSearchMatch $match, Property $property): void
    {
        $search = $match->smartSearch;
        $user = $match->user;

        if (!$search || !$user) {
            $match->markAsSkipped();
            return;
        }

        // Calculate exclusive window end time
        $exclusiveUntil = now()->addHours(self::VIP_EXCLUSIVE_WINDOW_HOURS);

        // Get notification channels for VIP
        $channels = $search->getNotificationChannels();

        // Send the notification
        $user->notify(new SmartSearchMatchNotification(
            $search,
            collect([$property]),
            $match->match_score,
            true, // isVipExclusive
            $exclusiveUntil
        ));

        // Update match record
        $match->markAsNotified($channels, $exclusiveUntil);

        // Update search stats
        $search->incrementMatchesSent(1);

        $this->log('info', "VIP notification sent to user {$user->id} for property {$property->id}, exclusive until {$exclusiveUntil}");
    }

    /**
     * Process non-VIP batch notifications (Priority, Standard, Starter)
     */
    public function processNonVipBatch(int $propertyId): void
    {
        $property = Property::find($propertyId);
        if (!$property || $property->status !== 'available') {
            $this->skipRemainingMatches($propertyId, 'Property no longer available');
            return;
        }

        // Check if VIP cascade is complete
        $pendingVip = SmartSearchMatch::forProperty($propertyId)
            ->vip()
            ->pending()
            ->exists();

        $activeVip = SmartSearchMatch::forProperty($propertyId)
            ->vip()
            ->where(function ($q) {
                $q->where('status', SmartSearchMatch::STATUS_NOTIFIED)
                    ->orWhere('status', SmartSearchMatch::STATUS_CLAIMED);
            })
            ->where(function ($q) {
                $q->whereNotNull('exclusive_until')
                    ->where('exclusive_until', '>', now())
                    ->orWhere(function ($subQ) {
                        $subQ->whereNotNull('claim_expires_at')
                            ->where('claim_expires_at', '>', now());
                    });
            })
            ->exists();

        if ($pendingVip || $activeVip) {
            $this->log('info', "VIP cascade still active for property {$propertyId}, delaying non-VIP batch");
            return;
        }

        // Process each tier in order
        $this->sendTierBatchNotifications($propertyId, SmartSearch::TIER_PRIORITY);
        $this->sendTierBatchNotifications($propertyId, SmartSearch::TIER_STANDARD);
        $this->sendTierBatchNotifications($propertyId, SmartSearch::TIER_STARTER);
    }

    /**
     * Send batch notifications for a specific tier
     */
    private function sendTierBatchNotifications(int $propertyId, string $tier): void
    {
        $property = Property::find($propertyId);
        if (!$property) {
            return;
        }

        $pendingMatches = SmartSearchMatch::forProperty($propertyId)
            ->forTier($tier)
            ->pending()
            ->with(['smartSearch', 'user'])
            ->get();

        foreach ($pendingMatches as $match) {
            $search = $match->smartSearch;
            $user = $match->user;

            if (!$search || !$user) {
                $match->markAsSkipped();
                continue;
            }

            $channels = $search->getNotificationChannels();

            // Send notification (no exclusive window for non-VIP)
            $user->notify(new SmartSearchMatchNotification(
                $search,
                collect([$property]),
                $match->match_score,
                false, // isVipExclusive
                null // no exclusive until
            ));

            $match->markAsNotified($channels, null);
            $search->incrementMatchesSent(1);

            $this->log('info', "{$tier} notification sent to user {$user->id} for property {$propertyId}");
        }
    }

    /**
     * Handle VIP exclusive window expiry
     * Called by scheduled job after 3 hours
     */
    public function handleExclusiveWindowExpiry(SmartSearchMatch $match): void
    {
        // Only process if still in notified status and window has expired
        if (!$match->isNotified() || $match->isInExclusiveWindow()) {
            return;
        }

        $this->log('info', "Exclusive window expired for match {$match->id}");

        // Check if user took any action
        if ($match->hasBeenActedUpon() && !$match->isFullyClaimed()) {
            // Partial action - extend pause slightly
            $this->log('info', "User partially acted on match {$match->id}, marking as expired");
            $match->markAsExpired();
        } else {
            // No action - mark as expired and move to next VIP
            $match->markAsExpired();
        }

        // Continue cascade to next VIP
        $this->notifyNextVip($match->property_id);
    }

    /**
     * Handle claim pause expiry
     * Called by scheduled job after 24 hours
     */
    public function handleClaimPauseExpiry(SmartSearchMatch $match): void
    {
        if (!$match->isClaimed() || $match->isClaimPauseActive()) {
            return;
        }

        $this->log('info', "Claim pause expired for match {$match->id}");

        // Mark as completed (claim period ended) and continue cascade
        $match->markAsCompleted();

        // Continue cascade to next VIP
        $this->notifyNextVip($match->property_id);
    }

    /**
     * Process a claim (user viewed property + contacted agent)
     */
    public function processClaim(SmartSearchMatch $match): void
    {
        if (!$match->isVipMatch()) {
            $this->log('info', "Non-VIP match {$match->id} cannot claim - ignoring");
            return;
        }

        if (!$match->isFullyClaimed()) {
            $this->log('info', "Match {$match->id} not fully claimed (need both view + contact)");
            return;
        }

        if ($match->isClaimed()) {
            $this->log('info', "Match {$match->id} already claimed");
            return;
        }

        // Mark as claimed with 24hr pause
        $match->markAsClaimed();

        $this->log('info', "Match {$match->id} claimed - cascade paused for 24 hours");

        // Pause cascade for other users of this property
        $this->pauseCascadeForProperty($match->property_id, $match->id);
    }

    /**
     * Pause cascade for a property (when someone claims)
     */
    private function pauseCascadeForProperty(int $propertyId, int $exceptMatchId): void
    {
        // All other pending VIP matches for this property should wait
        SmartSearchMatch::forProperty($propertyId)
            ->where('id', '!=', $exceptMatchId)
            ->pending()
            ->update(['status' => SmartSearchMatch::STATUS_QUEUED]);
    }

    /**
     * Skip remaining matches for a property
     */
    private function skipRemainingMatches(int $propertyId, string $reason): void
    {
        SmartSearchMatch::forProperty($propertyId)
            ->whereIn('status', [
                SmartSearchMatch::STATUS_PENDING,
                SmartSearchMatch::STATUS_QUEUED
            ])
            ->update(['status' => SmartSearchMatch::STATUS_SKIPPED]);

        $this->log('info', "Skipped remaining matches for property {$propertyId}: {$reason}");
    }

    /**
     * Create a match record
     */
    private function createMatchRecord(array $matchData, int $cascadePosition, string $status): SmartSearchMatch
    {
        return SmartSearchMatch::create([
            'smart_search_id' => $matchData['smart_search']->id,
            'property_id' => $matchData['property']->id,
            'user_id' => $matchData['smart_search']->user_id,
            'match_score' => $matchData['score'],
            'tier' => $matchData['tier'],
            'status' => $status,
            'match_reasons' => $matchData['match_reasons'] ?? [],
            'cascade_position' => $cascadePosition,
        ]);
    }

    /**
     * Get cascade status for a property
     */
    public function getCascadeStatus(int $propertyId): array
    {
        $matches = SmartSearchMatch::forProperty($propertyId)
            ->with(['smartSearch', 'user'])
            ->get();

        $vipMatches = $matches->where('tier', SmartSearch::TIER_VIP);
        $currentVip = $vipMatches->firstWhere('status', SmartSearchMatch::STATUS_NOTIFIED);
        $claimedVip = $vipMatches->firstWhere('status', SmartSearchMatch::STATUS_CLAIMED);

        return [
            'property_id' => $propertyId,
            'total_matches' => $matches->count(),
            'vip_matches' => $vipMatches->count(),
            'vip_pending' => $vipMatches->where('status', SmartSearchMatch::STATUS_PENDING)->count(),
            'vip_notified' => $vipMatches->where('status', SmartSearchMatch::STATUS_NOTIFIED)->count(),
            'vip_claimed' => $vipMatches->where('status', SmartSearchMatch::STATUS_CLAIMED)->count(),
            'vip_expired' => $vipMatches->where('status', SmartSearchMatch::STATUS_EXPIRED)->count(),
            'current_exclusive_user' => $currentVip?->user?->name,
            'exclusive_until' => $currentVip?->exclusive_until,
            'claimed_by' => $claimedVip?->user?->name,
            'claim_expires_at' => $claimedVip?->claim_expires_at,
            'non_vip_pending' => $matches->where('tier', '!=', SmartSearch::TIER_VIP)
                ->where('status', SmartSearchMatch::STATUS_PENDING)->count(),
            'cascade_complete' => $matches->whereIn('status', [
                SmartSearchMatch::STATUS_PENDING,
                SmartSearchMatch::STATUS_QUEUED,
                SmartSearchMatch::STATUS_NOTIFIED
            ])->isEmpty(),
        ];
    }

    /**
     * Check and process all expired exclusive windows
     * Called by scheduled job
     */
    public function processExpiredExclusiveWindows(): int
    {
        $expiredMatches = SmartSearchMatch::where('status', SmartSearchMatch::STATUS_NOTIFIED)
            ->whereNotNull('exclusive_until')
            ->where('exclusive_until', '<=', now())
            ->get();

        $processed = 0;
        foreach ($expiredMatches as $match) {
            $this->handleExclusiveWindowExpiry($match);
            $processed++;
        }

        return $processed;
    }

    /**
     * Check and process all expired claim pauses
     * Called by scheduled job
     */
    public function processExpiredClaimPauses(): int
    {
        $expiredClaims = SmartSearchMatch::where('status', SmartSearchMatch::STATUS_CLAIMED)
            ->whereNotNull('claim_expires_at')
            ->where('claim_expires_at', '<=', now())
            ->get();

        $processed = 0;
        foreach ($expiredClaims as $match) {
            $this->handleClaimPauseExpiry($match);
            $processed++;
        }

        return $processed;
    }

    /**
     * Log cascade activity
     */
    private function log(string $level, string $message, array $context = []): void
    {
        Log::channel('daily')->{$level}("[SmartSearchCascade] {$message}", $context);
    }
}
