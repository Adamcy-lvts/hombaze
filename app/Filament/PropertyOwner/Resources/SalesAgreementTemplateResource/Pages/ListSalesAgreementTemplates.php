<?php

namespace App\Filament\PropertyOwner\Resources\SalesAgreementTemplateResource\Pages;

use App\Filament\PropertyOwner\Resources\SalesAgreementTemplateResource;
use App\Models\SalesAgreementTemplate;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListSalesAgreementTemplates extends ListRecords
{
    protected static string $resource = SalesAgreementTemplateResource::class;

    public function mount(): void
    {
        parent::mount();

        if (Auth::check()) {
            SalesAgreementTemplate::ensureDefaultForLandlord(Auth::id());
        }
    }
}
