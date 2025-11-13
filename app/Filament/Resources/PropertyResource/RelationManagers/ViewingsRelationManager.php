<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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

class ViewingsRelationManager extends RelationManager
{
    protected static string $relationship = 'viewings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inquirer_id')
                    ->relationship('inquirer', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Inquirer'),
                Select::make('agent_id')
                    ->relationship('agent', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->label('Agent'),
                DatePicker::make('scheduled_date')
                    ->required()
                    ->label('Scheduled Date'),
                TimePicker::make('scheduled_time')
                    ->required()
                    ->label('Scheduled Time'),
                Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ])
                    ->default('scheduled')
                    ->required(),
                Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at')
                    ->label('Completed At')
                    ->visible(fn (Get $get): bool => $get('status') === 'completed'),
                DateTimePicker::make('cancelled_at')
                    ->label('Cancelled At')
                    ->visible(fn (Get $get): bool => $get('status') === 'cancelled'),
                TextInput::make('cancellation_reason')
                    ->maxLength(255)
                    ->label('Cancellation Reason')
                    ->visible(fn (Get $get): bool => $get('status') === 'cancelled'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inquirer.name')
            ->columns([
                TextColumn::make('inquirer.name')
                    ->label('Inquirer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->default('Not Assigned')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('scheduled_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('scheduled_time')
                    ->label('Time')
                    ->time()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'no_show' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ]),
                Filter::make('upcoming')
                    ->query(fn (Builder $query): Builder => $query->upcoming())
                    ->label('Upcoming Only'),
                Filter::make('past')
                    ->query(fn (Builder $query): Builder => $query->past())
                    ->label('Past Viewings'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'confirmed']))
                    ->visible(fn ($record) => $record->status === 'scheduled'),
                Action::make('complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]))
                    ->visible(fn ($record) => in_array($record->status, ['scheduled', 'confirmed'])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_date', 'desc');
    }
}
