<?php

namespace App\Filament\Agent\Resources\TenantInvitationResource\Pages;

use App\Filament\Agent\Resources\TenantInvitationResource;
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
