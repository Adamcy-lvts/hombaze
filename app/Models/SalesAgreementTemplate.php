<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesAgreementTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'agency_id',
        'agent_id',
        'name',
        'description',
        'terms_and_conditions',
        'available_variables',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public static function getAvailableVariables(): array
    {
        return [
            'Date Signed' => 'signed_date',
            'Agreement Date' => 'agreement_date',
            'Property Title' => 'property_title',
            'Property Address' => 'property_address',
            'Property Type' => 'property_type',
            'Property Subtype' => 'property_subtype',
            'Property City' => 'property_city',
            'Property State' => 'property_state',
            'Property Area' => 'property_area',
            'Seller Name' => 'seller_name',
            'Seller Email' => 'seller_email',
            'Seller Phone' => 'seller_phone',
            'Seller Address' => 'seller_address',
            'Buyer Name' => 'buyer_name',
            'Buyer Email' => 'buyer_email',
            'Buyer Phone' => 'buyer_phone',
            'Buyer Address' => 'buyer_address',
            'Sale Price' => 'sale_price',
            'Deposit Amount' => 'deposit_amount',
            'Balance Amount' => 'balance_amount',
            'Closing Date' => 'closing_date',
            'Agent Name' => 'agent_name',
            'Agent Email' => 'agent_email',
            'Agent Phone' => 'agent_phone',
            'Agency Name' => 'agency_name',
            'Agency Email' => 'agency_email',
            'Agency Phone' => 'agency_phone',
            'Current Date' => 'current_date',
            'Current Year' => 'current_year',
        ];
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

    public function salesAgreements(): HasMany
    {
        return $this->hasMany(SalesAgreement::class, 'template_id');
    }

    public function substituteVariables(array $data): string
    {
        return self::renderWithMergeTags($this->terms_and_conditions, $data);
    }

    public static function renderString(?string $string, array $data = []): string
    {
        return self::renderWithMergeTags($string, $data);
    }

    public static function renderWithMergeTags(?string $content, array $data = []): string
    {
        if (! $content) {
            return '';
        }

        $mergeData = [
            'signed_date' => $data['signed_date'] ?? '',
            'agreement_date' => $data['agreement_date'] ?? '',
            'property_title' => $data['property_title'] ?? '',
            'property_address' => $data['property_address'] ?? '',
            'property_type' => $data['property_type'] ?? '',
            'property_subtype' => $data['property_subtype'] ?? '',
            'property_city' => $data['property_city'] ?? '',
            'property_state' => $data['property_state'] ?? '',
            'property_area' => $data['property_area'] ?? '',
            'seller_name' => $data['seller_name'] ?? '',
            'seller_email' => $data['seller_email'] ?? '',
            'seller_phone' => $data['seller_phone'] ?? '',
            'seller_address' => $data['seller_address'] ?? '',
            'buyer_name' => $data['buyer_name'] ?? '',
            'buyer_email' => $data['buyer_email'] ?? '',
            'buyer_phone' => $data['buyer_phone'] ?? '',
            'buyer_address' => $data['buyer_address'] ?? '',
            'sale_price' => isset($data['sale_price']) && $data['sale_price'] ? '₦' . number_format($data['sale_price'], 2) : '',
            'deposit_amount' => isset($data['deposit_amount']) && $data['deposit_amount'] ? '₦' . number_format($data['deposit_amount'], 2) : '',
            'balance_amount' => isset($data['balance_amount']) && $data['balance_amount'] ? '₦' . number_format($data['balance_amount'], 2) : '',
            'closing_date' => $data['closing_date'] ?? '',
            'agent_name' => $data['agent_name'] ?? '',
            'agent_email' => $data['agent_email'] ?? '',
            'agent_phone' => $data['agent_phone'] ?? '',
            'agency_name' => $data['agency_name'] ?? '',
            'agency_email' => $data['agency_email'] ?? '',
            'agency_phone' => $data['agency_phone'] ?? '',
            'current_date' => now()->format('F j, Y'),
            'current_year' => now()->year,
        ];

        if (class_exists(\Filament\Forms\Components\RichEditor\RichContentRenderer::class) &&
            strpos($content, 'data-type="mergeTag"') !== false) {
            try {
                return \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content)
                    ->mergeTags($mergeData)
                    ->toHtml();
            } catch (\Exception $e) {
                \Log::warning('SalesAgreement RichContentRenderer failed: ' . $e->getMessage());
            }
        }

        return self::manualReplace($content, $mergeData);
    }

    private static function manualReplace(string $content, array $data): string
    {
        $replacements = [];
        $availableVars = self::getAvailableVariables();

        foreach ($data as $key => $value) {
            $label = array_search($key, $availableVars, true);
            if ($label !== false) {
                $replacements['{{ ' . $label . ' }}'] = (string) $value;
                $replacements['{{' . $label . '}}'] = (string) $value;
            }

            $replacements['{{ ' . $key . ' }}'] = (string) $value;
            $replacements['{{' . $key . '}}'] = (string) $value;

            $keyTitle = ucwords(str_replace('_', ' ', $key));
            $replacements['{{ ' . $keyTitle . ' }}'] = (string) $value;
            $replacements['{{' . $keyTitle . '}}'] = (string) $value;
        }

        $specialCases = [
            '{{ Sale Price }}' => $data['sale_price'] ?? '',
            '{{Sale Price}}' => $data['sale_price'] ?? '',
            '{{ Deposit Amount }}' => $data['deposit_amount'] ?? '',
            '{{Deposit Amount}}' => $data['deposit_amount'] ?? '',
            '{{ Balance Amount }}' => $data['balance_amount'] ?? '',
            '{{Balance Amount}}' => $data['balance_amount'] ?? '',
        ];

        $replacements = array_merge($replacements, $specialCases);

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    public function extractUsedVariables(): array
    {
        $availableVariables = self::getAvailableVariables();
        $usedVariables = [];

        foreach ($availableVariables as $label => $key) {
            $labelPlaceholder = '{{ ' . $label . ' }}';
            $keyPlaceholder = '{{ ' . $key . ' }}';

            if (str_contains($this->terms_and_conditions, $labelPlaceholder) ||
                str_contains($this->terms_and_conditions, $keyPlaceholder)) {
                $usedVariables[] = $key;
            }
        }

        return $usedVariables;
    }

    public static function getDefaultTemplate(?int $landlordId = null, ?int $agencyId = null, ?int $agentId = null): ?self
    {
        return self::query()
            ->when($landlordId, fn ($query) => $query->where('landlord_id', $landlordId))
            ->when($agencyId, fn ($query) => $query->where('agency_id', $agencyId))
            ->when($agentId, fn ($query) => $query->where('agent_id', $agentId))
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    public function setAsDefault(): void
    {
        $query = self::query()
            ->when($this->landlord_id, fn ($builder) => $builder->where('landlord_id', $this->landlord_id))
            ->when($this->agency_id, fn ($builder) => $builder->where('agency_id', $this->agency_id))
            ->when($this->agent_id, fn ($builder) => $builder->where('agent_id', $this->agent_id))
            ->where('is_default', true)
            ->where('id', '!=', $this->id);

        $query->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
