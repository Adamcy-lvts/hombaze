<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDraft extends Model
{
    protected $fillable = [
        'user_id',
        'agency_id',
        'form_data',
        'wizard_step',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];
}
