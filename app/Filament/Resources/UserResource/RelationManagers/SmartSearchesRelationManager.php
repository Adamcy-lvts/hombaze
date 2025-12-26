<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\SmartSearch;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmartSearchesRelationManager extends RelationManager
{
    protected static string $relationship = 'smartSearches';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., 2-Bedroom Apartments in Lekki'),
                Select::make('tier')
                    ->options([
                        SmartSearch::TIER_STARTER => 'Starter (₦10K)',
                        SmartSearch::TIER_STANDARD => 'Standard (₦20K)',
                        SmartSearch::TIER_PRIORITY => 'Priority (₦35K)',
                        SmartSearch::TIER_VIP => 'VIP (₦50K)',
                    ])
                    ->default(SmartSearch::TIER_STARTER)
                    ->required(),
                KeyValue::make('search_criteria')
                    ->label('Search Criteria')
                    ->keyLabel('Criteria')
                    ->valueLabel('Value')
                    ->default([])
                    ->columnSpanFull(),
                Select::make('alert_frequency')
                    ->options([
                        'instant' => 'Instant',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ])
                    ->default('daily')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Toggle::make('is_expired')
                    ->label('Expired')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tier')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        SmartSearch::TIER_VIP => 'purple',
                        SmartSearch::TIER_PRIORITY => 'blue',
                        SmartSearch::TIER_STANDARD => 'success',
                        SmartSearch::TIER_STARTER => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($record) => $record->getTierName())
                    ->sortable(),
                TextColumn::make('readable_criteria')
                    ->label('Search Criteria')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                TextColumn::make('matches_sent')
                    ->label('Matches Sent')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Never'),
                TextColumn::make('alert_frequency')
                    ->label('Frequency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'instant' => 'danger',
                        'daily' => 'warning',
                        'weekly' => 'info',
                        'monthly' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_expired')
                    ->label('Expired')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
                TextColumn::make('last_match_at')
                    ->label('Last Match')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Never'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tier')
                    ->options([
                        SmartSearch::TIER_VIP => 'VIP',
                        SmartSearch::TIER_PRIORITY => 'Priority',
                        SmartSearch::TIER_STANDARD => 'Standard',
                        SmartSearch::TIER_STARTER => 'Starter',
                    ]),
                SelectFilter::make('alert_frequency')
                    ->options([
                        'instant' => 'Instant',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All searches')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                TernaryFilter::make('is_expired')
                    ->label('Expiry Status')
                    ->placeholder('All')
                    ->trueLabel('Expired only')
                    ->falseLabel('Active only'),
                Filter::make('needs_alert')
                    ->query(fn (Builder $query): Builder => $query->needsAlert())
                    ->label('Needs Alert'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('toggle_active')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
                Action::make('extend')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Extend SmartSearch Duration')
                    ->modalDescription('This will extend the search by 30 days.')
                    ->action(fn ($record) => $record->extendDuration(30))
                    ->visible(fn ($record) => $record->expires_at !== null)
                    ->label('Extend 30 Days'),
                Action::make('send_alert')
                    ->icon('heroicon-o-bell')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->markAsAlerted())
                    ->visible(fn ($record) => $record->is_active && !$record->is_expired)
                    ->label('Send Alert Now'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
