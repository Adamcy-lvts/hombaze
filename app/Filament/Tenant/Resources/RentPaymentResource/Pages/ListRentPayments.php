<?php

namespace App\Filament\Tenant\Resources\RentPaymentResource\Pages;

use App\Filament\Tenant\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentPayments extends ListRecords
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
