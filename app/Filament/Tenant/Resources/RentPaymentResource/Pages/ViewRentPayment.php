<?php

namespace App\Filament\Tenant\Resources\RentPaymentResource\Pages;

use App\Filament\Tenant\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRentPayment extends ViewRecord
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
