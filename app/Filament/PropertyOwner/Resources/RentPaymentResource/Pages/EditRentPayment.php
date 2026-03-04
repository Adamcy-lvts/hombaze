<?php

namespace App\Filament\PropertyOwner\Resources\RentPaymentResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\PropertyOwner\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentPayment extends EditRecord
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
