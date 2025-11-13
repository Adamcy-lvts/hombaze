<?php

namespace App\Filament\Landlord\Resources\MaintenanceRequestResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Landlord\Resources\MaintenanceRequestResource;
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
