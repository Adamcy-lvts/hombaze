<?php

namespace App\Filament\PropertyOwner\Resources\PropertyViewingResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\PropertyOwner\Resources\PropertyViewingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyViewings extends ListRecords
{
    protected static string $resource = PropertyViewingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
