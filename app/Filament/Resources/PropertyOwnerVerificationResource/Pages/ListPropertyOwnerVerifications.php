<?php

namespace App\Filament\Resources\PropertyOwnerVerificationResource\Pages;

use App\Filament\Resources\PropertyOwnerVerificationResource;
use Filament\Resources\Pages\ListRecords;

class ListPropertyOwnerVerifications extends ListRecords
{
    protected static string $resource = PropertyOwnerVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
