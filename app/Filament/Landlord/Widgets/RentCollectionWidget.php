<?php

namespace App\Filament\Landlord\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\RentPayment;
use Illuminate\Support\Facades\Auth;

class RentCollectionWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $landlordId = Auth::id();
        
        // Get expected rent this month
        $expectedRentThisMonth = RentPayment::where('landlord_id', $landlordId)
            ->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->sum('amount');

        // Get collected rent this month
        $collectedRentThisMonth = RentPayment::where('landlord_id', $landlordId)
            ->where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('net_amount');

        // Get pending rent this month
        $pendingRentThisMonth = RentPayment::where('landlord_id', $landlordId)
            ->where('status', 'pending')
            ->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->sum('amount');

        // Calculate collection rate
        $collectionRate = $expectedRentThisMonth > 0 
            ? round(($collectedRentThisMonth / $expectedRentThisMonth) * 100, 1)
            : 0;

        return [
            Stat::make('Expected This Month', '₦' . number_format($expectedRentThisMonth, 2))
                ->description('Total rent due this month')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Collected This Month', '₦' . number_format($collectedRentThisMonth, 2))
                ->description('Rent payments received')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pending Collection', '₦' . number_format($pendingRentThisMonth, 2))
                ->description('Outstanding rent payments')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingRentThisMonth > 0 ? 'warning' : 'success'),

            Stat::make('Collection Rate', $collectionRate . '%')
                ->description('Percentage of expected rent collected')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($collectionRate >= 90 ? 'success' : ($collectionRate >= 70 ? 'warning' : 'danger')),
        ];
    }
}
