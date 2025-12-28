<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'landlord_id',
        'agency_id',
        'agent_id',
        'template_id',
        'buyer_user_id',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'buyer_address',
        'seller_name',
        'seller_email',
        'seller_phone',
        'seller_address',
        'sale_price',
        'deposit_amount',
        'balance_amount',
        'closing_date',
        'signed_date',
        'terms_and_conditions',
        'status',
        'notes',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'closing_date' => 'date',
        'signed_date' => 'date',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SIGNED = 'signed';
    const STATUS_COMPLETED = 'completed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SIGNED => 'Signed',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(SalesAgreementTemplate::class, 'template_id');
    }
}
