<?php

namespace App\Filament\Landlord\Resources\TenantInvitationResource\Pages;

use App\Filament\Landlord\Resources\TenantInvitationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTenantInvitation extends CreateRecord
{
    protected static string $resource = TenantInvitationResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['landlord_id'] = Auth::id();
        $data['invited_from_ip'] = request()->ip();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
