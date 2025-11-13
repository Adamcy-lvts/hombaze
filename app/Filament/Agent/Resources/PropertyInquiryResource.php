<?php

namespace App\Filament\Agent\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Agent\Resources\PropertyInquiryResource\Pages\ListPropertyInquiries;
use App\Filament\Agent\Resources\PropertyInquiryResource\Pages\EditPropertyInquiry;
use App\Filament\Agent\Resources\PropertyInquiryResource\Pages;
use App\Filament\Agent\Resources\PropertyInquiryResource\RelationManagers;
use App\Models\PropertyInquiry;
use App\Models\Property;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyInquiryResource extends Resource
{
    protected static ?string $model = PropertyInquiry::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Property Inquiries';
    
    protected static ?string $modelLabel = 'Inquiry';
    
    protected static ?string $pluralModelLabel = 'Inquiries';
    
    protected static ?int $navigationSort = 2;

    /**
     * Scope queries to only show inquiries for the current agent's properties
     */
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()
            ->whereHas('property', function (Builder $query) use ($user) {
                $query->where('agent_id', $user->id)
                      ->whereNull('agency_id'); // Independent agent properties only
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inquiry Details')
                    ->description('Information about the property inquiry')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('property_id')
                                    ->label('Property')
                                    ->relationship('property', 'title')
                                    ->disabled()
                                    ->required(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'responded' => 'Responded',
                                        'viewing_scheduled' => 'Viewing Scheduled',
                                        'closed' => 'Closed',
                                    ])
                                    ->required()
                                    ->default('pending'),
                            ]),
                    ]),
                
                Section::make('Inquirer Information')
                    ->description('Details about the person making the inquiry')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('inquirer_name')
                                    ->label('Name')
                                    ->disabled()
                                    ->required(),
                                TextInput::make('inquirer_email')
                                    ->label('Email')
                                    ->email()
                                    ->disabled()
                                    ->required(),
                                TextInput::make('inquirer_phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->disabled(),
                            ]),
                        
                        Textarea::make('message')
                            ->label('Inquiry Message')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        DateTimePicker::make('preferred_viewing_date')
                            ->label('Preferred Viewing Date')
                            ->disabled()
                            ->date(),
                    ]),
                
                Section::make('Response')
                    ->description('Your response to this inquiry')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('responded_at')
                                    ->label('Response Date')
                                    ->disabled(),
                                TextInput::make('responded_by')
                                    ->label('Responded By')
                                    ->disabled()
                                    ->default(fn () => auth()->user()->name),
                            ]),
                        
                        Textarea::make('response_message')
                            ->label('Response Message')
                            ->placeholder('Write your response to the inquirer...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('inquirer_name')
                    ->label('Inquirer')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('inquirer_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->limit(25),
                
                TextColumn::make('inquirer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'responded',
                        'info' => 'viewing_scheduled',
                        'gray' => 'closed',
                    ])
                    ->sortable(),
                
                TextColumn::make('preferred_viewing_date')
                    ->label('Preferred Date')
                    ->date('M j, Y')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Inquiry Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->since(),
                
                TextColumn::make('responded_at')
                    ->label('Responded')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->since()
                    ->placeholder('Not responded'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'responded' => 'Responded',
                        'viewing_scheduled' => 'Viewing Scheduled',
                        'closed' => 'Closed',
                    ]),
                
                SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'title')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('quick_response')
                    ->label('Quick Response')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->schema([
                        Textarea::make('response_message')
                            ->label('Response Message')
                            ->required()
                            ->placeholder('Write your response to the inquirer...')
                            ->rows(4),
                    ])
                    ->action(function (PropertyInquiry $record, array $data) {
                        $record->update([
                            'response_message' => $data['response_message'],
                            'responded_at' => now(),
                            'responded_by' => auth()->id(),
                            'status' => 'responded',
                        ]);
                        
                        // Here you could add email notification logic
                        
                        return redirect()->back();
                    })
                    ->visible(fn (PropertyInquiry $record) => $record->status === 'pending'),
                
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListPropertyInquiries::route('/'),
            'edit' => EditPropertyInquiry::route('/{record}/edit'),
        ];
    }
}
