<?php

namespace App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\Agent\Resources\SalesAgreementTemplateResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesAgreementTemplate extends ViewRecord
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    protected string $view = 'filament.pages.sales-agreement-template-view';

    public function getTitle(): string
    {
        return 'Sales Agreement Template - ' . $this->record->name;
    }
}
