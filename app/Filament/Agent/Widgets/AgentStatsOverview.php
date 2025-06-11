<?php

namespace App\Filament\Agent\Widgets;

use App\Models\Review;
use App\Models\Property;
use App\Models\PropertyView;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use Illuminate\Support\Facades\Log;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AgentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $agentProfile = $user?->agentProfile;
        
        if (!$agentProfile) {
            Log::warning('Agent stats: No agent profile found for user', [
                'user_id' => $user?->id,
                'user_email' => $user?->email,
            ]);
            
            // If no agent profile, return empty stats
            return [
                Stat::make('Total Properties', 0)
                    ->description('No agent profile found')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        Log::info('Agent stats calculation started', [
            'user_id' => $user->id,
            'agent_id' => $agentProfile->id,
            'user_email' => $user->email,
        ]);

        // Get agent's property IDs using the correct agent profile ID
        $propertyIds = Property::where('agent_id', $agentProfile->id)
            ->whereNull('agency_id')
            ->pluck('id');
            
        // Get agent properties count
        $propertiesCount = Property::where('agent_id', $agentProfile->id)
            ->whereNull('agency_id')
            ->count();

        Log::info('Agent stats: Properties count calculated', [
            'agent_id' => $agentProfile->id,
            'properties_count' => $propertiesCount,
            'property_ids' => $propertyIds->toArray(),
        ]);

        // Get active properties count
        $activePropertiesCount = Property::where('agent_id', $agentProfile->id)
            ->whereNull('agency_id')
            ->where('status', 'available')
            ->count();

        // Get total inquiries for agent's properties
        $inquiriesCount = PropertyInquiry::whereHas('property', function ($query) use ($agentProfile) {
            $query->where('agent_id', $agentProfile->id)
                ->whereNull('agency_id');
        })->count();

        // Get pending inquiries
        $pendingInquiriesCount = PropertyInquiry::whereHas('property', function ($query) use ($agentProfile) {
            $query->where('agent_id', $agentProfile->id)
                ->whereNull('agency_id');
        })->where('status', 'pending')->count();

        // Get reviews count
        $reviewsCount = Review::where('reviewable_type', 'App\\Models\\User')
            ->where('reviewable_id', $user->id)
            ->count();

        // Get average rating
        $averageRating = Review::where('reviewable_type', 'App\\Models\\User')
            ->where('reviewable_id', $user->id)
            ->avg('rating');

        // Get viewings count
        $viewingsCount = PropertyViewing::whereHas('property', function ($query) use ($agentProfile) {
            $query->where('agent_id', $agentProfile->id)
                ->whereNull('agency_id');
        })->count();

        $totalViews = PropertyView::whereIn('property_id', $propertyIds)->count();

        return [
            Stat::make('Total Properties', $propertiesCount)
                ->description('Properties you manage')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Stat::make('Active Properties', $activePropertiesCount)
                ->description('Currently available')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Inquiries', $inquiriesCount)
                ->description($pendingInquiriesCount . ' pending responses')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($pendingInquiriesCount > 0 ? 'warning' : 'info'),

            Stat::make('Reviews', $reviewsCount)
                ->description($averageRating ? number_format($averageRating, 1) . ' ★ average' : 'No reviews yet')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Property Viewings', $viewingsCount)
                ->description('Total scheduled viewings')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),

            Stat::make('Total Views', $totalViews)
                ->description('Property page views')
                ->descriptionIcon('heroicon-m-eye')
                ->color('gray'),
        ];
    }
}
