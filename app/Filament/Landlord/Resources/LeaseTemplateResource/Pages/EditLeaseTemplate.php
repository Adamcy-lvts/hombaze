<?php

namespace App\Filament\Landlord\Resources\LeaseTemplateResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Models\LeaseTemplate;
use App\Filament\Landlord\Resources\LeaseTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaseTemplate extends EditRecord
{
    protected static string $resource = LeaseTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract and store used variables
        $template = new LeaseTemplate();
        $template->terms_and_conditions = $data['terms_and_conditions'];
        $data['available_variables'] = $template->extractUsedVariables();
        
        return $data;
    }

    protected function afterSave(): void
    {
        // If this is set as default, update it
        if ($this->record->is_default) {
            $this->record->setAsDefault();
        }
    }
}