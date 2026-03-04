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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Models\Agent;
use App\Models\Agency;
use App\Models\PropertyOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AgentVerificationResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Agent Verification';

    protected static ?string $modelLabel = 'Agent';

    protected static ?string $pluralModelLabel = 'Agents';

    protected static string | \UnitEnum | null $navigationGroup = 'Verification';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'agent-verifications';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', false)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canAccess(): bool
    {
        // Allow access to super admins or users with admin role
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('admin') || $user->can('view_any_agent'));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Agent::query()->with(['user', 'agency']))
            ->columns([
                ImageColumn::make('profile_photo_url')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name ?? 'A') . '&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('agency.name')
                    ->label('Agency')
                    ->placeholder('Independent')
                    ->searchable(),

                TextColumn::make('license_number')
                    ->label('License #')
                    ->searchable()
                    ->placeholder('Not provided'),

                TextColumn::make('years_experience')
                    ->label('Experience')
                    ->suffix(' years')
                    ->placeholder('N/A')
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

                SelectFilter::make('agency_id')
                    ->relationship('agency', 'name')
                    ->label('Agency')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('verify')
                        ->label('Verify Agent')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Verify Agent')
                        ->modalDescription(fn (Agent $record): string => 
                            "Are you sure you want to verify {$record->full_name}? This will mark them as a trusted agent.")
                        ->hidden(fn (Agent $record): bool => $record->is_verified)
                        ->action(function (Agent $record): void {
                            $record->update([
                                'is_verified' => true,
                                'verified_at' => now(),
                            ]);
                            
                            Notification::make()
                                ->success()
                                ->title('Agent Verified')
                                ->body("'{$record->full_name}' has been verified successfully.")
                                ->send();
                        }),

                    Action::make('unverify')
                        ->label('Revoke Verification')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Revoke Verification')
                        ->modalDescription(fn (Agent $record): string => 
                            "Are you sure you want to revoke verification for {$record->full_name}?")
                        ->hidden(fn (Agent $record): bool => !$record->is_verified)
                        ->action(function (Agent $record): void {
                            $record->update([
                                'is_verified' => false,
                                'verified_at' => null,
                            ]);
                            
                            Notification::make()
                                ->warning()
                                ->title('Verification Revoked')
                                ->body("Verification has been revoked for '{$record->full_name}'.")
                                ->send();
                        }),

                    Action::make('view_documents')
                        ->label('View Documents')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->modalHeading(fn (Agent $record): string => "Documents for {$record->full_name}")
                        ->modalContent(fn (Agent $record) => view('filament.modals.agent-documents', ['agent' => $record]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    Action::make('view_profile')
                        ->label('View Agent Profile')
                        ->icon('heroicon-o-user')
                        ->color('info')
                        ->url(fn (Agent $record): string => \App\Filament\Resources\AgentResource::getUrl('edit', ['record' => $record])),
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
                            ->title('Agents Verified')
                            ->body("{$count} agents have been verified.")
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AgentVerificationResource\Pages\ListAgentVerifications::route('/'),
        ];
    }
}
