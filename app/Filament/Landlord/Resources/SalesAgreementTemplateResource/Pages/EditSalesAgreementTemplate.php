<?php

namespace App\Filament\Landlord\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Landlord\Resources\SalesAgreementTemplateResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\SalesAgreementTemplate;

class EditSalesAgreementTemplate extends EditRecord
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $template = new SalesAgreementTemplate();
        $template->terms_and_conditions = $data['terms_and_conditions'];
        $data['available_variables'] = $template->extractUsedVariables();

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->is_default) {
            $this->record->setAsDefault();
        }
    }
}
