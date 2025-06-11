<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends SpatieRole
{
    /**
     * Get the agency that owns the role.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
