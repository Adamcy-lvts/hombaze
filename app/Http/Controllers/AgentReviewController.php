<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\Review;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AgentReviewController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new agent review
     */
    public function create(User $agent): View
    {
        // Ensure the user is actually an agent
        if (!$agent->isAgent() || !$agent->agentProfile) {
            abort(404, 'Agent not found');
        }

        $currentUser = auth()->user();

        // Allow all users to review agents without interaction requirement

        // Check if user already has a review for this agent
        $existingReview = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $agent->id)
            ->where('reviewer_id', $currentUser->id)
            ->first();

        return view('agent-review.create', compact('agent', 'existingReview'));
    }

    /**
     * Store a new agent review
     */
    public function store(Request $request, User $agent): JsonResponse
    {
        // Ensure the user is actually an agent
        if (!$agent->isAgent() || !$agent->agentProfile) {
            abort(404, 'Agent not found');
        }

        $currentUser = auth()->user();

        // Allow all users to review agents without interaction requirement

        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:20|max:1000',
        ]);

        // Check if user already has a review for this agent
        $existingReview = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $agent->id)
            ->where('reviewer_id', $currentUser->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this agent. You can update your existing review.'
            ], 422);
        }

        // Create the review
        $review = Review::create([
            'reviewable_type' => User::class,
            'reviewable_id' => $agent->id,
            'reviewer_id' => $currentUser->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'is_verified' => false,
            'is_approved' => $validated['rating'] >= 4, // Auto-approve 4-5 star reviews
        ]);

        // Update agent's rating statistics
        $this->updateAgentRatingStats($agent->agentProfile);

        return response()->json([
            'success' => true,
            'message' => 'Your review has been submitted successfully.',
            'review_id' => $review->id
        ]);
    }

    /**
     * Update an existing agent review
     */
    public function update(Request $request, User $agent): JsonResponse
    {
        $currentUser = auth()->user();

        $existingReview = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $agent->id)
            ->where('reviewer_id', $currentUser->id)
            ->firstOrFail();

        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:20|max:1000',
        ]);

        // Update the review
        $existingReview->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'is_approved' => $validated['rating'] >= 4, // Auto-approve 4-5 star reviews
        ]);

        // Update agent's rating statistics
        $this->updateAgentRatingStats($agent->agentProfile);

        return response()->json([
            'success' => true,
            'message' => 'Your review has been updated successfully.',
            'review_id' => $existingReview->id
        ]);
    }

    /**
     * Removed interaction restriction - all users can now review agents
     */
    // private function userHasInteractedWithAgent(User $user, User $agent): bool { ... }

    /**
     * Update agent's rating statistics
     */
    private function updateAgentRatingStats(Agent $agentProfile): void
    {
        $reviews = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $agentProfile->user_id)
            ->where('is_approved', true)
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        $agentProfile->update([
            'rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews,
        ]);
    }
}
