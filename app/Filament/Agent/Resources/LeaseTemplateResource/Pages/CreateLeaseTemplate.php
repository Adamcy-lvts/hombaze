<?php

namespace App\Filament\Agent\Resources\LeaseTemplateResource\Pages;

use App\Filament\Agent\Resources\LeaseTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLeaseTemplate extends CreateRecord
{
    protected static string $resource = LeaseTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['landlord_id'] = Auth::id();
        return $data;
    }
}
