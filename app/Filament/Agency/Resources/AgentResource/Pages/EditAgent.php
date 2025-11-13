<?php

namespace App\Filament\Agency\Resources\AgentResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Agency\Resources\AgentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgent extends EditRecord
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
