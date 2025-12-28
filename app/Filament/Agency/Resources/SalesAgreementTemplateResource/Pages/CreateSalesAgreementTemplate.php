<?php

namespace App\Filament\Agency\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Agency\Resources\SalesAgreementTemplateResource;
use App\Models\SalesAgreementTemplate;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesAgreementTemplate extends CreateRecord
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $agency = Filament::getTenant();
        $data['agency_id'] = $agency?->id;

        $template = new SalesAgreementTemplate();
        $template->terms_and_conditions = $data['terms_and_conditions'];
        $data['available_variables'] = $template->extractUsedVariables();

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->is_default) {
            $this->record->setAsDefault();
        }
    }
}
