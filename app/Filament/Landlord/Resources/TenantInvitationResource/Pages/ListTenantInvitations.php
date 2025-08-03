<?php

namespace App\Filament\Landlord\Resources\TenantInvitationResource\Pages;

use App\Filament\Landlord\Resources\TenantInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantInvitations extends ListRecords
{
    protected static string $resource = TenantInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
