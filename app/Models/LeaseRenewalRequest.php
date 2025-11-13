<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaseRenewalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'tenant_id',
        'landlord_id',
        'agent_id',
        'status',
        'requested_start_date',
        'requested_end_date',
        'requested_monthly_rent',
        'tenant_message',
        'response_message',
        'responded_at',
    ];

    protected $casts = [
        'requested_start_date' => 'date',
        'requested_end_date' => 'date',
        'requested_monthly_rent' => 'decimal:2',
        'responded_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Relationships
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Helper methods
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Mark request as approved
     */
    public function approve(string $message = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'response_message' => $message,
            'responded_at' => now(),
        ]);
    }

    /**
     * Mark request as rejected
     */
    public function reject(string $message = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'response_message' => $message,
            'responded_at' => now(),
        ]);
    }
}
