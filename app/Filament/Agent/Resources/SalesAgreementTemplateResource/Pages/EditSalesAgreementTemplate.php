<?php

namespace App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Agent\Resources\SalesAgreementTemplateResource;
use App\Models\SalesAgreementTemplate;
use Filament\Resources\Pages\EditRecord;

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
