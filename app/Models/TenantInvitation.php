<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TenantInvitation extends Model
{
    protected $fillable = [
        'phone',
        'token',
        'status',
        'landlord_id',
        'agent_id',
        'property_id',
        'message',
        'expires_at',
        'accepted_at',
        'invited_from_ip',
        'accepted_from_ip',
        'tenant_user_id',
        'link_copied_at',
        'link_copy_count',
        'sent_via',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'link_copied_at' => 'datetime',
        'sent_via' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot method to generate token
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(64);
            }

            if (empty($invitation->expires_at)) {
                $invitation->expires_at = now()->addDays(7); // Default 7 days expiry
            }

            if (empty($invitation->status)) {
                $invitation->status = self::STATUS_PENDING; // Ensure status is set to pending
            }
        });
    }

    /**
     * Relationships
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenantUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Agent::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeForPhone($query, string $phone)
    {
        return $query->where('phone', $phone);
    }

    /**
     * Helper methods
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status === self::STATUS_EXPIRED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && !$this->isExpired();
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Mark invitation as accepted
     */
    public function markAsAccepted(User $tenantUser, ?string $ipAddress = null): void
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
            'tenant_user_id' => $tenantUser->id,
            'accepted_from_ip' => $ipAddress,
        ]);
    }

    /**
     * Mark invitation as expired
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Mark invitation as cancelled
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Generate invitation URL
     */
    public function getInvitationUrl(): string
    {
        return route('tenant.invitation.accept', ['token' => $this->token]);
    }

    /**
     * Check if invitation is valid for acceptance
     */
    public function isValidForAcceptance(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }
}
