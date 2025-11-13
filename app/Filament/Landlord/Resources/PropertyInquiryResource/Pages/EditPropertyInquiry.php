<?php

namespace App\Filament\Landlord\Resources\PropertyInquiryResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Landlord\Resources\PropertyInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyInquiry extends EditRecord
{
    protected static string $resource = PropertyInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
