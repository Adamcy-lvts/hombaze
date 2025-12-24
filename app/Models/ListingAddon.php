<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'price',
        'listing_credits',
        'featured_credits',
        'featured_expires_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'listing_credits' => 'integer',
        'featured_credits' => 'integer',
        'featured_expires_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
