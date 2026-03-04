<?php

namespace App\Filament\PropertyOwner\Resources\LeaseResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\PropertyOwner\Resources\LeaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLease extends EditRecord
{
    protected static string $resource = LeaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
