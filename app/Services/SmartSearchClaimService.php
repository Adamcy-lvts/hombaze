<?php

namespace App\Services;

use App\Models\SmartSearch;
use App\Models\SmartSearchMatch;
use App\Models\Property;
use App\Models\PropertyView;
use App\Models\PropertyInquiry;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * SmartSearch Claim Service
 *
 * Handles claim detection for the First Dibs system.
 * A valid claim requires BOTH actions within the exclusive window:
 * 1. User viewed the property page
 * 2. User contacted the agent (via inquiry, phone reveal, or WhatsApp)
 *
 * This service is called by observers when views or contacts are recorded.
 */
class SmartSearchClaimService
{
    private SmartSearchCascadeService $cascadeService;

    public function __construct(SmartSearchCascadeService $cascadeService)
    {
        $this->cascadeService = $cascadeService;
    }

    /**
     * Record a property view from a SmartSearch notification
     * Called when a user clicks through from a notification
     */
    public function recordPropertyView(Property $property, User $user, ?int $matchId = null): void
    {
        $this->log('info', "Recording property view for user {$user->id} on property {$property->id}");

        // Find the relevant SmartSearch match for this user/property
        $match = $this->findActiveMatch($property->id, $user->id, $matchId);

        if (!$match) {
            $this->log('info', "No active SmartSearch match found for user {$user->id} on property {$property->id}");
            return;
        }

        // Record the view
        $match->recordPropertyView();

        // Create or update PropertyView with SmartSearch source
        PropertyView::updateOrCreate(
            [
                'property_id' => $property->id,
                'user_id' => $user->id,
                'smart_search_match_id' => $match->id,
            ],
            [
                'source' => 'smartsearch',
                'viewed_at' => now(),
            ]
        );

        $this->log('info', "Property view recorded for match {$match->id}");

        // Check if this completes a claim
        $this->checkAndProcessClaim($match);
    }

    /**
     * Record an agent contact from a SmartSearch lead
     * Called when a user contacts an agent via inquiry, phone, or WhatsApp
     */
    public function recordAgentContact(Property $property, User $user, string $contactMethod = 'inquiry'): void
    {
        $this->log('info', "Recording agent contact for user {$user->id} on property {$property->id} via {$contactMethod}");

        // Find the relevant SmartSearch match for this user/property
        $match = $this->findActiveMatch($property->id, $user->id);

        if (!$match) {
            $this->log('info', "No active SmartSearch match found for user {$user->id} on property {$property->id}");
            return;
        }

        // Record the contact
        $match->recordAgentContact();

        $this->log('info', "Agent contact recorded for match {$match->id}");

        // Check if this completes a claim
        $this->checkAndProcessClaim($match);
    }

    /**
     * Find an active SmartSearch match for a user and property
     */
    private function findActiveMatch(int $propertyId, int $userId, ?int $specificMatchId = null): ?SmartSearchMatch
    {
        $query = SmartSearchMatch::forProperty($propertyId)
            ->forUser($userId)
            ->whereIn('status', [
                SmartSearchMatch::STATUS_NOTIFIED,
                SmartSearchMatch::STATUS_CLAIMED,
            ]);

        // If a specific match ID is provided, use it
        if ($specificMatchId) {
            return $query->where('id', $specificMatchId)->first();
        }

        // Otherwise, find the most recent active match
        return $query->orderByDesc('notified_at')->first();
    }

    /**
     * Check if a match has been fully claimed and process it
     */
    private function checkAndProcessClaim(SmartSearchMatch $match): void
    {
        // Only VIP matches can claim
        if (!$match->isVipMatch()) {
            $this->log('info', "Match {$match->id} is not VIP tier - cannot claim");
            return;
        }

        // Check if already claimed
        if ($match->isClaimed()) {
            $this->log('info', "Match {$match->id} already claimed");
            return;
        }

        // Check if still in exclusive window
        if (!$match->isInExclusiveWindow()) {
            $this->log('info', "Match {$match->id} exclusive window has expired - cannot claim");
            return;
        }

        // Check if fully claimed (both view and contact)
        if (!$match->isFullyClaimed()) {
            $missingAction = !$match->property_viewed ? 'view' : 'contact';
            $this->log('info', "Match {$match->id} missing {$missingAction} for claim");
            return;
        }

        // Process the claim
        $this->cascadeService->processClaim($match);

        $this->log('info', "Claim processed for match {$match->id}");
    }

