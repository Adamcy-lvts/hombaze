<?php

namespace App\Filament\PropertyOwner\Resources\TenantResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\PropertyOwner\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
