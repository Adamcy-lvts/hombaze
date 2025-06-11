<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'search_criteria',
        'alert_frequency',
        'is_active',
        'last_alerted_at',
    ];

    protected $casts = [
        'search_criteria' => 'array',
        'is_active' => 'boolean',
        'last_alerted_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the user who saved the search
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes

    /**
     * Scope for searches by a specific user
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope for active searches
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for searches that need alerts
     */
    public function scopeNeedsAlert($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('last_alerted_at')
                          ->orWhere('last_alerted_at', '<', now()->subDay());
                    });
    }

    // Helper methods

    /**
     * Check if search is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate the search
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the search
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Mark as alerted
     */
    public function markAsAlerted(): void
    {
        $this->update(['last_alerted_at' => now()]);
    }

    /**
     * Get search criteria as readable string
     */
    public function getReadableCriteriaAttribute(): string
    {
        $criteria = $this->search_criteria;
        $readable = [];

        if (isset($criteria['property_type'])) {
            $readable[] = "Type: " . $criteria['property_type'];
        }

        if (isset($criteria['min_price']) && isset($criteria['max_price'])) {
            $readable[] = "Price: ₦" . number_format($criteria['min_price']) . " - ₦" . number_format($criteria['max_price']);
        } elseif (isset($criteria['min_price'])) {
            $readable[] = "Min Price: ₦" . number_format($criteria['min_price']);
        } elseif (isset($criteria['max_price'])) {
            $readable[] = "Max Price: ₦" . number_format($criteria['max_price']);
        }

        if (isset($criteria['bedrooms'])) {
            $readable[] = "Bedrooms: " . $criteria['bedrooms'];
        }

        if (isset($criteria['location'])) {
            $readable[] = "Location: " . $criteria['location'];
        }

        return implode(', ', $readable) ?: 'No criteria set';
    }

    /**
     * Check if search matches property
     */
    public function matchesProperty(Property $property): bool
    {
        $criteria = $this->search_criteria;

        // Check property type
        if (isset($criteria['property_type_id']) && $property->property_type_id != $criteria['property_type_id']) {
            return false;
        }

        // Check price range
        if (isset($criteria['min_price']) && $property->price < $criteria['min_price']) {
            return false;
        }

        if (isset($criteria['max_price']) && $property->price > $criteria['max_price']) {
            return false;
        }

        // Check bedrooms
        if (isset($criteria['bedrooms']) && $property->bedrooms < $criteria['bedrooms']) {
            return false;
        }

        // Check location
        if (isset($criteria['state_id']) && $property->state_id != $criteria['state_id']) {
            return false;
        }

        if (isset($criteria['city_id']) && $property->city_id != $criteria['city_id']) {
            return false;
        }

        if (isset($criteria['area_id']) && $property->area_id != $criteria['area_id']) {
            return false;
        }

        return true;
    }
}
