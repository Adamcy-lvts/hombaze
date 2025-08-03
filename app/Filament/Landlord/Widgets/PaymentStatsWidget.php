<?php

namespace App\Filament\Landlord\Widgets;

use App\Models\RentPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PaymentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $landlordId = Auth::id();

        // Get this month's data
        $thisMonth = now();
        $lastMonth = now()->subMonth();

        // Total payments this month
        $thisMonthPayments = RentPayment::where('landlord_id', $landlordId)
            ->whereMonth('payment_date', $thisMonth->month)
            ->whereYear('payment_date', $thisMonth->year)
            ->where('status', 'paid')
            ->sum('net_amount');

        $lastMonthPayments = RentPayment::where('landlord_id', $landlordId)
            ->whereMonth('payment_date', $lastMonth->month)
            ->whereYear('payment_date', $lastMonth->year)
            ->where('status', 'paid')
            ->sum('net_amount');

        // Calculate percentage change
        $paymentsChange = $lastMonthPayments > 0 
            ? (($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100 
            : 0;

        // Pending payments
        $pendingPayments = RentPayment::where('landlord_id', $landlordId)
            ->where('status', 'pending')
            ->count();

        // Overdue payments
        $overduePayments = RentPayment::where('landlord_id', $landlordId)
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['paid', 'cancelled', 'refunded'])
            ->count();

        // Total revenue this year
        $yearlyRevenue = RentPayment::where('landlord_id', $landlordId)
            ->whereYear('payment_date', $thisMonth->year)
            ->where('status', 'paid')
            ->sum('net_amount');

        return [
            Stat::make('This Month Collections', '₦' . number_format($thisMonthPayments, 0))
                ->description($paymentsChange >= 0 ? "+" . number_format($paymentsChange, 1) . '% from last month' : number_format($paymentsChange, 1) . '% from last month')
                ->descriptionIcon($paymentsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($paymentsChange >= 0 ? 'success' : 'danger'),

            Stat::make('Pending Payments', $pendingPayments)
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Overdue Payments', $overduePayments)
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overduePayments > 0 ? 'danger' : 'success'),

            Stat::make('Year-to-Date Revenue', '₦' . number_format($yearlyRevenue, 0))
                ->description('Total collections in ' . $thisMonth->year)
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }
}