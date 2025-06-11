<?php

namespace App\Filament\Landlord\Resources\PropertyInquiryResource\Pages;

use App\Filament\Landlord\Resources\PropertyInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyInquiries extends ListRecords
{
    protected static string $resource = PropertyInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
