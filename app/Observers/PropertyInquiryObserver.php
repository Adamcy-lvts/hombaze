<?php

namespace App\Observers;

use App\Models\PropertyInquiry;
use App\Services\SmartSearchClaimService;
use Illuminate\Support\Facades\Log;

/**
 * PropertyInquiry Observer
 *
 * Monitors property inquiries to detect SmartSearch claims.
 * When a user contacts an agent about a property,
 * this records the contact action for claim tracking.
 */
class PropertyInquiryObserver
{
    private SmartSearchClaimService $claimService;

    public function __construct(SmartSearchClaimService $claimService)
    {
        $this->claimService = $claimService;
    }

    /**
     * Handle the PropertyInquiry "created" event.
     */
    public function created(PropertyInquiry $inquiry): void
    {
        // Only process inquiries from authenticated users
        if (!$inquiry->inquirer_id) {
            return;
        }

        // Check if user has an active SmartSearch notification for this property
        if ($this->claimService->hasActiveNotification($inquiry->property_id, $inquiry->inquirer_id)) {
            $this->recordAgentContact($inquiry);
        }
    }

    /**
     * Record the contact for SmartSearch claim tracking
     */
    private function recordAgentContact(PropertyInquiry $inquiry): void
    {
        try {
            $this->claimService->recordAgentContact(
                $inquiry->property,
                $inquiry->inquirer,
                'inquiry'
            );

            Log::info('[PropertyInquiryObserver] SmartSearch contact recorded', [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'user_id' => $inquiry->inquirer_id,
            ]);
        } catch (\Exception $e) {
            Log::error('[PropertyInquiryObserver] Failed to record SmartSearch contact', [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'user_id' => $inquiry->inquirer_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
