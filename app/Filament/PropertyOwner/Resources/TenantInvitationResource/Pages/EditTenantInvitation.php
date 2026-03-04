<?php

namespace App\Filament\PropertyOwner\Resources\TenantInvitationResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\PropertyOwner\Resources\TenantInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantInvitation extends EditRecord
{
    protected static string $resource = TenantInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
