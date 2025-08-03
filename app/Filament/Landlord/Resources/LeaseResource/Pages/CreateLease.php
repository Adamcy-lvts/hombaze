<?php

namespace App\Filament\Landlord\Resources\LeaseResource\Pages;

use App\Filament\Landlord\Resources\LeaseResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLease extends CreateRecord
{
    protected static string $resource = LeaseResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['landlord_id'] = Auth::id();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
