<?php

namespace App\Filament\Landlord\Resources\PropertyOwnerResource\Pages;

use App\Filament\Landlord\Resources\PropertyOwnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyOwner;

class ListPropertyOwners extends ListRecords
{
    protected static string $resource = PropertyOwnerResource::class;

    protected function getHeaderActions(): array
    {
        // Only show create action if user doesn't have a profile yet
        $hasProfile = PropertyOwner::where('user_id', Auth::id())->exists();

        return $hasProfile ? [] : [
            Actions\CreateAction::make()
                ->label('Create My Profile')
                ->icon('heroicon-o-user-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'My Property Owner Profile';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Could add profile completion widget here
        ];
    }
}