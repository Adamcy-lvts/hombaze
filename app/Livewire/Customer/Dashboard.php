<?php

namespace App\Livewire\Customer;

use Exception;
use Log;
use App\Models\SavedProperty;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use App\Models\Property;
use App\Services\SimpleRecommendationEngine;
use Livewire\Component;

class Dashboard extends Component
{
    public function getStatsProperty()
    {
        $userId = auth()->id();

        return [
            'saved_properties' => [
                'total' => SavedProperty::where('user_id', $userId)->count(),
                'change' => '+4 this month',
                'trend' => 'up'
            ],
            'active_inquiries' => [
                'total' => PropertyInquiry::where('inquirer_id', $userId)
                    ->whereIn('status', ['new', 'contacted'])
                    ->count(),
                'change' => '0 pending',
                'trend' => 'neutral'
            ],
            'property_views' => [
                'total' => PropertyViewing::where('inquirer_id', $userId)
                    ->whereBetween('created_at', [now()->startOfWeek(), now()])
                    ->count(),
                'change' => '+7 this week',
                'trend' => 'up'
            ],
            'scheduled_viewings' => [
                'total' => PropertyViewing::where('inquirer_id', $userId)
                    ->where('status', 'scheduled')
                    ->count(),
                'change' => '0 this week',
                'trend' => 'neutral'
            ]
        ];
    }

    public function getRecentActivityProperty()
    {
        $userId = auth()->id();
        $activities = collect();

        // Recent saved properties
        $savedProperties = SavedProperty::where('user_id', $userId)
            ->with('property')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($saved) {
                return [
                    'type' => 'saved',
                    'title' => 'Saved property',
                    'description' => $saved->property->title ?? 'Property',
                    'time' => $saved->created_at,
                    'icon' => 'heart'
                ];
            });

        // Recent inquiries
        $inquiries = PropertyInquiry::where('inquirer_id', $userId)
            ->with('property')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($inquiry) {
                return [
                    'type' => 'inquiry',
                    'title' => 'Sent inquiry',
                    'description' => $inquiry->property->title ?? 'Property',
                    'time' => $inquiry->created_at,
                    'icon' => 'chat-bubble-left-ellipsis'
                ];
            });

        return $activities
            ->merge($savedProperties)
            ->merge($inquiries)
            ->sortByDesc('time')
            ->take(5);
    }

    public function getRecommendedPropertiesProperty()
    {
        try {
            $engine = new SimpleRecommendationEngine();
            return $engine->getRecommendationsForUser(auth()->user(), 6);
        } catch (Exception $e) {
            Log::error('Dashboard recommendation error: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);

            // Fallback to recent properties
            return Property::with(['propertyType', 'area.city', 'media'])
                ->where('status', 'available')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
        }
    }

    public function getProfileCompletionProperty()
    {
        $user = auth()->user();
        $completedFields = 0;
        $totalFields = 6;

        // Check basic profile fields
        if ($user->name) $completedFields++;
        if ($user->email) $completedFields++;
        if ($user->phone) $completedFields++;
        if ($user->address) $completedFields++;

        // Check customer profile
        if ($user->customerProfile) {
            $profile = $user->customerProfile;

            // Check if location preferences are set
            $locations = $profile->preferred_locations ?? [];
            if (!empty($locations)) $completedFields++;

            // Check if property types are set
            $preferredTypes = $profile->preferred_property_types;
            if (!empty($preferredTypes) && is_array($preferredTypes)) $completedFields++;
        }

        return [
            'percentage' => round(($completedFields / $totalFields) * 100),
            'completed' => $completedFields,
            'total' => $totalFields
        ];
    }

    public function render()
    {
        return view('livewire.customer.dashboard', [
            'stats' => $this->stats,
            'recentActivity' => $this->recentActivity,
            'recommendedProperties' => $this->recommendedProperties,
            'profileCompletion' => $this->profileCompletion,
        ])->layout('layouts.livewire-property');
    }
}