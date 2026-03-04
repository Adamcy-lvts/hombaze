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
use Filament\Notifications\Notification;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Collection;

class AgencyVerificationResource extends Resource
{
    protected static ?string $model = Agency::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Agency Verification';

    protected static ?string $modelLabel = 'Agency';

    protected static ?string $pluralModelLabel = 'Agencies';

    protected static string | \UnitEnum | null $navigationGroup = 'Verification';

    protected static ?int $navigationSort = 2;

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
            ->query(Agency::query()->with(['owner', 'state', 'city']))
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'A') . '&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('name')
                    ->label('Agency Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->placeholder('No owner'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('rc_number')
                    ->label('RC Number')
                    ->searchable()
                    ->placeholder('Not provided'),

                TextColumn::make('state.name')
                    ->label('Location')
                    ->formatStateUsing(fn ($record): string => 
                        ($record->city?->name ?? '') . ', ' . ($record->state?->name ?? '')),

                TextColumn::make('agents_count')
                    ->label('Agents')
                    ->counts('agents')
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
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('verify')
                        ->label('Verify Agency')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Verify Agency')
                        ->modalDescription(fn (Agency $record): string => 
                            "Are you sure you want to verify {$record->name}? This will mark them as a trusted agency and their properties will be auto-approved.")
                        ->hidden(fn (Agency $record): bool => $record->is_verified)
                        ->action(function (Agency $record): void {
                            $record->update([
                                'is_verified' => true,
                                'verified_at' => now(),
                            ]);
                            
                            Notification::make()
                                ->success()
                                ->title('Agency Verified')
                                ->body("'{$record->name}' has been verified successfully.")
                                ->send();
                        }),

                    Action::make('unverify')
                        ->label('Revoke Verification')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Revoke Verification')
                        ->modalDescription(fn (Agency $record): string => 
                            "Are you sure you want to revoke verification for {$record->name}?")
                        ->hidden(fn (Agency $record): bool => !$record->is_verified)
                        ->action(function (Agency $record): void {
                            $record->update([
                                'is_verified' => false,
                                'verified_at' => null,
                            ]);
                            
                            Notification::make()
                                ->warning()
                                ->title('Verification Revoked')
                                ->body("Verification has been revoked for '{$record->name}'.")
                                ->send();
                        }),

                    Action::make('view_documents')
                        ->label('View Documents')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->modalHeading(fn (Agency $record): string => "Documents for {$record->name}")
                        ->modalContent(fn (Agency $record) => view('filament.modals.agency-documents', ['agency' => $record]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    Action::make('view_profile')
                        ->label('View Agency Profile')
                        ->icon('heroicon-o-building-office')
                        ->color('info')
                        ->url(fn (Agency $record): string => \App\Filament\Resources\AgencyResource::getUrl('edit', ['record' => $record])),
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
                            ->title('Agencies Verified')
                            ->body("{$count} agencies have been verified.")
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AgencyVerificationResource\Pages\ListAgencyVerifications::route('/'),
        ];
    }
}
