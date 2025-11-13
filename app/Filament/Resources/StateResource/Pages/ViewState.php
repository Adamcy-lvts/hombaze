<?php

namespace App\Filament\Resources\StateResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewState extends ViewRecord
{
    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
