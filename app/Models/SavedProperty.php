<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'notes',
    ];

    // Relationships

    /**
     * Get the user who saved the property
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the saved property
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // Scopes

    /**
     * Scope for properties saved by a specific user
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope for available saved properties
     */
    public function scopeAvailable($query)
    {
        return $query->whereHas('property', function ($q) {
            $q->where('status', 'available')->where('is_published', true);
        });
    }

    // Helper methods

    /**
     * Check if property is still available
     */
    public function isPropertyAvailable(): bool
    {
        return $this->property && 
               $this->property->status === 'available' && 
               $this->property->is_published;
    }

    /**
     * Get the property title
     */
    public function getPropertyTitleAttribute(): string
    {
        return $this->property ? $this->property->title : 'Property Not Found';
    }

    /**
     * Get the property price
     */
    public function getPropertyPriceAttribute(): ?float
    {
        return $this->property ? $this->property->price : null;
    }
}
