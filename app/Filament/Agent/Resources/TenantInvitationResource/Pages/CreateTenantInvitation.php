<?php

namespace App\Filament\Agent\Resources\TenantInvitationResource\Pages;

use Exception;
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
            throw new Exception('Agent profile not found.');
        }
        
        $data['agent_id'] = $agentProfile->id;
        $data['invited_from_ip'] = request()->ip();
        $data['status'] = 'pending';

        // Handle landlord_id and property_owner_id based on property owner status
        if (!empty($data['property_id'])) {
            $property = \App\Models\Property::find($data['property_id']);
            
            if ($property && $property->owner) {
                // Always set property_owner_id to track the actual property owner
                $data['property_owner_id'] = $property->owner->id;
                
                // If owner is a platform user, use their user_id as landlord_id
                if ($property->owner->user_id) {
                    $data['landlord_id'] = $property->owner->user_id;
                } else {
                    // Owner is not a platform user, use agent's user_id as landlord_id
                    $data['landlord_id'] = $user->id;
                }
            } else {
                // Fallback: No owner found (should never happen), use agent's user_id
                $data['landlord_id'] = $user->id;
            }
        } else {
            // No property selected (should never happen), use agent's user_id
            $data['landlord_id'] = $user->id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
