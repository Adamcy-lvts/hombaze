<?php

namespace App\Filament\Tenant\Resources\MaintenanceRequestResource\Pages;

use App\Filament\Tenant\Resources\MaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceRequest extends EditRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
