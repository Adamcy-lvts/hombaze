<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'tenant_id',
        'landlord_id',
        'property_id',
        'amount',
        'payment_date',
        'due_date',
        'payment_method',
        'payment_reference',
        'late_fee',
        'discount',
        'deposit',
        'balance_due',
        'net_amount',
        'status',
        'payment_for_period',
        'notes',
        'receipt_number',
        'processed_by',
        // Manual entry fields
        'manual_tenant_name',
        'manual_tenant_email',
        'manual_tenant_phone',
        'manual_property_title',
        'manual_property_address',
        'is_manual_entry',
        // Custom dates for free-form receipts
        'custom_start_date',
        'custom_end_date',
        'payment_for',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'deposit' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'is_manual_entry' => 'boolean',
        'custom_start_date' => 'date',
        'custom_end_date' => 'date',
    ];

    // Payment statuses
    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    // Payment methods
    const METHOD_CASH = 'cash';
    const METHOD_TRANSFER = 'transfer';
    const METHOD_POS = 'pos';
    const METHOD_CARD = 'card';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PARTIAL => 'Partial',
            self::STATUS_PAID => 'Paid',
            self::STATUS_OVERDUE => 'Overdue',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }

    public static function getPaymentMethods(): array
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_TRANSFER => 'Transfer',
            self::METHOD_POS => 'POS',
            self::METHOD_CARD => 'Card',
        ];
    }

    /**
     * Lease this payment belongs to
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Tenant who made the payment
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Landlord receiving the payment
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Property the payment is for
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * User who processed the payment
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now() && !in_array($this->status, [self::STATUS_PAID, self::STATUS_CANCELLED]);
    }

    /**
     * Calculate total amount including late fees and discounts
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->amount + ($this->late_fee ?? 0) - ($this->discount ?? 0);
    }

    /**
     * Get tenant name from relation or manual entry
     */
    public function getTenantNameAttribute(): ?string
    {
        if ($this->tenant) {
            return $this->tenant->name;
        }
        return $this->manual_tenant_name;
    }

    /**
     * Get tenant email from relation or manual entry
     */
    public function getTenantEmailAttribute(): ?string
    {
        if ($this->tenant) {
            return $this->tenant->email;
        }
        return $this->manual_tenant_email;
    }

    /**
     * Get tenant phone from relation or manual entry
     */
    public function getTenantPhoneAttribute(): ?string
    {
        if ($this->tenant) {
            return $this->tenant->phone;
        }
        return $this->manual_tenant_phone;
    }

    /**
     * Get property title from relation or manual entry
     */
    public function getPropertyTitleAttribute(): ?string
    {
        if ($this->property) {
            return $this->property->title;
        }
        return $this->manual_property_title;
    }

    /**
     * Get property address from relation or manual entry
     */
    public function getPropertyAddressAttribute(): ?string
    {
        if ($this->property) {
            return $this->property->address;
        }
        return $this->manual_property_address;
    }
}
