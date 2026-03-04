<?php

namespace App\Filament\PropertyOwner\Resources\MaintenanceRequestResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\PropertyOwner\Resources\MaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceRequest extends EditRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
