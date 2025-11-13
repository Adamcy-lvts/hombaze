<?php

namespace App\Filament\Agency\Resources\PropertyResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Agency\Resources\PropertyResource;
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
