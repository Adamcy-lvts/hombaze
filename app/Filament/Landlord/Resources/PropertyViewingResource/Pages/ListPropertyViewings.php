<?php

namespace App\Filament\Landlord\Resources\PropertyViewingResource\Pages;

use App\Filament\Landlord\Resources\PropertyViewingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyViewings extends ListRecords
{
    protected static string $resource = PropertyViewingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
