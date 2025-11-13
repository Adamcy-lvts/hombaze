<?php

namespace App\Filament\Resources\PropertyFeatureResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\PropertyFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyFeature extends EditRecord
{
    protected static string $resource = PropertyFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
