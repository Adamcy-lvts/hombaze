<?php

namespace App\Filament\Tenant\Resources\RentPaymentResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Tenant\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentPayment extends EditRecord
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
