<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Services\ListingCreditService;
use App\Models\ListingPackage;
use App\Models\ListingAddon;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('toggleStatus')
                ->label(fn ($record) => $record->is_active ? 'Deactivate User' : 'Activate User')
                ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
            Action::make('verifyUser')
                ->label('Verify User')
                ->color('success')
                ->icon('heroicon-o-shield-check')
                ->visible(fn ($record) => !$record->is_verified)
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update([
                    'is_verified' => true,
                    'email_verified_at' => now()
                ])),
            Action::make('grantListingBundle')
                ->label('Grant Listing Package')
                ->color('primary')
                ->icon('heroicon-o-briefcase')
                ->form([
                    \Filament\Forms\Components\Select::make('package_id')
                        ->label('Package')
                        ->options(ListingPackage::query()
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                            ->toArray())
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $package = ListingPackage::find($data['package_id']);
                    if ($package) {
                        ListingCreditService::grantPackage($record, $package, 'admin_grant');
                    }
                }),
            Action::make('grantListingAddon')
                ->label('Grant Listing Add-on')
                ->color('info')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    \Filament\Forms\Components\Select::make('addon_id')
                        ->label('Add-on')
                        ->options(ListingAddon::query()
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                            ->toArray())
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $addon = ListingAddon::find($data['addon_id']);
                    if ($addon) {
                        ListingCreditService::grantAddon($record, $addon, 'admin_grant');
                    }
                }),
        ];
    }
}
