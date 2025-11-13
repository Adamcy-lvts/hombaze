<?php

namespace App\Filament\Agency\Resources\AgentResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Agency\Resources\AgentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgents extends ListRecords
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
