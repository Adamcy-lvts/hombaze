<?php

namespace App\Filament\Agency\Resources\PropertyViewingResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Agency\Resources\PropertyViewingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPropertyViewing extends ViewRecord
{
    protected static string $resource = PropertyViewingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
