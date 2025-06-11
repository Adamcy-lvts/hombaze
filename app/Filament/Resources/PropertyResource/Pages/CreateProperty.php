<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PropertyResource;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;
}
