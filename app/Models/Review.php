<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'reviewer_id',
        'moderated_by',
        'rating',
        'title',
        'comment',
        'status',
        'is_verified',
        'is_approved',
        'is_featured',
        'is_anonymous',
        'helpful_count',
        'not_helpful_count',
        'response_count',
        'moderated_at',
        'moderation_notes',
        'last_activity_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'is_anonymous' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'response_count' => 'integer',
        'moderated_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the reviewable entity (property, agency, agent)
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who wrote the review
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the user who moderated the review.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    // Scopes

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for reviews by rating
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for high rated reviews (4-5 stars)
     */
    public function scopeHighRated($query)
    {
        return $query->whereIn('rating', [4, 5]);
    }

    /**
     * Scope for low rated reviews (1-2 stars)
     */
    public function scopeLowRated($query)
    {
        return $query->whereIn('rating', [1, 2]);
    }

    // Helper methods

    /**
     * Check if review is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * Check if review is verified
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Check if review is high rated
     */
    public function isHighRated(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Check if review is low rated
     */
    public function isLowRated(): bool
    {
        return $this->rating <= 2;
    }

    /**
     * Approve the review
     */
    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    /**
     * Reject the review
     */
    public function reject(): void
    {
        $this->update(['is_approved' => false]);
    }

    /**
     * Mark as verified
     */
    public function verify(): void
    {
        $this->update(['is_verified' => true]);
    }

    /**
     * Increment helpful count
     */
    public function incrementHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Get star rating display
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
