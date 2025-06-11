<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\PropertyInquiryResource\Pages;
use App\Filament\Agency\Resources\PropertyInquiryResource\RelationManagers;
use App\Models\PropertyInquiry;
use App\Models\Property;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class PropertyInquiryResource extends Resource
{
    protected static ?string $model = PropertyInquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    
    protected static ?string $navigationLabel = 'Inquiries';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'inquirer_name';

    // Disable create and edit - inquiries come from users naturally
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        // Form only used for viewing details - agencies can't create inquiries
        return $form
            ->schema([
                Forms\Components\Section::make('Inquiry Information')
                    ->schema([
                        Forms\Components\TextInput::make('inquirer_name')
                            ->label('Inquirer Name')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('inquirer_email')
                            ->label('Email')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('inquirer_phone')
                            ->label('Phone')
                            ->disabled(),

                        Forms\Components\Select::make('property_id')
                            ->label('Property')
                            ->relationship('property', 'title')
                            ->disabled(),
                            
                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->disabled()
                            ->rows(3),
                            
                        Forms\Components\DatePicker::make('preferred_viewing_date')
                            ->label('Preferred Viewing Date')
                            ->disabled(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Response & Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'closed' => 'Closed',
                            ])
                            ->required()
                            ->default('new'),
                            
                        Forms\Components\Textarea::make('response_message')
                            ->label('Response Message')
                            ->rows(4),
                            
                        Forms\Components\DateTimePicker::make('responded_at')
                            ->label('Response Date'),
                            
                        Forms\Components\Select::make('responded_by')
                            ->label('Responded By')
                            ->relationship('responder', 'name')
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
                    ->label('Inquirer')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('inquirer_email')
                    ->label('Email')
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                    
                Tables\Columns\TextColumn::make('inquirer_phone')
                    ->label('Phone')
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->placeholder('Not provided'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'contacted' => 'info',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(50)
                    ->tooltip(function (PropertyInquiry $record): string {
                        return $record->message;
                    }),
                    
                Tables\Columns\TextColumn::make('preferred_viewing_date')
                    ->label('Preferred Date')
                    ->date()
                    ->placeholder('Not specified'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                    
                Tables\Columns\TextColumn::make('responded_at')
                    ->label('Response Date')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Not responded'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'closed' => 'Closed',
                    ]),
                    
                Filter::make('unresponded')
                    ->query(fn (Builder $query): Builder => $query->whereNull('responded_at'))
                    ->label('Unresponded Inquiries'),
                    
                Filter::make('today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                    ->label('Today\'s Inquiries'),
                    
                Filter::make('this_week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->label('This Week\'s Inquiries'),
            ])
            ->headerActions([
                // No create action - inquiries come from users browsing properties
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Details')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\Action::make('respond')
                    ->label('Respond')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (PropertyInquiry $record): bool => !$record->responded_at)
                    ->form([
                        Forms\Components\Textarea::make('response_message')
                            ->label('Response Message')
                            ->required()
                            ->rows(4)
                            ->placeholder('Type your response to the inquiry...'),
                    ])
                    ->action(function (PropertyInquiry $record, array $data): void {
                        $record->markAsContacted(auth()->user(), $data['response_message']);
                    })
                    ->successNotificationTitle('Response sent successfully'),
                
                Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->form([
                        Forms\Components\Textarea::make('response_message')
                            ->label('Closing Message (Optional)')
                            ->rows(3)
                            ->placeholder('Add a closing message if needed...'),
                    ])
                    ->action(function (PropertyInquiry $record, array $data): void {
                        $record->markAsClosed(auth()->user(), $data['response_message'] ?? null);
                    })
                    ->successNotificationTitle('Inquiry closed successfully'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_contacted')
                        ->label('Mark as Contacted')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records): void {
                            $records->each(fn (PropertyInquiry $record) => $record->markAsContacted(auth()->user()));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('close')
                        ->label('Close Inquiries')
                        ->icon('heroicon-o-x-mark')
                        ->color('gray')
                        ->action(function ($records): void {
                            $records->each(fn (PropertyInquiry $record) => $record->markAsClosed(auth()->user()));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds for new inquiries
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
            'index' => Pages\ListPropertyInquiries::route('/'),
            'view' => Pages\ViewPropertyInquiry::route('/{record}'),
        ];
    }
}
