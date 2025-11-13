<?php

namespace App\Filament\Landlord\Resources\PropertyOwnerResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Filament\Landlord\Resources\PropertyOwnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPropertyOwner extends EditRecord
{
    protected static string $resource = PropertyOwnerResource::class;

    public function getTitle(): string
    {
        return 'Edit My Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_properties')
                ->label('View My Properties')
                ->icon('heroicon-o-home')
                ->url(fn () => route('filament.landlord.resources.properties.index'))
                ->color('info'),

            Action::make('download_profile')
                ->label('Download Profile')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Future: Generate PDF profile
                    Notification::make()
                        ->title('Feature Coming Soon')
                        ->body('Profile download will be available soon.')
                        ->info()
                        ->send();
                })
                ->color('gray'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // Send notification about profile update
        Notification::make()
            ->title('Profile Updated Successfully')
            ->body('Your property owner profile has been updated.')
            ->success()
            ->send();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-fill with user data if empty
        if (empty($data['email'])) {
            $data['email'] = auth()->user()->email;
        }

        return $data;
    }
}