<?php

namespace App\Filament\PropertyOwner\Resources\RentPaymentResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\PropertyOwner\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentPayments extends ListRecords
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
