<?php

namespace App\Filament\Landlord\Resources\LeaseTemplateResource\Pages;

use App\Filament\Landlord\Resources\LeaseTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaseTemplates extends ListRecords
{
    protected static string $resource = LeaseTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}