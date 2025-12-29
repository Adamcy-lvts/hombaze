<?php

namespace App\Filament\Agency\Resources\SalesAgreementResource\Pages;

use App\Filament\Agency\Resources\SalesAgreementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalesAgreements extends ListRecords
{
    protected static string $resource = SalesAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
