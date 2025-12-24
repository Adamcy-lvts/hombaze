<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Agent;
use App\Models\Review;
use App\Models\Property;
use Illuminate\View\View;

class AgentProfile extends Component
{
    use WithPagination;

    public User $agent;
    public ?Agent $agentProfile = null;
    public string $reviewFilter = 'all'; // all, 5, 4, 3, 2, 1
    public string $sortBy = 'newest'; // newest, oldest, highest, lowest

    protected $queryString = [
        'reviewFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'newest'],
        'page' => ['except' => 1]
    ];

    public function mount(User $agent): void
    {
        // Ensure the user is actually an agent
        $isAgentType = in_array($agent->user_type, ['agent', 'agency_owner'], true);

        if (!$isAgentType || !$agent->agentProfile) {
            abort(404, 'Agent not found');
        }

        $this->agent = $agent;
        $this->agentProfile = $agent->agentProfile()
            ->with('user') // Eager load the user relationship
            ->withCount(['properties' => function($query) {
                $query->where('is_published', true);
            }])
            ->first();
    }

    public function updatedReviewFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function getReviewsProperty()
    {
        $query = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $this->agent->id)
            ->where('is_approved', true)
            ->with(['reviewer']);

        // Apply rating filter
        if ($this->reviewFilter !== 'all') {
            $query->where('rating', $this->reviewFilter);
        }

        // Apply sorting
        $query = match($this->sortBy) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'highest' => $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc'),
            'lowest' => $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'), // newest
        };

        return $query->paginate(10);
    }

    public function getReviewStatsProperty(): array
    {
        $reviews = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $this->agent->id)
            ->where('is_approved', true)
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $reviews->where('rating', $i)->count();
            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => round($percentage, 1)
            ];
        }

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 2),
            'rating_distribution' => $ratingDistribution,
        ];
    }

    public function getRecentPropertiesProperty()
    {
        return Property::where('agent_id', $this->agentProfile->id)
            ->where('is_published', true)
            ->with(['propertyType', 'city', 'state'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.agent-profile', [
            'reviews' => $this->reviews,
            'reviewStats' => $this->reviewStats,
            'recentProperties' => $this->recentProperties,
        ])->layout('layouts.guest-app', ['title' => $this->agent->name . ' - Real Estate Agent']);
    }
}
