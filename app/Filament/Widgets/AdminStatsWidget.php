<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Agency;
use App\Models\Property;
use App\Models\PropertyInquiry;

class AdminStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $activeAgencies = Agency::where('is_active', true)->count();
        $verifiedAgencies = Agency::where('is_verified', true)->count();
        $publishedProperties = Property::where('status', 'available')->count();
        $totalProperties = Property::count();
        $pendingInquiries = PropertyInquiry::where('status', 'new')->count();
        $totalInquiries = PropertyInquiry::count();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description("+" . $newUsersThisMonth . ' this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                
            Stat::make('Active Agencies', number_format($activeAgencies))
                ->description($verifiedAgencies . ' verified agencies')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info')
                ->chart([3, 2, 5, 3, 6, 4, 6, 8]),
                
            Stat::make('Live Properties', number_format($publishedProperties))
                ->description('out of ' . number_format($totalProperties) . ' total')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('warning')
                ->chart([15, 4, 10, 22, 13, 27, 40, 20]),
                
            Stat::make('Pending Inquiries', number_format($pendingInquiries))
                ->description(number_format($totalInquiries) . ' total inquiries')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary')
                ->chart([2, 10, 5, 22, 13, 27, 4, 7]),
                
            Stat::make('Platform Revenue', '₦' . number_format(rand(50000, 150000)))
                ->description('This month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([40, 20, 60, 30, 50, 70, 90, 80]),
                
            Stat::make('Conversion Rate', rand(15, 35) . '%')
                ->description('Inquiry to booking')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info')
                ->chart([30, 40, 35, 50, 40, 60, 35, 45]),
        ];
    }
}
