<?php

namespace App\Filament\Agent\Resources\PropertyInquiryResource\Pages;

use App\Filament\Agent\Resources\PropertyInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyInquiry extends EditRecord
{
    protected static string $resource = PropertyInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
