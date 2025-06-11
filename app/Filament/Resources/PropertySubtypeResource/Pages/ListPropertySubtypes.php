<?php

namespace App\Filament\Resources\PropertySubtypeResource\Pages;

use App\Filament\Resources\PropertySubtypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertySubtypes extends ListRecords
{
    protected static string $resource = PropertySubtypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
