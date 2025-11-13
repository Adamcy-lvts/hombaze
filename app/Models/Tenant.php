<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'employment_status',
        'employer_name',
        'monthly_income',
        'identification_type',
        'identification_number',
        'date_of_birth',
        'nationality',
        'occupation',
        'guarantor_name',
        'guarantor_phone',
        'guarantor_email',
        'notes',
        'is_active',
        'user_id', // Optional link to platform user
        'landlord_id', // Link to landlord/property owner
        'agent_id', // Optional link to agent who brought the tenant
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
        'monthly_income' => 'decimal:2',
    ];

    /**
     * Get the full name
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Leases for this tenant
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    /**
     * Rent payments made by this tenant
     */
    public function rentPayments(): HasMany
    {
        return $this->hasMany(RentPayment::class);
    }

    /**
     * Maintenance requests from this tenant
     */
    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /**
     * Optional link to platform user (if tenant has an account)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Landlord/property owner managing this tenant
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Agent who brought this tenant (if any)
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the current active lease for this tenant
     */
    public function currentLease()
    {
        return $this->leases()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Check if tenant has an active lease
     */
    public function hasActiveLease(): bool
    {
        return $this->currentLease() !== null;
    }
}
