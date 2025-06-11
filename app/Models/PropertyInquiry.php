<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'inquirer_id',
        'inquirer_name',
        'inquirer_email',
        'inquirer_phone',
        'message',
        'preferred_viewing_date',
        'status',
        'responded_at',
        'responded_by',
        'response_message',
    ];

    protected $casts = [
        'preferred_viewing_date' => 'date',
        'responded_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the property being inquired about
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who made the inquiry
     */
    public function inquirer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inquirer_id');
    }

    /**
     * Get the user who responded to the inquiry
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
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
            'property_id', // Local key on PropertyInquiry table
            'agency_id' // Local key on Property table
        );
    }

    // Scopes

    /**
     * Scope for new inquiries
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope for responded inquiries
     */
    public function scopeResponded($query)
    {
        return $query->whereNotNull('responded_at');
    }

    /**
     * Scope for pending inquiries
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['new', 'contacted']);
    }

    // Helper methods

    /**
     * Check if inquiry has been responded to
     */
    public function isResponded(): bool
    {
        return !is_null($this->responded_at);
    }

    /**
     * Check if inquiry is pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['new', 'contacted']);
    }

    /**
     * Mark inquiry as contacted
     */
    public function markAsContacted(User $user, string $response = null): void
    {
        $this->update([
            'status' => 'contacted',
            'responded_at' => now(),
            'responded_by' => $user->id,
            'response_message' => $response,
        ]);
    }

    /**
     * Mark inquiry as closed
     */
    public function markAsClosed(User $user, string $response = null): void
    {
        $this->update([
            'status' => 'closed',
            'responded_at' => now(),
            'responded_by' => $user->id,
            'response_message' => $response,
        ]);
    }
}
