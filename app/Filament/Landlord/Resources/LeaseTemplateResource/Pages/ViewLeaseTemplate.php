<?php

namespace App\Filament\Landlord\Resources\LeaseTemplateResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Landlord\Resources\LeaseTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLeaseTemplate extends ViewRecord
{
    protected static string $resource = LeaseTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}