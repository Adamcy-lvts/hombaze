<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use App\Models\PropertyOwner;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PropertyOwnerVerificationResource extends Resource
{
    protected static ?string $model = PropertyOwner::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Property Owner Verification';

    protected static ?string $modelLabel = 'Property Owner';

    protected static ?string $pluralModelLabel = 'Property Owners';

    protected static string | \UnitEnum | null $navigationGroup = 'Verification';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', false)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(PropertyOwner::query()->with(['user', 'state', 'city']))
            ->columns([
                ImageColumn::make('profile_photo_url')
                    ->label('Photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->profile_photo)
                    ->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->display_name ?? 'O') . '&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('display_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record): string => $record->type === 'individual' 
                        ? ($record->first_name . ' ' . $record->last_name) 
                        : ($record->company_name ?? 'N/A'))
                    ->searchable(['first_name', 'last_name', 'company_name'])
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'individual' => 'info',
                        'company' => 'primary',
                        'trust' => 'warning',
                        'government' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('nin_number')
                    ->label('NIN')
                    ->searchable()
                    ->placeholder('Not provided'),

                TextColumn::make('state.name')
                    ->label('Location')
                    ->formatStateUsing(fn ($record): string => 
                        ($record->city ?? '') . ', ' . ($record->state ?? '')),

                TextColumn::make('properties_count')
                    ->label('Properties')
                    ->counts('properties')
                    ->sortable(),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Not verified')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->placeholder('All')
                    ->trueLabel('Verified')
                    ->falseLabel('Pending'),

                SelectFilter::make('type')
                    ->options([
                        'individual' => 'Individual',
                        'company' => 'Company',
                        'trust' => 'Trust',
                        'government' => 'Government',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('verify')
                        ->label('Verify Owner')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Verify Property Owner')
                        ->modalDescription(fn (PropertyOwner $record): string => 
                            "Are you sure you want to verify this property owner?")
                        ->hidden(fn (PropertyOwner $record): bool => $record->is_verified)
                        ->action(function (PropertyOwner $record): void {
                            $record->update([
                                'is_verified' => true,
                                'verified_at' => now(),
                            ]);
                            
                            $name = $record->type === 'individual' 
                                ? ($record->first_name . ' ' . $record->last_name) 
                                : $record->company_name;
                            
                            Notification::make()
                                ->success()
                                ->title('Property Owner Verified')
                                ->body("'{$name}' has been verified successfully.")
                                ->send();
                        }),

                    Action::make('unverify')
                        ->label('Revoke Verification')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Revoke Verification')
                        ->modalDescription('Are you sure you want to revoke verification for this property owner?')
                        ->hidden(fn (PropertyOwner $record): bool => !$record->is_verified)
                        ->action(function (PropertyOwner $record): void {
                            $record->update([
                                'is_verified' => false,
                                'verified_at' => null,
                            ]);
                            
                            Notification::make()
                                ->warning()
                                ->title('Verification Revoked')
                                ->body("Verification has been revoked for this property owner.")
                                ->send();
                        }),

                    Action::make('view_documents')
                        ->label('View Documents')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->modalHeading('Property Owner Documents')
                        ->modalContent(fn (PropertyOwner $record) => view('filament.modals.property-owner-documents', ['owner' => $record]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    Action::make('view_user')
                        ->label('View User Profile')
                        ->icon('heroicon-o-user')
                        ->color('info')
                        ->url(fn (PropertyOwner $record): ?string => $record->user_id 
                            ? \App\Filament\Resources\UserResource::getUrl('view', ['record' => $record->user_id]) 
                            : null)
                        ->hidden(fn (PropertyOwner $record): bool => !$record->user_id),
                ]),
            ])
            ->bulkActions([
                BulkAction::make('bulk_verify')
                    ->label('Verify Selected')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $count = 0;
                        foreach ($records as $record) {
                            if (!$record->is_verified) {
                                $record->update([
                                    'is_verified' => true,
                                    'verified_at' => now(),
                                ]);
                                $count++;
                            }
                        }
                        
                        Notification::make()
                            ->success()
                            ->title('Property Owners Verified')
                            ->body("{$count} property owners have been verified.")
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PropertyOwnerVerificationResource\Pages\ListPropertyOwnerVerifications::route('/'),
        ];
    }
}
