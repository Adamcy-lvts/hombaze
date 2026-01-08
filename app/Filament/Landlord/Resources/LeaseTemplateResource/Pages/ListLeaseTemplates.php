<?php

namespace App\Filament\Landlord\Resources\LeaseTemplateResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Landlord\Resources\LeaseTemplateResource;
use App\Models\LeaseTemplate;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListLeaseTemplates extends ListRecords
{
    protected static string $resource = LeaseTemplateResource::class;

    public function mount(): void
    {
        parent::mount();

        if (Auth::check()) {
            LeaseTemplate::ensureDefaultForLandlord(Auth::id());
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
