<?php

namespace App\Filament\Agency\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Agency\Resources\SalesAgreementTemplateResource;
use App\Models\SalesAgreementTemplate;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListSalesAgreementTemplates extends ListRecords
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    public function mount(): void
    {
        parent::mount();

        $agency = Filament::getTenant();
        if ($agency) {
            SalesAgreementTemplate::ensureDefaultForAgency($agency->id);
        }
    }
}
