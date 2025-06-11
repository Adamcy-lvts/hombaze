<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends SpatiePermission
{
    /**
     * Get the agency that owns the permission.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
