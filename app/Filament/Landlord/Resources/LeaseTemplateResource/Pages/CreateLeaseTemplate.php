<?php

namespace App\Filament\Landlord\Resources\LeaseTemplateResource\Pages;

use App\Filament\Landlord\Resources\LeaseTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLeaseTemplate extends CreateRecord
{
    protected static string $resource = LeaseTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['landlord_id'] = Auth::id();
        
        // Extract and store used variables
        $template = new \App\Models\LeaseTemplate();
        $template->terms_and_conditions = $data['terms_and_conditions'];
        $data['available_variables'] = $template->extractUsedVariables();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // If this is set as default, update it
        if ($this->record->is_default) {
            $this->record->setAsDefault();
        }
    }
}