<?php

namespace App\Filament\Resources\AgentVerificationResource\Pages;

use App\Filament\Resources\AgentVerificationResource;
use Filament\Resources\Pages\ListRecords;

class ListAgentVerifications extends ListRecords
{
    protected static string $resource = AgentVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
