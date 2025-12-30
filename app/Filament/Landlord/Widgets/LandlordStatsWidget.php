<?php

namespace App\Filament\Landlord\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Property;
use App\Models\Lease;
use App\Models\RentPayment;
use Illuminate\Support\Facades\Auth;

class LandlordStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $landlordId = Auth::id();
        $now = now();

        $propertiesCount = Property::whereHas('owner', function ($query) use ($landlordId) {
            $query->where('user_id', $landlordId);
        })->count();

        // $activeLeasesCount = Lease::where('landlord_id', $landlordId)
        //     ->where('status', 'active')
        //     ->count();

        $rentCollectedThisYear = RentPayment::where('landlord_id', $landlordId)
            ->where('status', 'paid')
            ->whereYear('payment_date', $now->year)
            ->sum('net_amount');

        $expectedRentThisYear = RentPayment::where('landlord_id', $landlordId)
            ->whereYear('due_date', $now->year)
            ->sum('amount');

        // $outstandingRentThisYear = RentPayment::where('landlord_id', $landlordId)
        //     ->whereIn('status', ['pending', 'partial', 'overdue'])
        //     ->whereYear('due_date', $now->year)
        //     ->sum('amount');

        $collectionRate = $expectedRentThisYear > 0
            ? round(($rentCollectedThisYear / $expectedRentThisYear) * 100, 1)
            : 0;

        return [
            Stat::make('Total Properties', $propertiesCount)
                ->description('Properties in your portfolio')
                ->icon('heroicon-m-home')
                ->color('primary'),

            // Stat::make('Active Leases', $activeLeasesCount)
            //     ->description('Current rental agreements')
            //     ->icon('heroicon-m-document-text')
            //     ->color('success'),

            Stat::make('Rent Collected (YTD)', '₦' . number_format($rentCollectedThisYear, 0))
                ->description($now->year . ' collections · ' . $collectionRate . '% of expected')
                ->icon('heroicon-m-banknotes')
                ->color('success'),

            // Stat::make('Outstanding Rent', '₦' . number_format($outstandingRentThisYear, 0))
            //     ->description('Still due this year')
            //     ->icon('heroicon-m-clock')
            //     ->color($outstandingRentThisYear > 0 ? 'warning' : 'success'),
        ];
    }
}
