<?php

namespace App\Filament\Landlord\Resources\TenantInvitationResource\Pages;

use App\Filament\Landlord\Resources\TenantInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantInvitation extends EditRecord
{
    protected static string $resource = TenantInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
