<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmartSearchPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'price',
        'searches_limit',
        'duration_days',
        'notification_channels',
        'priority_order',
        'delay_hours',
        'exclusive_window_hours',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'searches_limit' => 'integer',
        'duration_days' => 'integer',
        'notification_channels' => 'array',
        'priority_order' => 'integer',
        'delay_hours' => 'integer',
        'exclusive_window_hours' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
