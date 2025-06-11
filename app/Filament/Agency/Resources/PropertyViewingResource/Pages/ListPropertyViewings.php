<?php

namespace App\Filament\Agency\Resources\PropertyViewingResource\Pages;

use App\Filament\Agency\Resources\PropertyViewingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyViewings extends ListRecords
{
    protected static string $resource = PropertyViewingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - viewings are requested by users browsing properties
        ];
    }
}
