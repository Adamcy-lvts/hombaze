<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use App\Models\SavedProperty;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customerProfile = $user->customerProfile;

        return view('customer.dashboard', [
            'user' => $user,
            'customerProfile' => $customerProfile,
            'stats' => $this->getDashboardStats($user),
            'recentActivity' => $this->getRecentActivity($user),
            'recommendedProperties' => $this->getRecommendedProperties($user),
            'savedProperties' => $this->getSavedPropertiesPreview($user),
            'activeInquiries' => $this->getActiveInquiries($user),
            'upcomingViewings' => $this->getUpcomingViewings($user),
            'profileCompletion' => $this->calculateProfileCompletion($customerProfile),
        ]);
    }

    private function getDashboardStats($user): array
    {
        return [
            'saved_properties' => SavedProperty::where('user_id', $user->id)->count(),
            'active_inquiries' => PropertyInquiry::where('inquirer_id', $user->id)
                ->whereIn('status', ['new', 'contacted'])->count(),
            'property_views' => $user->customerProfile?->viewed_properties ?
                count($user->customerProfile->viewed_properties) : 0,
            'scheduled_viewings' => PropertyViewing::where('inquirer_id', $user->id)
                ->whereIn('status', ['scheduled', 'confirmed'])->count(),
        ];
    }

    private function getRecentActivity($user): array
    {
        $activities = [];

        // Recent property views
        if ($user->customerProfile && $user->customerProfile->viewed_properties) {
            $recentViewed = array_slice($user->customerProfile->viewed_properties, 0, 3);
            foreach ($recentViewed as $propertyId) {
                $property = Property::find($propertyId);
                if ($property) {
                    $activities[] = [
                        'type' => 'view',
                        'message' => "You viewed \"{$property->title}\"",
                        'time' => '2 hours ago', // Would be actual timestamp in real implementation
                        'link' => route('property.show', $property->slug)
                    ];
                }
            }
        }

        // Recent saved properties
        $recentSaved = SavedProperty::where('user_id', $user->id)
            ->with('property')
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentSaved as $saved) {
            if ($saved->property) {
                $activities[] = [
                    'type' => 'save',
                    'message' => "You saved \"{$saved->property->title}\"",
                    'time' => $saved->created_at->diffForHumans(),
                    'link' => route('property.show', $saved->property->slug)
                ];
            }
        }

        // Recent inquiries
        $recentInquiries = PropertyInquiry::where('inquirer_id', $user->id)
            ->with('property')
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentInquiries as $inquiry) {
            if ($inquiry->property) {
                $activities[] = [
                    'type' => 'inquiry',
                    'message' => $inquiry->isResponded() ?
                        "New response for \"{$inquiry->property->title}\"" :
                        "You inquired about \"{$inquiry->property->title}\"",
                    'time' => $inquiry->responded_at ?
                        $inquiry->responded_at->diffForHumans() :
                        $inquiry->created_at->diffForHumans(),
                    'link' => '#' // Will update when customer inquiry page is created
                ];
            }
        }

        // Sort by time and return latest 5
        return array_slice($activities, 0, 5);
    }

    private function getRecommendedProperties($user): \Illuminate\Database\Eloquent\Collection
    {
        $customerProfile = $user->customerProfile;

        if (!$customerProfile) {
            return Property::where('is_published', true)
                ->where('status', 'available')
                ->latest()
                ->take(3)
                ->get();
        }

        // Get properties matching customer preferences
        $query = Property::where('is_published', true)
            ->where('status', 'available');

        // Filter by budget
        if ($customerProfile->budget_min) {
            $query->where('price', '>=', $customerProfile->budget_min);
        }
        if ($customerProfile->budget_max) {
            $query->where('price', '<=', $customerProfile->budget_max);
        }

        // Filter by preferred locations
        if (!empty($customerProfile->preferred_locations)) {
            $query->whereIn('area_id', $customerProfile->preferred_locations);
        }

        // Filter by property types
        if (!empty($customerProfile->preferred_property_types)) {
            $query->whereIn('property_type_id', $customerProfile->preferred_property_types);
        }

        return $query->latest()->take(3)->get();
    }

    private function getSavedPropertiesPreview($user): array
    {
        $savedProperties = SavedProperty::where('user_id', $user->id)
            ->with('property.area')
            ->get();

        $locationGroups = $savedProperties->groupBy('property.area.name')
            ->map(function ($group) {
                return [
                    'location' => $group->first()->property->area->name ?? 'Unknown',
                    'count' => $group->count()
                ];
            })
            ->values()
            ->toArray();

        return [
            'total' => $savedProperties->count(),
            'locations' => $locationGroups
        ];
    }

    private function getActiveInquiries($user): \Illuminate\Database\Eloquent\Collection
    {
        return PropertyInquiry::where('inquirer_id', $user->id)
            ->with(['property', 'responder'])
            ->whereIn('status', ['new', 'contacted'])
            ->latest()
            ->take(3)
            ->get();
    }

    private function getUpcomingViewings($user): \Illuminate\Database\Eloquent\Collection
    {
        return PropertyViewing::where('inquirer_id', $user->id)
            ->with(['property', 'agent'])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_date', '>=', now()->toDateString())
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->take(3)
            ->get();
    }

    private function calculateProfileCompletion($customerProfile): array
    {
        if (!$customerProfile) {
            return ['percentage' => 20, 'missing' => ['Complete basic preferences']];
        }

        $totalFields = 8;
        $completedFields = 0;
        $missing = [];

        // Check basic info
        if ($customerProfile->interested_in && count($customerProfile->interested_in) > 0) {
            $completedFields++;
        } else {
            $missing[] = 'Set property interests (rent/buy/shortlet)';
        }

        // Check budget
        if ($customerProfile->budget_min || $customerProfile->budget_max) {
            $completedFields++;
        } else {
            $missing[] = 'Set budget range';
        }

        // Check property types
        if ($customerProfile->preferred_property_types && count($customerProfile->preferred_property_types) > 0) {
            $completedFields++;
        } else {
            $missing[] = 'Select preferred property types';
        }

        // Check locations
        if ($customerProfile->preferred_locations && count($customerProfile->preferred_locations) > 0) {
            $completedFields++;
        } else {
            $missing[] = 'Choose preferred locations';
        }

        // Check notification preferences
        if ($customerProfile->notification_preferences) {
            $completedFields++;
        } else {
            $missing[] = 'Set notification preferences';
        }

        // Additional completion checks
        $completedFields += 3; // Base completion for having profile

        $percentage = round(($completedFields / $totalFields) * 100);

        return [
            'percentage' => $percentage,
            'missing' => $missing
        ];
    }

    public function savedProperties()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $savedProperties = SavedProperty::where('user_id', $user->id)
            ->with(['property.area', 'property.city'])
            ->paginate(9);

        $stats = [
            'total' => $savedProperties->total(),
            'rent' => SavedProperty::where('user_id', $user->id)
                ->whereHas('property', function($q) { $q->where('listing_type', 'rent'); })->count(),
            'sale' => SavedProperty::where('user_id', $user->id)
                ->whereHas('property', function($q) { $q->where('listing_type', 'sale'); })->count(),
        ];

        return view('customer.saved-properties', compact('savedProperties', 'stats'));
    }

    public function inquiries()
    {
        $user = auth()->user();
        $inquiries = PropertyInquiry::where('inquirer_id', $user->id)
            ->with(['property.area', 'property.city', 'responder'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => PropertyInquiry::where('inquirer_id', $user->id)->count(),
            'pending' => PropertyInquiry::where('inquirer_id', $user->id)
                ->whereIn('status', ['new', 'contacted'])->count(),
            'responded' => PropertyInquiry::where('inquirer_id', $user->id)
                ->where('status', 'responded')->count(),
            'scheduled_viewings' => PropertyViewing::where('inquirer_id', $user->id)
                ->whereIn('status', ['scheduled', 'confirmed'])->count(),
        ];

        return view('customer.inquiries', compact('inquiries', 'stats'));
    }

    public function settings()
    {
        $user = auth()->user();
        $customerProfile = $user->customerProfile;

        return view('customer.settings', compact('user', 'customerProfile'));
    }
}