<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Property;
use App\Models\Agent;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

class AgencyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    /**
     * Only show this widget to agency owners and super admins
     */
    public static function canView(): bool
    {
        $user = auth()->user();
        
        // Allow agency owners
        if ($user->user_type === 'agency_owner') {
            return true;
        }
        
        // Allow users with super_admin role
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        return false;
    }
    
    protected function getStats(): array
    {
        // Get current agency from Filament tenant
        $agency = Filament::getTenant();
        
        if (!$agency) {
            return [];
        }
        
        // Calculate stats for the agency
        $totalProperties = Property::where('agency_id', $agency->id)->count();
        $activeProperties = Property::where('agency_id', $agency->id)
            ->where('status', 'available')
            ->count();
        $propertiesSold = Property::where('agency_id', $agency->id)
            ->where('status', 'sold')
            ->count();
        $propertiesRented = Property::where('agency_id', $agency->id)
            ->where('status', 'rented')
            ->count();
            
        $totalAgents = Agent::where('agency_id', $agency->id)->count();
        $activeAgents = Agent::where('agency_id', $agency->id)
            ->where('is_available', true)
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->count();
            
        $pendingInquiries = PropertyInquiry::whereHas('property', function ($query) use ($agency) {
            $query->where('agency_id', $agency->id);
        })
        ->where('status', 'pending')
        ->count();
        
        $upcomingViewings = PropertyViewing::whereHas('property', function ($query) use ($agency) {
            $query->where('agency_id', $agency->id);
        })
        ->where('scheduled_date', '>=', now())
        ->whereIn('status', ['scheduled', 'confirmed'])
        ->count();
        
        // Calculate monthly revenue (simplified - you may want to add a revenue tracking system)
        $monthlyDeals = Property::where('agency_id', $agency->id)
            ->whereIn('status', ['sold', 'rented'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
            
        return [
            Stat::make('Total Properties', $totalProperties)
                ->description('All agency properties')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Active Listings', $activeProperties)
                ->description('Available properties')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([3, 7, 5, 12, 8, 15, 10]),
                
            Stat::make('Team Agents', $totalAgents)
                ->description($activeAgents . ' active agents')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([2, 3, 4, 5, 6, 7, 8]),
                
            Stat::make('Pending Inquiries', $pendingInquiries)
                ->description('Awaiting response')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color($pendingInquiries > 10 ? 'warning' : 'gray')
                ->chart([12, 8, 15, 10, 20, 5, $pendingInquiries]),
                
            Stat::make('Upcoming Viewings', $upcomingViewings)
                ->description('Scheduled viewings')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning')
                ->chart([3, 5, 2, 8, 4, 12, $upcomingViewings]),
                
            Stat::make('Monthly Deals', $monthlyDeals)
                ->description('Sold/Rented this month')
                ->descriptionIcon('heroicon-m-trophy')
                ->color($monthlyDeals > 5 ? 'success' : 'gray')
                ->chart([1, 2, 1, 3, 2, 4, $monthlyDeals]),
        ];
    }
}
