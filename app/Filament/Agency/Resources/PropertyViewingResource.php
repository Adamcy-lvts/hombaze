<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\PropertyViewingResource\Pages;
use App\Models\PropertyViewing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class PropertyViewingResource extends Resource
{
    protected static ?string $model = PropertyViewing::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Property Viewings';
    
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'property.title';

    // Disable create - viewings are requested by users, not created by agencies
    public static function canCreate(): bool
    {
        return false;
    }

    // Allow limited editing for appointment management only
    public static function canEdit($record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Viewing Information')
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->label('Property')
                            ->relationship('property', 'title')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('inquirer_name')
                            ->label('Client Name')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('inquirer_email')
                            ->label('Client Email')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('inquirer_phone')
                            ->label('Client Phone')
                            ->disabled(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Appointment Details')
                    ->schema([
                        Forms\Components\Select::make('agent_id')
                            ->label('Assigned Agent')
                            ->relationship('agent', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        Forms\Components\DateTimePicker::make('scheduled_date')
                            ->label('Scheduled Date & Time')
                            ->required()
                            ->minDate(now()),
                            
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Duration (minutes)')
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(240)
                            ->default(60)
                            ->suffix('minutes'),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'confirmed' => 'Confirmed',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'no_show' => 'No Show',
                                'rescheduled' => 'Rescheduled',
                            ])
                            ->required()
                            ->default('scheduled'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('special_instructions')
                            ->label('Special Instructions')
                            ->rows(3)
                            ->disabled(),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Agency Notes')
                            ->rows(3),
                            
                        Forms\Components\Textarea::make('feedback')
                            ->label('Client Feedback')
                            ->rows(3),
                            
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completion Date')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->weight('medium'),
                    
                Tables\Columns\TextColumn::make('inquirer_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('inquirer_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                    
                Tables\Columns\TextColumn::make('inquirer_phone')
                    ->label('Phone')
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->placeholder('Not provided'),
                    
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agent')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Scheduled')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->color(fn ($record) => $record->scheduled_date < now() ? 'danger' : 'success'),
                    
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'confirmed' => 'info',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'no_show' => 'gray',
                        'rescheduled' => 'yellow',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
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
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                        'rescheduled' => 'Rescheduled',
                    ]),
                    
                SelectFilter::make('agent_id')
                    ->relationship('agent', 'name')
                    ->label('Agent')
                    ->preload(),
                    
                Filter::make('today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('scheduled_date', today()))
                    ->label('Today\'s Viewings'),
                    
                Filter::make('this_week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->label('This Week\'s Viewings'),
                    
                Filter::make('upcoming')
                    ->query(fn (Builder $query): Builder => $query->where('scheduled_date', '>=', now())
                        ->whereIn('status', ['scheduled', 'confirmed']))
                    ->label('Upcoming Viewings'),
                    
                Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('scheduled_date', '<', now())
                        ->whereIn('status', ['scheduled', 'confirmed']))
                    ->label('Overdue Viewings'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),
                
                Tables\Actions\Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PropertyViewing $record): bool => $record->status === 'scheduled')
                    ->action(fn (PropertyViewing $record) => $record->update(['status' => 'confirmed']))
                    ->successNotificationTitle('Viewing confirmed'),
                
                Tables\Actions\Action::make('start')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->visible(fn (PropertyViewing $record): bool => in_array($record->status, ['scheduled', 'confirmed']))
                    ->action(fn (PropertyViewing $record) => $record->update(['status' => 'in_progress']))
                    ->successNotificationTitle('Viewing started'),
                
                Tables\Actions\Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PropertyViewing $record): bool => in_array($record->status, ['confirmed', 'in_progress']))
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Viewing Notes')
                            ->rows(3),
                        Forms\Components\Textarea::make('feedback')
                            ->label('Client Feedback')
                            ->rows(3),
                    ])
                    ->action(function (PropertyViewing $record, array $data): void {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'notes' => $data['notes'] ?? $record->notes,
                            'feedback' => $data['feedback'] ?? $record->feedback,
                        ]);
                    })
                    ->successNotificationTitle('Viewing marked as completed'),
                
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (PropertyViewing $record): bool => !in_array($record->status, ['completed', 'cancelled', 'no_show']))
                    ->action(fn (PropertyViewing $record) => $record->update(['status' => 'cancelled']))
                    ->requiresConfirmation(),
                
                Tables\Actions\Action::make('reschedule')
                    ->label('Reschedule')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (PropertyViewing $record): bool => !in_array($record->status, ['completed', 'cancelled']))
                    ->form([
                        Forms\Components\DateTimePicker::make('scheduled_date')
                            ->label('New Date & Time')
                            ->required()
                            ->minDate(now()),
                    ])
                    ->action(function (PropertyViewing $record, array $data): void {
                        $record->update([
                            'scheduled_date' => $data['scheduled_date'],
                            'status' => 'rescheduled',
                        ]);
                    })
                    ->successNotificationTitle('Viewing rescheduled'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_viewings')
                        ->label('Confirm Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records): void {
                            $records->each(function (PropertyViewing $record) {
                                if ($record->status === 'scheduled') {
                                    $record->update(['status' => 'confirmed']);
                                }
                            });
                        })
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('cancel_viewings')
                        ->label('Cancel Selected')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function (PropertyViewing $record) {
                                if (!in_array($record->status, ['completed', 'cancelled'])) {
                                    $record->update(['status' => 'cancelled']);
                                }
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('scheduled_date', 'asc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyViewings::route('/'),
            'view' => Pages\ViewPropertyViewing::route('/{record}'),
            'edit' => Pages\EditPropertyViewing::route('/{record}/edit'),
        ];
    }
}
