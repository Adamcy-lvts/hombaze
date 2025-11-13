<?php

namespace App\Filament\Tenant\Resources\MaintenanceRequestResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Tenant\Resources\MaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceRequests extends ListRecords
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
