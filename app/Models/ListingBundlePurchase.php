<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingBundlePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'bundle_key',
        'product_type',
        'product_id',
        'amount',
        'currency',
        'status',
        'paystack_reference',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
