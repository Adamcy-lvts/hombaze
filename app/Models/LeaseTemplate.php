<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaseTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'name',
        'description',
        'terms_and_conditions',
        'default_payment_frequency',
        'default_security_deposit',
        'default_service_charge',
        'default_legal_fee',
        'default_agency_fee',
        'default_caution_deposit',
        'default_grace_period_days',
        'default_renewal_option',
        'available_variables',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'default_security_deposit' => 'decimal:2',
        'default_service_charge' => 'decimal:2',
        'default_legal_fee' => 'decimal:2',
        'default_agency_fee' => 'decimal:2',
        'default_caution_deposit' => 'decimal:2',
        'default_grace_period_days' => 'integer',
        'default_renewal_option' => 'boolean',
        'available_variables' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Available template variables that can be used in terms and conditions
    public static function getAvailableVariables(): array
    {
        return [
            'property_title' => 'Property Title',
            'property_address' => 'Property Address',
            'property_type' => 'Property Type',
            'property_subtype' => 'Property Subtype',
            'landlord_name' => 'Landlord Name',
            'landlord_email' => 'Landlord Email',
            'landlord_phone' => 'Landlord Phone',
            'tenant_name' => 'Tenant Name',
            'tenant_email' => 'Tenant Email',
            'tenant_phone' => 'Tenant Phone',
            'lease_start_date' => 'Lease Start Date',
            'lease_end_date' => 'Lease End Date',
            'lease_duration_months' => 'Lease Duration (Months)',
            'rent_amount' => 'Monthly/Annual Rent Amount',
            'payment_frequency' => 'Payment Frequency',
            'security_deposit' => 'Security Deposit',
            'service_charge' => 'Service Charge',
            'legal_fee' => 'Legal Fee',
            'agency_fee' => 'Agency Fee',
            'caution_deposit' => 'Caution Deposit',
            'grace_period_days' => 'Grace Period (Days)',
            'renewal_option' => 'Renewal Option (Yes/No)',
            'current_date' => 'Current Date',
            'current_year' => 'Current Year',
        ];
    }

    public static function getPaymentFrequencies(): array
    {
        return [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'biannually' => 'Bi-annually',
            'annually' => 'Annually',
        ];
    }

    /**
     * Landlord who owns this template
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Leases created from this template
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'template_id');
    }

    /**
     * Replace template variables with actual values
     */
    public function substituteVariables(array $data): string
    {
        $termsAndConditions = $this->terms_and_conditions;

        // Define variable patterns and their replacements
        $variables = [
            '{{property_title}}' => $data['property_title'] ?? '',
            '{{property_address}}' => $data['property_address'] ?? '',
            '{{property_type}}' => $data['property_type'] ?? '',
            '{{property_subtype}}' => $data['property_subtype'] ?? '',
            '{{landlord_name}}' => $data['landlord_name'] ?? '',
            '{{landlord_email}}' => $data['landlord_email'] ?? '',
            '{{landlord_phone}}' => $data['landlord_phone'] ?? '',
            '{{tenant_name}}' => $data['tenant_name'] ?? '',
            '{{tenant_email}}' => $data['tenant_email'] ?? '',
            '{{tenant_phone}}' => $data['tenant_phone'] ?? '',
            '{{lease_start_date}}' => $data['lease_start_date'] ?? '',
            '{{lease_end_date}}' => $data['lease_end_date'] ?? '',
            '{{lease_duration_months}}' => $data['lease_duration_months'] ?? '',
            '{{rent_amount}}' => $data['rent_amount'] ? '₦' . number_format($data['rent_amount'], 2) : '',
            '{{payment_frequency}}' => $data['payment_frequency'] ?? '',
            '{{security_deposit}}' => $data['security_deposit'] ? '₦' . number_format($data['security_deposit'], 2) : '',
            '{{service_charge}}' => $data['service_charge'] ? '₦' . number_format($data['service_charge'], 2) : '',
            '{{legal_fee}}' => $data['legal_fee'] ? '₦' . number_format($data['legal_fee'], 2) : '',
            '{{agency_fee}}' => $data['agency_fee'] ? '₦' . number_format($data['agency_fee'], 2) : '',
            '{{caution_deposit}}' => $data['caution_deposit'] ? '₦' . number_format($data['caution_deposit'], 2) : '',
            '{{grace_period_days}}' => $data['grace_period_days'] ?? '',
            '{{renewal_option}}' => ($data['renewal_option'] ?? false) ? 'Yes' : 'No',
            '{{current_date}}' => now()->format('F j, Y'),
            '{{current_year}}' => now()->year,
        ];

        // Replace all variables in the terms and conditions
        foreach ($variables as $placeholder => $value) {
            $termsAndConditions = str_replace($placeholder, $value, $termsAndConditions);
        }

        return $termsAndConditions;
    }

    /**
     * Extract variables used in the template
     */
    public function extractUsedVariables(): array
    {
        $allVariables = array_keys(self::getAvailableVariables());
        $usedVariables = [];

        foreach ($allVariables as $variable) {
            $placeholder = '{{' . $variable . '}}';
            if (str_contains($this->terms_and_conditions, $placeholder)) {
                $usedVariables[] = $variable;
            }
        }

        return $usedVariables;
    }

    /**
     * Get the default template for a landlord
     */
    public static function getDefaultTemplate(int $landlordId): ?self
    {
        return self::where('landlord_id', $landlordId)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Set this template as the default (and unset others)
     */
    public function setAsDefault(): void
    {
        // Unset other default templates for this landlord
        self::where('landlord_id', $this->landlord_id)
            ->where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }
}