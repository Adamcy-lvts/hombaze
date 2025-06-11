<?php

namespace App\Filament\Tenant\Resources\LeaseResource\Pages;

use App\Filament\Tenant\Resources\LeaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLease extends CreateRecord
{
    protected static string $resource = LeaseResource::class;
}
