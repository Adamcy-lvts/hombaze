<?php

namespace App\Filament\Agent\Resources\LeaseResource\Pages;

use App\Filament\Agent\Resources\LeaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeases extends ListRecords
{
    protected static string $resource = LeaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
