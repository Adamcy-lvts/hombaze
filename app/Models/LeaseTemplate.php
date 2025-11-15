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

    // Available template variables that can be used in terms and conditions (Filament merge tags format)
    public static function getAvailableVariables(): array
    {
        return [
            'Date Signed' => 'signed_date',
            'Property Title' => 'property_title',
            'Property Address' => 'property_address',
            'Property Type' => 'property_type',
            'Property Subtype' => 'property_subtype',
            'Property City' => 'property_city',
            'Property State' => 'property_state',
            'Property Area' => 'property_area',
            'Landlord Name' => 'landlord_name',
            'Landlord Email' => 'landlord_email',
            'Landlord Phone' => 'landlord_phone',
            'Landlord Address' => 'landlord_address',
            'Tenant Name' => 'tenant_name',
            'Tenant Email' => 'tenant_email',
            'Tenant Phone' => 'tenant_phone',
            'Tenant Address' => 'tenant_address',
            'Lease Start Date' => 'lease_start_date',
            'Lease End Date' => 'lease_end_date',
            'Lease Duration Months' => 'lease_duration_months',
            'Rent Amount' => 'rent_amount',
            'Payment Frequency' => 'payment_frequency',
            'Security Deposit' => 'security_deposit',
            'Service Charge' => 'service_charge',
            'Legal Fee' => 'legal_fee',
            'Agency Fee' => 'agency_fee',
            'Caution Deposit' => 'caution_deposit',
            'Grace Period Days' => 'grace_period_days',
            'Renewal Option' => 'renewal_option',
            'Current Date' => 'current_date',
            'Current Year' => 'current_year',
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
     * Replace template variables with actual values using Filament merge tags
     */
    public function substituteVariables(array $data): string
    {
        return self::renderWithMergeTags($this->terms_and_conditions, $data);
    }

    /**
     * Render an arbitrary string using Filament merge tags
     *
     * @param string|null $string
     * @param array $data
     * @return string
     */
    public static function renderString(?string $string, array $data = []): string
    {
        return self::renderWithMergeTags($string, $data);
    }

    /**
     * Process content using Filament RichEditor merge tags
     *
     * @param string|null $content
     * @param array $data
     * @return string
     */
    public static function renderWithMergeTags(?string $content, array $data = []): string
    {
        if (! $content) {
            return '';
        }

        // Prepare data for Filament merge tags
        $mergeData = [
            'signed_date' => $data['signed_date'] ?? '',
            'property_title' => $data['property_title'] ?? '',
            'property_address' => $data['property_address'] ?? '',
            'property_type' => $data['property_type'] ?? '',
            'property_subtype' => $data['property_subtype'] ?? '',
            'property_city' => $data['property_city'] ?? '',
            'property_state' => $data['property_state'] ?? '',
            'property_area' => $data['property_area'] ?? '',
            'landlord_name' => $data['landlord_name'] ?? '',
            'landlord_email' => $data['landlord_email'] ?? '',
            'landlord_phone' => $data['landlord_phone'] ?? '',
            'landlord_address' => $data['landlord_address'] ?? '',
            'tenant_name' => $data['tenant_name'] ?? '',
            'tenant_email' => $data['tenant_email'] ?? '',
            'tenant_phone' => $data['tenant_phone'] ?? '',
            'tenant_address' => $data['tenant_address'] ?? '',
            'lease_start_date' => $data['lease_start_date'] ?? '',
            'lease_end_date' => $data['lease_end_date'] ?? '',
            'lease_duration_months' => $data['lease_duration_months'] ?? '',
            'rent_amount' => isset($data['rent_amount']) && $data['rent_amount'] ? '₦' . number_format($data['rent_amount'], 2) : '',
            'payment_frequency' => $data['payment_frequency'] ?? '',
            'security_deposit' => isset($data['security_deposit']) && $data['security_deposit'] ? '₦' . number_format($data['security_deposit'], 2) : '',
            'service_charge' => isset($data['service_charge']) && $data['service_charge'] ? '₦' . number_format($data['service_charge'], 2) : '',
            'legal_fee' => isset($data['legal_fee']) && $data['legal_fee'] ? '₦' . number_format($data['legal_fee'], 2) : '',
            'agency_fee' => isset($data['agency_fee']) && $data['agency_fee'] ? '₦' . number_format($data['agency_fee'], 2) : '',
            'caution_deposit' => isset($data['caution_deposit']) && $data['caution_deposit'] ? '₦' . number_format($data['caution_deposit'], 2) : '',
            'grace_period_days' => $data['grace_period_days'] ?? '',
            'renewal_option' => ! empty($data['renewal_option']) ? 'Yes' : 'No',
            'current_date' => now()->format('F j, Y'),
            'current_year' => now()->year,
        ];

        // First, try to use Filament's RichContentRenderer if the content contains proper merge tag format
        if (class_exists(\Filament\Forms\Components\RichEditor\RichContentRenderer::class) &&
            strpos($content, 'data-type="mergeTag"') !== false) {
            try {
                return \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content)
                    ->mergeTags($mergeData)
                    ->toHtml();
            } catch (\Exception $e) {
                // Log the error but fall through to manual replacement
                \Log::warning('RichContentRenderer failed: ' . $e->getMessage());
            }
        }

        // Use manual replacement for simple {{ Tag }} format merge tags
        return self::manualReplace($content, $mergeData);
    }

    /**
     * Manual replacement for merge tags as fallback
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    private static function manualReplace(string $content, array $data): string
    {
        // Build replacement map
        $replacements = [];

        // Get available variables to build proper label mapping
        $availableVars = self::getAvailableVariables();

        foreach ($data as $key => $value) {
            // Find the proper label for this key
            $label = array_search($key, $availableVars);
            if ($label !== false) {
                // Replace label-style merge tags
                $replacements['{{ ' . $label . ' }}'] = (string) $value;
                $replacements['{{' . $label . '}}'] = (string) $value;
            }

            // Also replace key-style merge tags
            $replacements['{{ ' . $key . ' }}'] = (string) $value;
            $replacements['{{' . $key . '}}'] = (string) $value;

            // Handle various case formats
            $keyTitle = ucwords(str_replace('_', ' ', $key));
            $replacements['{{ ' . $keyTitle . ' }}'] = (string) $value;
            $replacements['{{' . $keyTitle . '}}'] = (string) $value;
        }

        // Handle special cases and variations that might exist in templates
        $specialCases = [
            '{{ Lease Duration (Months) }}' => $data['lease_duration_months'] ?? '',
            '{{Lease Duration (Months)}}' => $data['lease_duration_months'] ?? '',
            '{{ Grace Period (Days) }}' => $data['grace_period_days'] ?? '',
            '{{Grace Period (Days)}}' => $data['grace_period_days'] ?? '',
            '{{ Monthly/Annual Rent Amount }}' => $data['rent_amount'] ?? '',
            '{{Monthly/Annual Rent Amount}}' => $data['rent_amount'] ?? '',
            '{{ Renewal Option (Yes/No) }}' => $data['renewal_option'] ?? '',
            '{{Renewal Option (Yes/No)}}' => $data['renewal_option'] ?? '',
        ];

        $replacements = array_merge($replacements, $specialCases);

        // Perform all replacements
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        return $content;
    }

    /**
     * Extract variables used in the template
     */
    public function extractUsedVariables(): array
    {
        $availableVariables = self::getAvailableVariables();
        $usedVariables = [];

        foreach ($availableVariables as $label => $key) {
            // Check for both label and key formats
            $labelPlaceholder = '{{ ' . $label . ' }}';
            $keyPlaceholder = '{{ ' . $key . ' }}';

            if (str_contains($this->terms_and_conditions, $labelPlaceholder) ||
                str_contains($this->terms_and_conditions, $keyPlaceholder)) {
                $usedVariables[] = $key;
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