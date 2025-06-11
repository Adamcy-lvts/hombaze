<?php

namespace App\Filament\Agency\Resources\PropertyInquiryResource\Pages;

use App\Filament\Agency\Resources\PropertyInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyInquiries extends ListRecords
{
    protected static string $resource = PropertyInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - inquiries come from users browsing properties
        ];
    }
}
