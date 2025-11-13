<?php

namespace App\Filament\Landlord\Resources\PropertyResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Landlord\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
