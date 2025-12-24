<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingCreditAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'listing_balance',
        'featured_balance',
        'featured_expires_at',
    ];

    protected $casts = [
        'listing_balance' => 'integer',
        'featured_balance' => 'integer',
        'featured_expires_at' => 'datetime',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ListingCreditTransaction::class);
    }

    public function hasListingCredits(int $required = 1): bool
    {
        return $this->listing_balance >= $required;
    }

    public function hasFeaturedCredits(int $required = 1): bool
    {
        if ($this->featured_expires_at && $this->featured_expires_at->isPast()) {
            return false;
        }

        return $this->featured_balance >= $required;
    }
}
