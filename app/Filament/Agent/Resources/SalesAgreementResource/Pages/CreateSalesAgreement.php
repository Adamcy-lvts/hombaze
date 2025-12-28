<?php

namespace App\Filament\Agent\Resources\SalesAgreementResource\Pages;

use App\Enums\PropertyStatus;
use App\Filament\Agent\Resources\SalesAgreementResource;
use App\Models\Property;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSalesAgreement extends CreateRecord
{
    protected static string $resource = SalesAgreementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['agent_id'] = Auth::user()?->agentProfile?->id;

        $property = Property::find($data['property_id'] ?? null);
        if ($property) {
            if ($property->listing_type !== 'sale' || $property->status !== PropertyStatus::SOLD->value) {
                abort(403, 'Sales agreements can only be created for sold properties.');
            }

            if ($property->salesAgreement) {
                abort(409, 'A sales agreement already exists for this property.');
            }

            $owner = $property->owner;
            $data['seller_name'] = $data['seller_name'] ?: $owner?->name;
            $data['seller_email'] = $data['seller_email'] ?: $owner?->email;
            $data['seller_phone'] = $data['seller_phone'] ?: $owner?->phone;
            $data['seller_address'] = $data['seller_address'] ?: $owner?->address;
            $data['sale_price'] = $data['sale_price'] ?? $property->price;
            $data['agency_id'] = $property->agency_id;
            $data['landlord_id'] = $owner?->user_id;
        }

        $data['balance_amount'] = $this->calculateBalance($data);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    private function calculateBalance(array $data): ?float
    {
        $salePrice = isset($data['sale_price']) ? (float) $data['sale_price'] : null;
        $deposit = isset($data['deposit_amount']) ? (float) $data['deposit_amount'] : 0.0;

        if ($salePrice === null) {
            return null;
        }

        return max($salePrice - $deposit, 0);
    }
}
