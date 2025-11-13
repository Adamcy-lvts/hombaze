<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\User;
use App\Models\Agent;
use App\Models\Review;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use Carbon\Carbon;

class AgentRatingForm extends Component
{
    public User $agent;
    public ?Review $existingReview = null;
    public bool $showForm = false;
    public bool $hasInteracted = false;
    public string $interactionMessage = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('nullable|string|max:255')]
    public ?string $title = '';

    #[Validate('required|string|min:20|max:1000')]
    public string $comment = '';

    public bool $isSubmitting = false;

    public function mount(User $agent)
    {
        $this->agent = $agent;

        if (!auth()->check()) {
            return;
        }

        // Allow all users to review agents without interaction requirement
        $this->hasInteracted = true;
        $this->interactionMessage = '';

        // Check if user already has a review
        $this->existingReview = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $this->agent->id)
            ->where('reviewer_id', auth()->id())
            ->first();

        // Pre-fill form if editing existing review
        if ($this->existingReview) {
            $this->rating = $this->existingReview->rating;
            $this->title = $this->existingReview->title ?? '';
            $this->comment = $this->existingReview->comment;
        }
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function submitReview()
    {
        if (!auth()->check()) {
            session()->flash('error', 'You must be logged in to submit a review.');
            return;
        }

        $this->validate();

        $this->isSubmitting = true;

        try {
            if ($this->existingReview) {
                // Update existing review
                $this->existingReview->update([
                    'rating' => $this->rating,
                    'title' => $this->title,
                    'comment' => $this->comment,
                    'is_approved' => $this->rating >= 4,
                ]);

                session()->flash('message', 'Your review has been updated successfully!');
            } else {
                // Create new review
                Review::create([
                    'reviewable_type' => User::class,
                    'reviewable_id' => $this->agent->id,
                    'reviewer_id' => auth()->id(),
                    'rating' => $this->rating,
                    'title' => $this->title,
                    'comment' => $this->comment,
                    'is_verified' => false,
                    'is_approved' => $this->rating >= 4,
                ]);

                session()->flash('message', 'Your review has been submitted successfully!');
            }

            // Update agent's rating statistics
            $this->updateAgentRatingStats();

            $this->showForm = false;
            $this->dispatch('reviewSubmitted');

        } catch (Exception $e) {
            session()->flash('error', 'There was an error submitting your review. Please try again.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    // Removed interaction restriction - all users can now review agents
    // private function checkUserInteraction() { ... }

    private function updateAgentRatingStats()
    {
        $reviews = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $this->agent->id)
            ->where('is_approved', true)
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        $this->agent->agentProfile->update([
            'rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews,
        ]);
    }

    public function render()
    {
        return view('livewire.agent-rating-form');
    }
}
