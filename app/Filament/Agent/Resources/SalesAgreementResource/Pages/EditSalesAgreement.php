<?php

namespace App\Filament\Agent\Resources\SalesAgreementResource\Pages;

use App\Filament\Agent\Resources\SalesAgreementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalesAgreement extends EditRecord
{
    protected static string $resource = SalesAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
