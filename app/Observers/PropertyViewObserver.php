<?php

namespace App\Observers;

use App\Models\PropertyView;
use App\Services\SmartSearchClaimService;
use Illuminate\Support\Facades\Log;

/**
 * PropertyView Observer
 *
 * Monitors property views to detect SmartSearch claims.
 * When a user views a property from a SmartSearch notification,
 * this records the view action for claim tracking.
 */
class PropertyViewObserver
{
    private SmartSearchClaimService $claimService;

    public function __construct(SmartSearchClaimService $claimService)
    {
        $this->claimService = $claimService;
    }

    /**
     * Handle the PropertyView "created" event.
     */
    public function created(PropertyView $view): void
    {
        // Only process views from authenticated users with SmartSearch source
        if (!$view->user_id) {
            return;
        }

        // Check if this view is from a SmartSearch notification
        if ($view->source === 'smartsearch' || $view->smart_search_match_id) {
            $this->recordSmartSearchView($view);
            return;
        }

        // Also check for any active SmartSearch match for this user/property
        // This handles cases where user navigates directly to property
        if ($this->claimService->hasActiveNotification($view->property_id, $view->user_id)) {
            $this->recordSmartSearchView($view);
        }
    }

    /**
     * Record the view for SmartSearch claim tracking
     */
    private function recordSmartSearchView(PropertyView $view): void
    {
        try {
            $this->claimService->recordPropertyView(
                $view->property,
                $view->user,
                $view->smart_search_match_id
            );

            Log::info('[PropertyViewObserver] SmartSearch view recorded', [
                'property_id' => $view->property_id,
                'user_id' => $view->user_id,
                'match_id' => $view->smart_search_match_id,
            ]);
        } catch (\Exception $e) {
            Log::error('[PropertyViewObserver] Failed to record SmartSearch view', [
                'property_id' => $view->property_id,
                'user_id' => $view->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
