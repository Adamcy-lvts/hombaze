<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'landlord_id',
        'start_date',
        'end_date',
        'yearly_rent',
        'security_deposit',
        'service_charge',
        'legal_fee',
        'agency_fee',
        'caution_deposit',
        'lease_type',
        'payment_frequency',
        'payment_method',
        'late_fee_amount',
        'grace_period_days',
        'renewal_option',
        'early_termination_fee',
        'terms_and_conditions',
        'special_clauses',
        'status',
        'signed_date',
        'move_in_date',
        'move_out_date',
        'notes',
        'template_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_date' => 'date',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'yearly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'legal_fee' => 'decimal:2',
        'agency_fee' => 'decimal:2',
        'caution_deposit' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'early_termination_fee' => 'decimal:2',
        'grace_period_days' => 'integer',
        'renewal_option' => 'boolean',
    ];

    // Lease types
    const TYPE_FIXED_TERM = 'fixed_term';
    const TYPE_MONTH_TO_MONTH = 'month_to_month';
    const TYPE_WEEK_TO_WEEK = 'week_to_week';

    // Lease statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_RENEWED = 'renewed';

    // Payment frequencies
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_BIANNUALLY = 'biannually';
    const FREQUENCY_ANNUALLY = 'annually';

    public static function getTypes(): array
    {
        return [
            self::TYPE_FIXED_TERM => 'Fixed Term',
            self::TYPE_MONTH_TO_MONTH => 'Month-to-Month',
            self::TYPE_WEEK_TO_WEEK => 'Week-to-Week',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_TERMINATED => 'Terminated',
            self::STATUS_RENEWED => 'Renewed',
        ];
    }

    public static function getPaymentFrequencies(): array
    {
        return [
            self::FREQUENCY_MONTHLY => 'Monthly',
            self::FREQUENCY_QUARTERLY => 'Quarterly',
            self::FREQUENCY_BIANNUALLY => 'Bi-annually',
            self::FREQUENCY_ANNUALLY => 'Annually',
        ];
    }

    /**
     * Property being leased
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Tenant of the lease
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Landlord/property owner
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Rent payments for this lease
     */
    public function rentPayments(): HasMany
    {
        return $this->hasMany(RentPayment::class);
    }

    /**
     * Maintenance requests for this lease
     */
    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /**
     * Check if lease is currently active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    /**
     * Check if lease is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->end_date && $this->end_date->diffInDays(now()) <= 30 && $this->end_date > now();
    }

    /**
     * Lease template used to create this lease
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(LeaseTemplate::class, 'template_id');
    }
}
