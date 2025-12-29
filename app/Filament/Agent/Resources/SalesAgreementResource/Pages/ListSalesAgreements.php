<?php

namespace App\Filament\Agent\Resources\SalesAgreementResource\Pages;

use App\Filament\Agent\Resources\SalesAgreementResource;
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
