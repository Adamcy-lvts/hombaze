<?php

namespace App\Filament\Agent\Resources\PropertyInquiryResource\Pages;

use App\Filament\Agent\Resources\PropertyInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPropertyInquiry extends ViewRecord
{
    protected static string $resource = PropertyInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
