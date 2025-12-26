<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmartSearchSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tier',
        'searches_limit',
        'searches_used',
        'duration_days',
        'amount_paid',
        'payment_reference',
        'payment_method',
        'payment_status',
        'paid_at',
        'starts_at',
        'expires_at',
        'is_renewal',
        'renewal_discount',
        'renewed_from_id',
        'notification_channels',
        'payment_metadata',
        'notes',
    ];

    protected $casts = [
        'searches_limit' => 'integer',
        'searches_used' => 'integer',
        'duration_days' => 'integer',
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_renewal' => 'boolean',
        'renewal_discount' => 'decimal:2',
        'notification_channels' => 'array',
        'payment_metadata' => 'array',
    ];

    // =========================================
    // Relationships
    // =========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function smartSearches(): HasMany
    {
        return $this->hasMany(SmartSearch::class, 'user_id', 'user_id')
            ->where('tier', $this->tier);
    }

    public function renewedFrom(): BelongsTo
    {
        return $this->belongsTo(SmartSearchSubscription::class, 'renewed_from_id');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(SmartSearchSubscription::class, 'renewed_from_id');
    }

    // =========================================
    // Scopes
    // =========================================

    public function scopeActive($query)
    {
        return $query->where('payment_status', 'paid')
            ->where('expires_at', '>', now());
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeForTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('payment_status', 'paid')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    // =========================================
    // Status Methods
    // =========================================

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isActive(): bool
    {
        return $this->isPaid() && $this->expires_at && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // =========================================
    // Search Limit Methods
    // =========================================

    public function canCreateSearch(): bool
    {
        return $this->isPaid() &&
            $this->isActive() &&
            $this->searches_used < $this->searches_limit;
    }

    public function incrementSearchCount(): void
    {
        $this->increment('searches_used');
    }

    public function decrementSearchCount(): void
    {
        if ($this->searches_used > 0) {
            $this->decrement('searches_used');
        }
    }

    public function getRemainingSearches(): int
    {
        return max(0, $this->searches_limit - $this->searches_used);
    }

    public function hasUnlimitedSearches(): bool
    {
        return $this->searches_limit >= 999;
    }

    // =========================================
    // Activation Methods
    // =========================================

    public function activate(array $paymentData = []): void
    {
        $startsAt = now();
        $expiresAt = now()->addDays($this->duration_days);

        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'payment_metadata' => array_merge(
                $this->payment_metadata ?? [],
                $paymentData
            ),
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'notes' => $reason,
        ]);
    }

    // =========================================
    // Renewal Methods
    // =========================================

    public function canBeRenewed(): bool
    {
        // Can renew within 14 days before expiry or up to 30 days after
        if (!$this->expires_at) {
            return false;
        }

        return $this->isPaid() &&
            $this->expires_at->isBetween(now()->subDays(30), now()->addDays(14));
    }

    public function createRenewal(): self
    {
        $tierConfig = SmartSearch::TIER_CONFIGS[$this->tier] ?? SmartSearch::TIER_CONFIGS[SmartSearch::TIER_STARTER];
        $basePrice = $tierConfig['price'];
        $discount = SmartSearch::RENEWAL_DISCOUNT_PERCENT;
        $discountedPrice = $basePrice * (1 - $discount / 100);

        return self::create([
            'user_id' => $this->user_id,
            'tier' => $this->tier,
            'searches_limit' => $tierConfig['searches'],
            'duration_days' => $tierConfig['duration_days'],
            'amount_paid' => $discountedPrice,
            'payment_status' => 'pending',
            'is_renewal' => true,
            'renewal_discount' => $discount,
            'renewed_from_id' => $this->id,
            'notification_channels' => $tierConfig['channels'],
        ]);
    }

    // =========================================
    // Display Methods
    // =========================================

    public function getTierName(): string
    {
        return SmartSearch::TIER_CONFIGS[$this->tier]['name'] ?? 'Unknown';
    }

    public function getFormattedAmount(): string
    {
        return '₦' . number_format($this->amount_paid);
    }

    public function getDaysRemaining(): int
    {
        if (!$this->expires_at || $this->expires_at->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }

    public function getStatusBadge(): array
    {
        if ($this->isPending()) {
            return ['label' => 'Pending Payment', 'color' => 'warning'];
        }

        if ($this->isFailed()) {
            return ['label' => 'Payment Failed', 'color' => 'danger'];
        }

        if ($this->isExpired()) {
            return ['label' => 'Expired', 'color' => 'gray'];
        }

        $daysLeft = $this->getDaysRemaining();
        if ($daysLeft <= 7) {
            return ['label' => "Expiring in {$daysLeft}d", 'color' => 'warning'];
        }

        return ['label' => 'Active', 'color' => 'success'];
    }
}
