<?php

namespace App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Agent\Resources\SalesAgreementTemplateResource;
use App\Models\SalesAgreementTemplate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSalesAgreementTemplate extends CreateRecord
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['agent_id'] = Auth::user()?->agentProfile?->id;

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
