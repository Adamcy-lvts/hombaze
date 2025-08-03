<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Lease;
use App\Models\RentPayment;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TenantOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $tenant = $user->tenant ?? null;

        if (!$tenant) {
            return [
                Stat::make('No Tenant Profile', 'Please contact support')
                    ->description('Unable to load tenant information')
                    ->color('danger'),
            ];
        }

        // Get current active lease
        $currentLease = Lease::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        // Calculate lease time remaining
        $leaseTimeRemaining = null;
        $leaseEndingSoon = false;
        if ($currentLease) {
            $daysRemaining = now()->diffInDays($currentLease->end_date, false);
            if ($daysRemaining > 0) {
                if ($daysRemaining <= 30) {
                    $leaseTimeRemaining = $daysRemaining . ' days remaining';
                    $leaseEndingSoon = true;
                } elseif ($daysRemaining <= 365) {
                    $leaseTimeRemaining = floor($daysRemaining / 30) . ' months remaining';
                } else {
                    $leaseTimeRemaining = floor($daysRemaining / 365) . ' years remaining';
                }
            } else {
                $leaseTimeRemaining = 'Lease expired';
                $leaseEndingSoon = true;
            }
        }

        // Get payment statistics
        $pendingPayments = RentPayment::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->count();

        $totalPaid = RentPayment::where('tenant_id', $tenant->id)
            ->where('status', 'paid')
            ->sum('amount');

        // Get maintenance request statistics
        $openMaintenanceRequests = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'in_progress', 'scheduled'])
            ->count();

        return [
            Stat::make('Current Lease Status', $currentLease ? 'Active' : 'No Active Lease')
                ->description($leaseTimeRemaining ?: 'No active lease found')
                ->descriptionIcon($currentLease ? 'heroicon-m-home' : 'heroicon-m-exclamation-triangle')
                ->color($currentLease ? ($leaseEndingSoon ? 'warning' : 'success') : 'gray'),

            Stat::make('Pending Payments', $pendingPayments)
                ->description($pendingPayments > 0 ? 'Payments due' : 'All payments up to date')
                ->descriptionIcon($pendingPayments > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($pendingPayments > 0 ? 'danger' : 'success'),

            Stat::make('Total Paid', '₦' . number_format($totalPaid, 0))
                ->description('Lifetime rent payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Open Maintenance Requests', $openMaintenanceRequests)
                ->description($openMaintenanceRequests > 0 ? 'Requests in progress' : 'No open requests')
                ->descriptionIcon($openMaintenanceRequests > 0 ? 'heroicon-m-wrench-screwdriver' : 'heroicon-m-check-circle')
                ->color($openMaintenanceRequests > 0 ? 'warning' : 'success'),
        ];
    }
}
