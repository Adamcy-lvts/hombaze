<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'price',
        'listing_credits',
        'featured_credits',
        'featured_expires_days',
        'max_active_listing_credits',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'listing_credits' => 'integer',
        'featured_credits' => 'integer',
        'featured_expires_days' => 'integer',
        'max_active_listing_credits' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
