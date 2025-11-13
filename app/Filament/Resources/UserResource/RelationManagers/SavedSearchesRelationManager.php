<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

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

class SavedSearchesRelationManager extends RelationManager
{
    protected static string $relationship = 'savedSearches';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., 2-Bedroom Apartments in Lekki'),
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
                TextColumn::make('readable_criteria')
                    ->label('Search Criteria')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                TextColumn::make('alert_frequency')
                    ->label('Alert Frequency')
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
                TextColumn::make('last_alerted_at')
                    ->label('Last Alert')
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
                Action::make('send_alert')
                    ->icon('heroicon-o-bell')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->markAsAlerted())
                    ->visible(fn ($record) => $record->is_active)
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
