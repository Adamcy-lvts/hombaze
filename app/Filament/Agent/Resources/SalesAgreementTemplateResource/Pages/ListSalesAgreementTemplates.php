<?php

namespace App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Agent\Resources\SalesAgreementTemplateResource;
use App\Models\SalesAgreementTemplate;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListSalesAgreementTemplates extends ListRecords
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    public function mount(): void
    {
        parent::mount();

        $agentId = Auth::user()?->agentProfile?->id;
        if ($agentId) {
            SalesAgreementTemplate::ensureDefaultForAgent($agentId);
        }
    }
}
