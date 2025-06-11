<?php

namespace App\Filament\Tenant\Resources\RentPaymentResource\Pages;

use App\Filament\Tenant\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentPayment extends EditRecord
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
