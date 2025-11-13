<?php

namespace App\Filament\Landlord\Resources\PropertyOwnerResource\Pages;

use Filament\Notifications\Notification;
use App\Filament\Landlord\Resources\PropertyOwnerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CreatePropertyOwner extends CreateRecord
{
    protected static string $resource = PropertyOwnerResource::class;

    public function getTitle(): string
    {
        return 'Create My Property Owner Profile';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically link the profile to the current user
        $data['user_id'] = Auth::id();

        // If no email provided, use the user's email
        if (empty($data['email'])) {
            $data['email'] = Auth::user()->email;
        }

        // Set default values
        $data['is_active'] = true;
        $data['country'] = $data['country'] ?? 'Nigeria';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Send notification about profile creation
        Notification::make()
            ->title('Profile Created Successfully')
            ->body('Your property owner profile has been created. You can now manage your properties.')
            ->success()
            ->send();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}