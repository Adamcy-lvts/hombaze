<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingCreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_credit_account_id',
        'property_id',
        'package',
        'credit_type',
        'credits',
        'reason',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ListingCreditAccount::class, 'listing_credit_account_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
