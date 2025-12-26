<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class SmartSearchMatch extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_PENDING = 'pending';       // Waiting in queue
    public const STATUS_QUEUED = 'queued';         // Scheduled for notification
    public const STATUS_NOTIFIED = 'notified';     // Notification sent
    public const STATUS_CLAIMED = 'claimed';       // User viewed + contacted (VIP only)
    public const STATUS_EXPIRED = 'expired';       // Exclusive window passed without claim
    public const STATUS_SKIPPED = 'skipped';       // Cascade ended (property unavailable)
    public const STATUS_COMPLETED = 'completed';   // All notifications sent

    protected $fillable = [
        'smart_search_id',
        'property_id',
        'user_id',
        'match_score',
        'tier',
        'status',
        'queued_at',
        'notified_at',
        'exclusive_until',
        'claimed_at',
        'claim_expires_at',
        'property_viewed',
        'property_viewed_at',
        'agent_contacted',
        'agent_contacted_at',
        'notification_channels_used',
        'match_reasons',
        'cascade_position',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'queued_at' => 'datetime',
        'notified_at' => 'datetime',
        'exclusive_until' => 'datetime',
        'claimed_at' => 'datetime',
        'claim_expires_at' => 'datetime',
        'property_viewed' => 'boolean',
        'property_viewed_at' => 'datetime',
        'agent_contacted' => 'boolean',
        'agent_contacted_at' => 'datetime',
        'notification_channels_used' => 'array',
        'match_reasons' => 'array',
        'cascade_position' => 'integer',
    ];

    // =========================================
    // Relationships
    // =========================================

    public function smartSearch(): BelongsTo
    {
        return $this->belongsTo(SmartSearch::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================
    // Scopes
    // =========================================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeQueued($query)
    {
        return $query->where('status', self::STATUS_QUEUED);
    }

    public function scopeNotified($query)
    {
        return $query->where('status', self::STATUS_NOTIFIED);
    }

    public function scopeClaimed($query)
    {
        return $query->where('status', self::STATUS_CLAIMED);
    }

    public function scopeForTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeVip($query)
    {
        return $query->where('tier', SmartSearch::TIER_VIP);
    }

    public function scopeForProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExclusiveWindowActive($query)
    {
        return $query->whereNotNull('exclusive_until')
            ->where('exclusive_until', '>', now());
    }

    public function scopeClaimPauseActive($query)
    {
        return $query->where('status', self::STATUS_CLAIMED)
            ->whereNotNull('claim_expires_at')
            ->where('claim_expires_at', '>', now());
    }

    public function scopeOrderedByCascadePosition($query)
    {
        return $query->orderBy('cascade_position')->orderBy('created_at');
    }

    // =========================================
    // Status Check Methods
    // =========================================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isQueued(): bool
    {
        return $this->status === self::STATUS_QUEUED;
    }

    public function isNotified(): bool
    {
        return $this->status === self::STATUS_NOTIFIED;
    }

    public function isClaimed(): bool
    {
        return $this->status === self::STATUS_CLAIMED;
    }

    public function isSkipped(): bool
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    public function isInExclusiveWindow(): bool
    {
        return $this->exclusive_until && $this->exclusive_until->isFuture();
    }

    public function isClaimPauseActive(): bool
    {
        return $this->isClaimed() &&
            $this->claim_expires_at &&
            $this->claim_expires_at->isFuture();
    }

    public function hasBeenActedUpon(): bool
    {
        return $this->property_viewed || $this->agent_contacted;
    }

    public function isFullyClaimed(): bool
    {
        return $this->property_viewed && $this->agent_contacted;
    }

    public function isVipMatch(): bool
    {
        return $this->tier === SmartSearch::TIER_VIP;
    }

    // =========================================
    // Status Update Methods
    // =========================================

    public function markAsQueued(): void
    {
        $this->update([
            'status' => self::STATUS_QUEUED,
            'queued_at' => now(),
        ]);
    }

    public function markAsNotified(array $channels = [], ?Carbon $exclusiveUntil = null): void
    {
        $this->update([
            'status' => self::STATUS_NOTIFIED,
            'notified_at' => now(),
            'exclusive_until' => $exclusiveUntil,
            'notification_channels_used' => $channels,
        ]);
    }

    public function markAsClaimed(): void
    {
        $this->update([
            'status' => self::STATUS_CLAIMED,
            'claimed_at' => now(),
            'claim_expires_at' => now()->addHours(24),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);
    }

    public function markAsSkipped(): void
    {
        $this->update([
            'status' => self::STATUS_SKIPPED,
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    // =========================================
    // Action Tracking Methods
    // =========================================

    public function recordPropertyView(): void
    {
        if (!$this->property_viewed) {
            $this->update([
                'property_viewed' => true,
                'property_viewed_at' => now(),
            ]);

            $this->checkForClaim();
        }
    }

    public function recordAgentContact(): void
    {
        if (!$this->agent_contacted) {
            $this->update([
                'agent_contacted' => true,
                'agent_contacted_at' => now(),
            ]);

            $this->checkForClaim();
        }
    }

    protected function checkForClaim(): void
    {
        // Only VIP matches can claim
        if (!$this->isVipMatch()) {
            return;
        }

        // Only claim if in exclusive window and both actions taken
        if ($this->isInExclusiveWindow() && $this->isFullyClaimed() && !$this->isClaimed()) {
            $this->markAsClaimed();
        }
    }

    // =========================================
    // Cascade Methods
    // =========================================

    public function getExclusiveTimeRemaining(): ?int
    {
        if (!$this->exclusive_until || $this->exclusive_until->isPast()) {
            return null;
        }

        return (int) now()->diffInMinutes($this->exclusive_until);
    }

    public function getClaimPauseTimeRemaining(): ?int
    {
        if (!$this->claim_expires_at || $this->claim_expires_at->isPast()) {
            return null;
        }

        return (int) now()->diffInMinutes($this->claim_expires_at);
    }

    // =========================================
    // Display Methods
    // =========================================

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Waiting',
            self::STATUS_QUEUED => 'Scheduled',
            self::STATUS_NOTIFIED => 'Notified',
            self::STATUS_CLAIMED => 'Claimed',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_SKIPPED => 'Skipped',
            self::STATUS_COMPLETED => 'Completed',
            default => 'Unknown',
        };
    }

    public function getStatusBadge(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING => ['label' => 'Waiting', 'color' => 'gray'],
            self::STATUS_QUEUED => ['label' => 'Scheduled', 'color' => 'blue'],
            self::STATUS_NOTIFIED => ['label' => 'Notified', 'color' => 'green'],
            self::STATUS_CLAIMED => ['label' => 'Claimed', 'color' => 'purple'],
            self::STATUS_EXPIRED => ['label' => 'Expired', 'color' => 'warning'],
            self::STATUS_SKIPPED => ['label' => 'Skipped', 'color' => 'danger'],
            self::STATUS_COMPLETED => ['label' => 'Completed', 'color' => 'success'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }

    public function getFormattedMatchScore(): string
    {
        return number_format($this->match_score, 1) . '%';
    }

    public function getMatchQuality(): string
    {
        if ($this->match_score >= 90) {
            return 'Excellent';
        }
        if ($this->match_score >= 80) {
            return 'Great';
        }
        if ($this->match_score >= 70) {
            return 'Good';
        }

        return 'Fair';
    }
}
