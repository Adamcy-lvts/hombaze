<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\StateResource\Pages\ListStates;
use App\Filament\Resources\StateResource\Pages\CreateState;
use App\Filament\Resources\StateResource\Pages\ViewState;
use App\Filament\Resources\StateResource\Pages\EditState;
use App\Filament\Resources\StateResource\Pages;
use App\Filament\Resources\StateResource\RelationManagers;
use App\Models\State;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';

    protected static string | \UnitEnum | null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('State Information')
                                    ->description('Basic state details and identification')
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('code')
                                            ->required()
                                            ->maxLength(10)
                                            ->helperText('State abbreviation or code'),
                                        Select::make('region')
                                            ->required()
                                            ->options([
                                                'North Central' => 'North Central',
                                                'North East' => 'North East',
                                                'North West' => 'North West',
                                                'South East' => 'South East',
                                                'South South' => 'South South',
                                                'South West' => 'South West',
                                            ])
                                            ->native(false),
                                        Select::make('status')
                                            ->required()
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                            ])
                                            ->default('active')
                                            ->native(false),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                        
                        Group::make()
                            ->schema([
                                Section::make('System Info')
                                    ->schema([
                                        Placeholder::make('created_at')
                                            ->label('Created')
                                            ->content(fn ($record): string => $record?->created_at?->diffForHumans() ?? 'Not created yet'),
                                        Placeholder::make('updated_at')
                                            ->label('Last Modified')
                                            ->content(fn ($record): string => $record?->updated_at?->diffForHumans() ?? 'Not modified yet'),
                                    ])
                                    ->collapsible()
                                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('region'),
                TextColumn::make('status'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListStates::route('/'),
            'create' => CreateState::route('/create'),
            'view' => ViewState::route('/{record}'),
            'edit' => EditState::route('/{record}/edit'),
        ];
    }
}
