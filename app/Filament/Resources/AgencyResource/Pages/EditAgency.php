<?php

namespace App\Filament\Resources\AgencyResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\ListingCreditService;
use App\Models\ListingPackage;
use App\Models\ListingAddon;

class EditAgency extends EditRecord
{
    protected static string $resource = AgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Actions\Action::make('grantListingBundle')
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
            Actions\Action::make('grantListingAddon')
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
