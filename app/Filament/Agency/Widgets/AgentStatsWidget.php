<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

class AgentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    /**
     * Only show this widget to agents (not agency owners)
     */
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->user_type === 'agent' && $user->agentProfile;
    }
    
    protected function getStats(): array
    {
        // Get current agency and agent
        $agency = Filament::getTenant();
        $user = auth()->user();
        
        if (!$agency || !$user->agentProfile) {
            return [];
        }
        
        $agent = $user->agentProfile;
        
        // Calculate agent-specific stats
        $myProperties = Property::where('agency_id', $agency->id)
            ->where('agent_id', $agent->id)
            ->count();
            
        $myActiveListings = Property::where('agency_id', $agency->id)
            ->where('agent_id', $agent->id)
            ->where('status', 'available')
            ->count();
            
        $myPropertiesSold = Property::where('agency_id', $agency->id)
            ->where('agent_id', $agent->id)
            ->where('status', 'sold')
            ->count();
            
        $myPropertiesRented = Property::where('agency_id', $agency->id)
            ->where('agent_id', $agent->id)
            ->where('status', 'rented')
            ->count();
            
        // My inquiries this month
        $myInquiriesThisMonth = PropertyInquiry::whereHas('property', function ($query) use ($agency, $agent) {
            $query->where('agency_id', $agency->id)
                  ->where('agent_id', $agent->id);
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
        
        // My upcoming viewings
        $myUpcomingViewings = PropertyViewing::whereHas('property', function ($query) use ($agency, $agent) {
            $query->where('agency_id', $agency->id)
                  ->where('agent_id', $agent->id);
        })
        ->where('scheduled_date', '>=', now())
        ->whereIn('status', ['scheduled', 'confirmed'])
        ->count();
        
        // My deals this month
        $myMonthlyDeals = Property::where('agency_id', $agency->id)
            ->where('agent_id', $agent->id)
            ->whereIn('status', ['sold', 'rented'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
            
        // My average rating
        $myRating = $agent->rating ?? 0;
        
        // My total reviews (using polymorphic relationship)
        $myReviews = Review::where('reviewable_type', User::class)
                          ->where('reviewable_id', $agent->user_id)
                          ->count();
        
        // Calculate commission earnings (if commission rate is available)
        $estimatedCommission = 0;
        if ($agent->commission_rate) {
            $soldProperties = Property::where('agency_id', $agency->id)
                ->where('agent_id', $agent->id)
                ->where('status', 'sold')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->sum('price');
            $estimatedCommission = ($soldProperties * $agent->commission_rate) / 100;
        }
        
        return [
            Stat::make('My Properties', $myProperties)
                ->description('Total properties assigned')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('primary')
                ->chart([2, 4, 3, 7, 5, 8, $myProperties]),
                
            Stat::make('Active Listings', $myActiveListings)
                ->description('Currently available')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([1, 3, 2, 5, 4, 6, $myActiveListings]),
                
            Stat::make('Monthly Deals', $myMonthlyDeals)
                ->description('Sold/Rented this month')
                ->descriptionIcon('heroicon-m-trophy')
                ->color($myMonthlyDeals > 2 ? 'success' : 'warning')
                ->chart([0, 1, 0, 2, 1, 3, $myMonthlyDeals]),
                
            Stat::make('My Inquiries', $myInquiriesThisMonth)
                ->description('Inquiries this month')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color($myInquiriesThisMonth > 5 ? 'info' : 'gray')
                ->chart([2, 5, 3, 8, 6, 10, $myInquiriesThisMonth]),
                
            Stat::make('Upcoming Viewings', $myUpcomingViewings)
                ->description('Scheduled viewings')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning')
                ->chart([1, 2, 1, 4, 2, 5, $myUpcomingViewings]),
                
            Stat::make('My Rating', number_format($myRating, 1))
                ->description($myReviews . ' reviews')
                ->descriptionIcon('heroicon-m-star')
                ->color($myRating >= 4.0 ? 'success' : ($myRating >= 3.0 ? 'warning' : 'danger'))
                ->chart([3.5, 3.8, 4.0, 4.2, 4.1, 4.3, $myRating]),
        ];
    }
}
