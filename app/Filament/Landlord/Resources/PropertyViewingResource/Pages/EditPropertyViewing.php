<?php

namespace App\Filament\Landlord\Resources\PropertyViewingResource\Pages;

use App\Filament\Landlord\Resources\PropertyViewingResource;
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
