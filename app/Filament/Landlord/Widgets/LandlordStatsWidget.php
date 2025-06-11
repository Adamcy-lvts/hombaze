<?php

namespace App\Filament\Landlord\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\MaintenanceRequest;
use App\Models\RentPayment;
use Illuminate\Support\Facades\Auth;

class LandlordStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $landlordId = Auth::id();
        
        // Get properties count where user is the owner
        $propertiesCount = Property::whereHas('owner', function ($query) use ($landlordId) {
            $query->where('user_id', $landlordId);
        })->count();

        // Get active leases count
        $activeLeasesCount = Lease::where('landlord_id', $landlordId)
            ->where('status', 'active')
            ->count();

        // Get total tenants
        $tenantsCount = Tenant::where('landlord_id', $landlordId)
            ->where('is_active', true)
            ->count();

        // Get pending maintenance requests
        $pendingMaintenanceCount = MaintenanceRequest::where('landlord_id', $landlordId)
            ->whereIn('status', ['submitted', 'acknowledged'])
            ->count();

        // Get this month's rent collected
        $monthlyRentCollected = RentPayment::where('landlord_id', $landlordId)
            ->where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('net_amount');

        // Get overdue payments count
        $overduePaymentsCount = RentPayment::where('landlord_id', $landlordId)
            ->where('status', 'overdue')
            ->count();

        return [
            Stat::make('Total Properties', $propertiesCount)
                ->description('Properties in your portfolio')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Stat::make('Active Leases', $activeLeasesCount)
                ->description('Currently active rental agreements')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Total Tenants', $tenantsCount)
                ->description('Active tenants across all properties')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Rent Collected This Month', '₦' . number_format($monthlyRentCollected, 2))
                ->description('Total rent payments received')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pending Maintenance', $pendingMaintenanceCount)
                ->description('Maintenance requests awaiting action')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($pendingMaintenanceCount > 0 ? 'warning' : 'success'),

            Stat::make('Overdue Payments', $overduePaymentsCount)
                ->description('Late rent payments requiring attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overduePaymentsCount > 0 ? 'danger' : 'success'),
        ];
    }
}
