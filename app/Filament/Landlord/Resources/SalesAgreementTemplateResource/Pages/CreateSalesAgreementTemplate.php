<?php

namespace App\Filament\Landlord\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Landlord\Resources\SalesAgreementTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\SalesAgreementTemplate;
use Illuminate\Support\Facades\Auth;

class CreateSalesAgreementTemplate extends CreateRecord
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['landlord_id'] = Auth::id();
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
