<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyViewing extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'inquirer_id',
        'agent_id',
        'scheduled_date',
        'scheduled_time',
        'status',
        'notes',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the property being viewed
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who requested the viewing
     */
    public function inquirer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inquirer_id');
    }

    /**
     * Get the agent conducting the viewing
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the agency through the property (for Filament tenancy)
     */
    public function agency(): HasOneThrough
    {
        return $this->hasOneThrough(
            Agency::class,
            Property::class,
            'id', // Foreign key on Property table
            'id', // Foreign key on Agency table  
            'property_id', // Local key on PropertyViewing table
            'agency_id' // Local key on Property table
        );
    }

    // Scopes

    /**
     * Scope for scheduled viewings
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for confirmed viewings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for completed viewings
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for upcoming viewings
     */
    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['scheduled', 'confirmed'])
                    ->where('scheduled_date', '>=', now()->toDateString());
    }

    /**
     * Scope for past viewings
     */
    public function scopePast($query)
    {
        return $query->where('scheduled_date', '<', now()->toDateString())
                    ->orWhere('status', 'completed');
    }

    // Helper methods

    /**
     * Check if viewing is upcoming
     */
    public function isUpcoming(): bool
    {
        return in_array($this->status, ['scheduled', 'confirmed']) && 
               $this->scheduled_date >= now()->toDateString();
    }

    /**
     * Check if viewing is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if viewing is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Confirm the viewing
     */
    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    /**
     * Complete the viewing
     */
    public function complete(string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Cancel the viewing
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Mark as no show
     */
    public function markAsNoShow(): void
    {
        $this->update(['status' => 'no_show']);
    }
}
