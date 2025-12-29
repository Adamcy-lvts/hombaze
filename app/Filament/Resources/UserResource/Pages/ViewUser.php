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
use App\Models\SmartSearchSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                        $owner = ListingCreditService::resolveOwner($record);
                        if ($owner) {
                            ListingCreditService::grantPackage($owner, $package, 'admin_grant');
                        }
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
                        $owner = ListingCreditService::resolveOwner($record);
                        if ($owner) {
                            ListingCreditService::grantAddon($owner, $addon, 'admin_grant');
                        }
                    }
                }),
            Action::make('grantSmartSearchPlan')
                ->label('Grant SmartSearch Plan')
                ->color('success')
                ->icon('heroicon-o-magnifying-glass')
                ->form([
                    \Filament\Forms\Components\Select::make('plan_id')
                        ->label('Plan')
                        ->options(DB::table('smart_search_plans')
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                            ->toArray())
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $plan = DB::table('smart_search_plans')->where('id', $data['plan_id'] ?? null)->first();
                    if (!$plan) {
                        return;
                    }
                    $channels = $plan->notification_channels ?? '[]';
                    if (is_string($channels)) {
                        $decoded = json_decode($channels, true);
                        $channels = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                    }

                    SmartSearchSubscription::create([
                        'user_id' => $record->id,
                        'tier' => $plan->slug,
                        'searches_limit' => (int) $plan->searches_limit,
                        'searches_used' => 0,
                        'duration_days' => (int) $plan->duration_days,
                        'amount_paid' => 0,
                        'payment_reference' => Str::uuid()->toString(),
                        'payment_method' => 'admin_grant',
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                        'starts_at' => now(),
                        'expires_at' => now()->addDays((int) $plan->duration_days),
                        'notification_channels' => $channels,
                        'notes' => 'Admin granted plan',
                    ]);
                }),
        ];
    }
}
