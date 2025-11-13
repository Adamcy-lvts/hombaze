<?php

namespace App\Filament\Agent\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use App\Filament\Agent\Resources\PropertyViewingResource\Pages\ListPropertyViewings;
use App\Filament\Agent\Resources\PropertyViewingResource\Pages\CreatePropertyViewing;
use App\Filament\Agent\Resources\PropertyViewingResource\Pages\EditPropertyViewing;
use App\Filament\Agent\Resources\PropertyViewingResource\Pages;
use App\Filament\Agent\Resources\PropertyViewingResource\RelationManagers;
use App\Models\PropertyViewing;
use App\Models\Property;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyViewingResource extends Resource
{
    protected static ?string $model = PropertyViewing::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Property Viewings';
    
    protected static ?string $modelLabel = 'Viewing';
    
    protected static ?string $pluralModelLabel = 'Viewings';
    
    protected static ?int $navigationSort = 3;

    /**
     * Scope queries to only show viewings for the current agent's properties
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
                Section::make('Viewing Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('property_id')
                                    ->label('Property')
                                    ->options(function () {
                                        $user = auth()->user();
                                        return Property::where('agent_id', $user->id)
                                            ->whereNull('agency_id')
                                            ->pluck('title', 'id');
                                    })
                                    ->required()
                                    ->searchable(),
                                
                                Select::make('status')
                                    ->options([
                                        'scheduled' => 'Scheduled',
                                        'confirmed' => 'Confirmed',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                        'no_show' => 'No Show',
                                    ])
                                    ->required()
                                    ->default('scheduled'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('scheduled_date')
                                    ->label('Viewing Date')
                                    ->required()
                                    ->minDate(now()),
                                
                                TimePicker::make('scheduled_time')
                                    ->label('Viewing Time')
                                    ->required(),
                            ]),
                        
                        TextInput::make('inquirer.name')
                            ->label('Inquirer Name')
                            ->disabled(),
                        
                        TextInput::make('inquirer.email')
                            ->label('Inquirer Email')
                            ->disabled(),
                        
                        TextInput::make('inquirer.phone')
                            ->label('Inquirer Phone')
                            ->disabled(),
                        
                        Textarea::make('notes')
                            ->label('Notes')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->placeholder('Any special instructions or notes about the viewing...'),
                        
                        TextInput::make('cancellation_reason')
                            ->label('Cancellation Reason')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('status') === 'cancelled')
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
                    ->sortable(),
                
                TextColumn::make('inquirer.name')
                    ->label('Inquirer')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('inquirer.phone')
                    ->label('Phone')
                    ->searchable(),
                
                TextColumn::make('scheduled_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('scheduled_time')
                    ->label('Time')
                    ->time(),
                
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'scheduled',
                        'primary' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'secondary' => 'no_show',
                    ]),
                
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('created_at')
                    ->label('Scheduled')
                    ->dateTime()
                    ->sortable(),
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
                
                SelectFilter::make('property_id')
                    ->label('Property')
                    ->options(function () {
                        $user = auth()->user();
                        return Property::where('agent_id', $user->id)
                            ->whereNull('agency_id')
                            ->pluck('title', 'id');
                    }),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (PropertyViewing $record): void {
                        $record->update(['status' => 'confirmed']);
                    })
                    ->visible(fn (PropertyViewing $record) => $record->status === 'scheduled'),
                
                Action::make('complete')
                    ->label('Mark Complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function (PropertyViewing $record): void {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    })
                    ->visible(fn (PropertyViewing $record) => in_array($record->status, ['scheduled', 'confirmed'])),
                
                EditAction::make(),
            ])
            ->toolbarActions([
                // No bulk actions for viewings
            ])
            ->defaultSort('scheduled_date', 'asc');
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
            'index' => ListPropertyViewings::route('/'),
            'create' => CreatePropertyViewing::route('/create'),
            'edit' => EditPropertyViewing::route('/{record}/edit'),
        ];
    }
}
