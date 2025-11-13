<?php

namespace App\Filament\Landlord\Resources\TenantResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Landlord\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
