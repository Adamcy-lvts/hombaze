<?php

namespace App\Filament\Tenant\Resources\MaintenanceRequestResource\Pages;

use App\Filament\Tenant\Resources\MaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceRequests extends ListRecords
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
