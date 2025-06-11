<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ViewingsRelationManager extends RelationManager
{
    protected static string $relationship = 'viewings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('inquirer_id')
                    ->relationship('inquirer', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Inquirer'),
                Forms\Components\Select::make('agent_id')
                    ->relationship('agent', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->label('Agent'),
                Forms\Components\DatePicker::make('scheduled_date')
                    ->required()
                    ->label('Scheduled Date'),
                Forms\Components\TimePicker::make('scheduled_time')
                    ->required()
                    ->label('Scheduled Time'),
                Forms\Components\Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ])
                    ->default('scheduled')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Completed At')
                    ->visible(fn (Forms\Get $get): bool => $get('status') === 'completed'),
                Forms\Components\DateTimePicker::make('cancelled_at')
                    ->label('Cancelled At')
                    ->visible(fn (Forms\Get $get): bool => $get('status') === 'cancelled'),
                Forms\Components\TextInput::make('cancellation_reason')
                    ->maxLength(255)
                    ->label('Cancellation Reason')
                    ->visible(fn (Forms\Get $get): bool => $get('status') === 'cancelled'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inquirer.name')
            ->columns([
                Tables\Columns\TextColumn::make('inquirer.name')
                    ->label('Inquirer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agent')
                    ->default('Not Assigned')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_time')
                    ->label('Time')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
                Tables\Columns\TextColumn::make('notes')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ]),
                Tables\Filters\Filter::make('upcoming')
                    ->query(fn (Builder $query): Builder => $query->upcoming())
                    ->label('Upcoming Only'),
                Tables\Filters\Filter::make('past')
                    ->query(fn (Builder $query): Builder => $query->past())
                    ->label('Past Viewings'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'confirmed']))
                    ->visible(fn ($record) => $record->status === 'scheduled'),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]))
                    ->visible(fn ($record) => in_array($record->status, ['scheduled', 'confirmed'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_date', 'desc');
    }
}
