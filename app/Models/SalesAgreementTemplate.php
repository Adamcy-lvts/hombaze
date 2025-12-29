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

    public static function getDefaultContent(): string
    {
        return '
<div class="agreement-content" style="font-family: \'Inter\', sans-serif; line-height: 1.7; color: #111827;">
    <div style="text-align: center; margin-bottom: 32px; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
        <h1 style="font-size: 26px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; margin: 0;">Property Sale Agreement</h1>
        <p style="font-size: 13px; color: #6b7280; margin-top: 6px;">Effective Date: {{ Current Date }}</p>
    </div>

    <div style="margin-bottom: 22px;">
        <p>This Property Sale Agreement ("Agreement") is made on {{ Current Date }} between:</p>
        <p style="margin: 8px 0 0 0;"><strong>{{ Seller Name }}</strong>, of {{ Seller Address }}, hereinafter referred to as "the Seller",</p>
        <p style="margin: 6px 0 0 0;"><strong>{{ Buyer Name }}</strong>, of {{ Buyer Address }}, hereinafter referred to as "the Buyer."</p>
    </div>

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">1. PROPERTY DETAILS</h3>
        <p>The Seller agrees to sell and the Buyer agrees to purchase the property described below:</p>
        <p><strong>Property Type:</strong> {{ Property Type }}</p>
        <p><strong>Property Address:</strong> {{ Property Address }}, {{ Property Area }}, {{ Property City }}, {{ Property State }}</p>
    </div>

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">2. PURCHASE PRICE</h3>
        <p>The purchase price for the Property is {{ Sale Price }}, agreed by both parties.</p>
    </div>

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">3. PAYMENT TERMS</h3>
        {{#if has_deposit}}
        <p>The Buyer shall pay a deposit of {{ Deposit Amount }} upon signing this Agreement.</p>
        {{/if}}
        {{#if has_balance}}
        <p>The balance of {{ Balance Amount }} shall be paid {{#if has_closing_date}}on or before {{ Closing Date }}{{/if}}.</p>
        {{/if}}
        {{#if is_full_payment}}
        <p>The full purchase price of {{ Sale Price }} has been paid in full on the date of this Agreement.</p>
        {{/if}}
        <p>Payment shall be made via bank transfer or any other agreed method.</p>
    </div>

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">4. POSSESSION</h3>
        <p>Vacant possession of the Property shall be delivered to the Buyer upon full payment and completion of necessary documentation.</p>
    </div>

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">5. TITLE & DOCUMENTS</h3>
        <p>The Seller warrants that the Property is free from encumbrances except as disclosed and agrees to provide all relevant title documents upon completion.</p>
    </div>

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">6. FEES & CHARGES</h3>
        <p>Unless otherwise agreed:</p>
        <p>Statutory fees, documentation costs, and taxes shall be borne by the Buyer.</p>
        <p>Outstanding charges up to the date of possession shall be settled by the Seller.</p>
    </div>

    {{#if has_agency_or_agent}}
    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">7. AGENCY</h3>
        <p>Where applicable, this transaction is facilitated{{#if has_agency}} by {{ Agency Name }}{{/if}}{{#if has_agent}} through {{ Agent Name }}{{/if}}.</p>
    </div>
    {{/if}}

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">8. DEFAULT</h3>
        <p>If either party fails to meet their obligations, the non-defaulting party may terminate this Agreement and seek appropriate remedies.</p>
    </div>

    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">9. GOVERNING LAW</h3>
        <p>This Agreement shall be governed by the laws of the Federal Republic of Nigeria.</p>
    </div>

    <div style="margin-bottom: 10px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px 0;">10. SIGNATURES</h3>
        <p>IN WITNESS WHEREOF, the Parties have executed this Agreement on the date above.</p>
    </div>
</div>
        ';
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

        $salePriceValue = self::normalizeAmount($data['sale_price'] ?? 0);
        $depositValue = self::normalizeAmount($data['deposit_amount'] ?? 0);
        $balanceValue = self::normalizeAmount($data['balance_amount'] ?? 0);
        $hasDeposit = $depositValue > 0;
        $hasBalance = $balanceValue > 0;
        $hasSalePrice = $salePriceValue > 0;
        $hasClosingDate = ! empty($data['closing_date']);
        $hasAgency = ! empty($data['agency_name']);
        $hasAgent = ! empty($data['agent_name']);
        $hasAgencyOrAgent = $hasAgency || $hasAgent;
        $isFullPayment = $hasSalePrice && ! $hasDeposit && ! $hasBalance;

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
            'sale_price' => $salePriceValue ? '₦' . number_format($salePriceValue, 2) : '',
            'deposit_amount' => $depositValue ? '₦' . number_format($depositValue, 2) : '',
            'balance_amount' => $balanceValue ? '₦' . number_format($balanceValue, 2) : '',
            'closing_date' => $data['closing_date'] ?? '',
            'agent_name' => $data['agent_name'] ?? '',
            'agent_email' => $data['agent_email'] ?? '',
            'agent_phone' => $data['agent_phone'] ?? '',
            'agency_name' => $data['agency_name'] ?? '',
            'agency_email' => $data['agency_email'] ?? '',
            'agency_phone' => $data['agency_phone'] ?? '',
            'current_date' => now()->format('F j, Y'),
            'current_year' => now()->year,
            'has_deposit' => $hasDeposit,
            'has_balance' => $hasBalance,
            'has_closing_date' => $hasClosingDate,
            'has_agency_or_agent' => $hasAgencyOrAgent,
            'has_agency' => $hasAgency,
            'has_agent' => $hasAgent,
            'is_full_payment' => $isFullPayment,
        ];

        $content = self::applyConditionalBlocks($content, $mergeData);

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

    private static function normalizeAmount(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $sanitized = preg_replace('/[^\d.]/', '', (string) $value);

        return $sanitized === '' ? 0.0 : (float) $sanitized;
    }

    private static function applyConditionalBlocks(string $content, array $data): string
    {
        $pattern = '/\{\{\s*#if\s+([a-zA-Z0-9_]+)\s*\}\}(.*?)\{\{\s*\/if\s*\}\}/s';

        do {
            $content = preg_replace_callback(
                $pattern,
                function (array $matches) use ($data): string {
                    $key = $matches[1];
                    $value = $data[$key] ?? false;

                    return self::isTruthy($value) ? $matches[2] : '';
                },
                $content,
                -1,
                $replacements
            );
        } while ($replacements > 0);

        $content = preg_replace('/\{\{\s*#if\s+[a-zA-Z0-9_]+\s*\}\}/', '', $content);
        $content = preg_replace('/\{\{\s*\/if\s*\}\}/', '', $content);

        return $content;
    }

    private static function isTruthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value > 0;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            if ($normalized === '' || $normalized === '0' || $normalized === 'false' || $normalized === 'no') {
                return false;
            }

            return true;
        }

        return ! empty($value);
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

    public static function ensureDefaultForLandlord(int $landlordId): self
    {
        $existing = self::getDefaultTemplate($landlordId);
        if ($existing) {
            return $existing;
        }

        $template = new self();
        $template->terms_and_conditions = self::getDefaultContent();

        return self::create([
            'landlord_id' => $landlordId,
            'name' => 'Standard Sales Agreement',
            'description' => 'Default sales agreement template',
            'terms_and_conditions' => $template->terms_and_conditions,
            'available_variables' => $template->extractUsedVariables(),
            'is_active' => true,
            'is_default' => true,
        ]);
    }

    public static function ensureDefaultForAgency(int $agencyId): self
    {
        $existing = self::getDefaultTemplate(null, $agencyId);
        if ($existing) {
            return $existing;
        }

        $template = new self();
        $template->terms_and_conditions = self::getDefaultContent();

        return self::create([
            'agency_id' => $agencyId,
            'name' => 'Standard Sales Agreement',
            'description' => 'Default sales agreement template',
            'terms_and_conditions' => $template->terms_and_conditions,
            'available_variables' => $template->extractUsedVariables(),
            'is_active' => true,
            'is_default' => true,
        ]);
    }

    public static function ensureDefaultForAgent(int $agentId): self
    {
        $existing = self::getDefaultTemplate(null, null, $agentId);
        if ($existing) {
            return $existing;
        }

        $template = new self();
        $template->terms_and_conditions = self::getDefaultContent();

        return self::create([
            'agent_id' => $agentId,
            'name' => 'Standard Sales Agreement',
            'description' => 'Default sales agreement template',
            'terms_and_conditions' => $template->terms_and_conditions,
            'available_variables' => $template->extractUsedVariables(),
            'is_active' => true,
            'is_default' => true,
        ]);
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
