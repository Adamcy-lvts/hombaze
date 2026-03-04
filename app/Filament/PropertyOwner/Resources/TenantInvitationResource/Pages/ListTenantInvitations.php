<?php

namespace App\Filament\PropertyOwner\Resources\TenantInvitationResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\PropertyOwner\Resources\TenantInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantInvitations extends ListRecords
{
    protected static string $resource = TenantInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
