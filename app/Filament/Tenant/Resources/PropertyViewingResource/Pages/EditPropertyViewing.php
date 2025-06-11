<?php

namespace App\Filament\Tenant\Resources\PropertyViewingResource\Pages;

use App\Filament\Tenant\Resources\PropertyViewingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyViewing extends EditRecord
{
    protected static string $resource = PropertyViewingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