    /**
     * Check if a user has any pending SmartSearch notifications for a property
     */
    public function hasActiveNotification(int $propertyId, int $userId): bool
    {
        return SmartSearchMatch::forProperty($propertyId)
            ->forUser($userId)
            ->whereIn('status', [
                SmartSearchMatch::STATUS_NOTIFIED,
            ])
            ->exists();
    }

    /**
     * Check if a user is currently in an exclusive window for a property
     */
    public function isInExclusiveWindow(int $propertyId, int $userId): bool
    {
        return SmartSearchMatch::forProperty($propertyId)
            ->forUser($userId)
            ->where('status', SmartSearchMatch::STATUS_NOTIFIED)
            ->exclusiveWindowActive()
            ->exists();
    }

    /**
     * Get claim status for a user and property
     */
    public function getClaimStatus(int $propertyId, int $userId): array
    {
        $match = SmartSearchMatch::forProperty($propertyId)
            ->forUser($userId)
            ->whereIn('status', [
                SmartSearchMatch::STATUS_NOTIFIED,
                SmartSearchMatch::STATUS_CLAIMED,
            ])
            ->first();

        if (!$match) {
            return [
                'has_match' => false,
                'is_vip' => false,
                'can_claim' => false,
                'property_viewed' => false,
                'agent_contacted' => false,
                'is_claimed' => false,
                'exclusive_until' => null,
                'exclusive_remaining_minutes' => null,
            ];
        }

        return [
            'has_match' => true,
            'is_vip' => $match->isVipMatch(),
            'can_claim' => $match->isVipMatch() && $match->isInExclusiveWindow() && !$match->isClaimed(),
            'property_viewed' => $match->property_viewed,
            'agent_contacted' => $match->agent_contacted,
            'is_claimed' => $match->isClaimed(),
            'exclusive_until' => $match->exclusive_until,
            'exclusive_remaining_minutes' => $match->getExclusiveTimeRemaining(),
            'claim_expires_at' => $match->claim_expires_at,
        ];
    }

    /**
     * Check for claims from property inquiries
     * Called when a new inquiry is created
     */
    public function checkInquiryForClaim(PropertyInquiry $inquiry): void
    {
        if (!$inquiry->inquirer_id) {
            return;
        }

        $this->recordAgentContact(
            $inquiry->property,
            $inquiry->inquirer,
            'inquiry'
        );
    }

    /**
     * Check for claims from phone reveals
     * Called when a user reveals agent phone number
     */
    public function recordPhoneReveal(Property $property, User $user): void
    {
        $this->recordAgentContact($property, $user, 'phone_reveal');
    }

    /**
     * Check for claims from WhatsApp clicks
     * Called when a user clicks WhatsApp link
     */
    public function recordWhatsAppContact(Property $property, User $user): void
    {
        $this->recordAgentContact($property, $user, 'whatsapp');
    }

    /**
     * Get all active exclusive windows for a property
     */
    public function getActiveExclusiveWindows(int $propertyId): array
    {
        return SmartSearchMatch::forProperty($propertyId)
            ->vip()
            ->where('status', SmartSearchMatch::STATUS_NOTIFIED)
            ->exclusiveWindowActive()
            ->with('user')
            ->get()
            ->map(function ($match) {
                return [
                    'user_id' => $match->user_id,
                    'user_name' => $match->user?->name,
                    'exclusive_until' => $match->exclusive_until,
                    'minutes_remaining' => $match->getExclusiveTimeRemaining(),
                    'property_viewed' => $match->property_viewed,
                    'agent_contacted' => $match->agent_contacted,
                ];
            })
            ->toArray();
    }

    /**
     * Log claim activity
     */
    private function log(string $level, string $message, array $context = []): void
    {
        Log::channel('daily')->{$level}("[SmartSearchClaim] {$message}", $context);
    }
}
