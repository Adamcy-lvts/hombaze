<?php

namespace App\Filament\Agent\Resources\TenantInvitationResource\Pages;

use App\Filament\Agent\Resources\TenantInvitationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTenantInvitation extends CreateRecord
{
    protected static string $resource = TenantInvitationResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $agentProfile = $user->agentProfile;
        
        if (!$agentProfile) {
            throw new \Exception('Agent profile not found.');
        }
        
        $data['agent_id'] = $agentProfile->id;
        $data['invited_from_ip'] = request()->ip();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
