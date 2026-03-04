<?php

namespace App\Filament\Resources\AgencyVerificationResource\Pages;

use App\Filament\Resources\AgencyVerificationResource;
use Filament\Resources\Pages\ListRecords;

class ListAgencyVerifications extends ListRecords
{
    protected static string $resource = AgencyVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
