<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\Widget;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaseRenewalWidget extends Widget
{
    protected static string $view = 'filament.tenant.widgets.lease-renewal-widget';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();
        $tenant = $user->tenant ?? null;

        if (!$tenant) {
            return ['showWidget' => false];
        }

        // Get current active lease
        $currentLease = Lease::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('property')
            ->first();

        if (!$currentLease) {
            return ['showWidget' => false];
        }

        $daysUntilExpiry = now()->diffInDays($currentLease->end_date, false);
        $showRenewalOption = $daysUntilExpiry <= 90 && $daysUntilExpiry > 0; // Show 3 months before expiry

        return [
            'showWidget' => $showRenewalOption || $daysUntilExpiry <= 0,
            'lease' => $currentLease,
            'daysUntilExpiry' => $daysUntilExpiry,
            'isExpired' => $daysUntilExpiry <= 0,
            'canRenew' => $currentLease->renewal_option !== 'not_allowed',
        ];
    }
}
