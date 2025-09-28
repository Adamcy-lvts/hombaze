<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\Widget;
use App\Models\Lease;
use App\Models\LeaseRenewalRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

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

        // Check if there's already a pending renewal request
        $existingRequest = LeaseRenewalRequest::where('lease_id', $currentLease->id)
            ->where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->first();

        return [
            'showWidget' => $showRenewalOption || $daysUntilExpiry <= 0,
            'lease' => $currentLease,
            'daysUntilExpiry' => $daysUntilExpiry,
            'isExpired' => $daysUntilExpiry <= 0,
            'canRenew' => $currentLease->renewal_option !== 'not_allowed',
            'hasExistingRequest' => $existingRequest !== null,
            'existingRequest' => $existingRequest,
        ];
    }

    public function requestRenewal()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            Notification::make()
                ->title('Error')
                ->body('Tenant profile not found.')
                ->danger()
                ->send();
            return;
        }

        // Get current active lease
        $currentLease = Lease::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->first();

        if (!$currentLease) {
            Notification::make()
                ->title('Error')
                ->body('No active lease found.')
                ->danger()
                ->send();
            return;
        }

        // Check if there's already a pending request
        $existingRequest = LeaseRenewalRequest::where('lease_id', $currentLease->id)
            ->where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            Notification::make()
                ->title('Request Already Submitted')
                ->body('You already have a pending renewal request for this lease.')
                ->warning()
                ->send();
            return;
        }

        // Create renewal request
        $renewalRequest = LeaseRenewalRequest::create([
            'lease_id' => $currentLease->id,
            'tenant_id' => $tenant->id,
            'landlord_id' => $tenant->landlord_id,
            'agent_id' => $tenant->agent_id, // Will be null if no agent
            'requested_start_date' => $currentLease->end_date->addDay(),
            'requested_end_date' => $currentLease->end_date->addYear(),
            'requested_monthly_rent' => $currentLease->yearly_rent,
            'tenant_message' => 'I would like to renew my lease for another term. Please let me know if you need any additional information.',
        ]);

        Notification::make()
            ->title('Renewal Request Submitted')
            ->body('Your lease renewal request has been submitted successfully. You will be notified once it has been reviewed.')
            ->success()
            ->send();

        // Refresh the component
        $this->dispatch('$refresh');
    }
}
