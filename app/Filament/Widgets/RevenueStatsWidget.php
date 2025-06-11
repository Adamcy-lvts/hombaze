<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Property;
use App\Models\Agency;
use App\Models\PropertyInquiry;

class RevenueStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Calculate mock revenue data based on properties and inquiries
        $totalProperties = Property::count();
        $totalInquiries = PropertyInquiry::count();
        $activeAgencies = Agency::where('is_active', true)->count();
        
        // Mock calculations for demonstration
        $monthlyRevenue = $totalProperties * rand(500, 1500) + $totalInquiries * rand(100, 300);
        $avgPropertyValue = Property::avg('price') ?? 0;
        $conversionRate = $totalInquiries > 0 ? round(($totalProperties / $totalInquiries) * 100, 1) : 0;
        
        return [
            Stat::make('Monthly Revenue', '₦' . number_format($monthlyRevenue))
                ->description('Platform commission')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([40, 60, 45, 80, 65, 90, 75, 100]),
                
            Stat::make('Avg Property Value', '₦' . number_format($avgPropertyValue))
                ->description('Across all listings')
                ->descriptionIcon('heroicon-m-home')
                ->color('info')
                ->chart([100, 120, 90, 140, 110, 160, 130, 180]),
                
            Stat::make('Commission Rate', '2.5%')
                ->description('Per successful transaction')
                ->descriptionIcon('heroicon-m-percent-badge')
                ->color('warning')
                ->chart([2.5, 2.3, 2.7, 2.4, 2.6, 2.5, 2.8, 2.5]),
                
            Stat::make('Conversion Rate', $conversionRate . '%')
                ->description('Inquiries to bookings')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([15, 20, 18, 25, 22, 30, 28, 35]),
        ];
    }
}
