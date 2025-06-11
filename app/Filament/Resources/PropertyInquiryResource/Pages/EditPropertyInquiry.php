<?php

namespace App\Filament\Resources\PropertyInquiryResource\Pages;

use App\Filament\Resources\PropertyInquiryResource;
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
