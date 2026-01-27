<?php

namespace App\Filament\Landlord\Resources\TenantResource\Pages;

use App\Filament\Landlord\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    /**
     * Automatically set the landlord_id before creating the tenant record
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['landlord_id'] = Auth::id();

        \Log::info('[CreateTenant] mutateFormDataBeforeCreate', [
            'landlord_id' => $data['landlord_id'],
            'data_keys' => array_keys($data),
        ]);

        return $data;
    }

    /**
     * Redirect to the tenants list after creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
