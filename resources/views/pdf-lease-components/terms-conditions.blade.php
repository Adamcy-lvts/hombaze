{{-- Update your terms-conditions.blade.php --}}
<div class="mt-3">
    <div class="border-b border-gray-300 pb-2 mb-3">
        <h2 class="text-sm font-semibold text-gray-900 text-center uppercase tracking-wide">Terms and Conditions</h2>
    </div>
    <div class="text-xs leading-relaxed text-gray-800 space-y-2 prose max-w-none">
        @php
            $renderContent = $content ?? '';
            // If a $record is available, use it to substitute placeholders as a final pass.
            if (isset($record)) {
                $subData = [
                    'property_title' => $record->property->title ?? '',
                    'property_address' => $record->property->address ?? '',
                    'property_type' => $record->property->propertyType->name ?? '',
                    'property_subtype' => $record->property->propertySubtype->name ?? '',
                    'property_area' => $record->property->area->name ?? '',
                    'property_city' => $record->property->city->name ?? '',
                    'property_state' => $record->property->state->name ?? '',
                    'landlord_name' => $record->landlord->name ?? '',
                    'landlord_email' => $record->landlord->email ?? '',
                    'landlord_phone' => $record->landlord->phone_number ?? '',
                    'landlord_address' => $record->landlord->address ?? '',
                    'tenant_name' => $record->tenant->name ?? '',
                    'tenant_email' => $record->tenant->email ?? '',
                    'tenant_phone' => $record->tenant->phone_number ?? '',
                    'tenant_address' => $record->tenant->address ?? '',
                    'lease_start_date' => $record->start_date ? $record->start_date->format('F j, Y') : '',
                    'lease_end_date' => $record->end_date ? $record->end_date->format('F j, Y') : '',
                    'lease_duration_months' => $record->start_date && $record->end_date ? $record->start_date->diffInMonths($record->end_date) : '',
                    'rent_amount' => $record->yearly_rent ?? $record->monthly_rent ?? null,
                    'payment_frequency' => $record->payment_frequency ?? '',
                    'security_deposit' => $record->security_deposit ?? null,
                    'service_charge' => $record->service_charge ?? null,
                    'legal_fee' => $record->legal_fee ?? null,
                    'agency_fee' => $record->agency_fee ?? null,
                    'caution_deposit' => $record->caution_deposit ?? null,
                    'grace_period_days' => $record->grace_period_days ?? null,
                    'renewal_option' => $record->renewal_option ?? null,
                    'signed_date' => $record->signed_date ? $record->signed_date->format('F j, Y') : '',
                ];

                $renderContent = \App\Models\LeaseTemplate::renderWithMergeTags($renderContent, $subData);
            }
        @endphp

        @if (class_exists(\Filament\Forms\Components\RichEditor\RichContentRenderer::class))
            {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($renderContent)->toHtml() !!}
        @else
            {!! str($renderContent)->sanitizeHtml() !!}
        @endif
    </div>
</div>
