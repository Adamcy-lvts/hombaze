<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('toggleStatus')
                ->label(fn ($record) => $record->is_active ? 'Deactivate User' : 'Activate User')
                ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
            Actions\Action::make('verifyUser')
                ->label('Verify User')
                ->color('success')
                ->icon('heroicon-o-shield-check')
                ->visible(fn ($record) => !$record->is_verified)
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update([
                    'is_verified' => true,
                    'email_verified_at' => now()
                ])),
        ];
    }
}
