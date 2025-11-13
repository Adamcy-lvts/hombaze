<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CityResource\Pages\ListCities;
use App\Filament\Resources\CityResource\Pages\CreateCity;
use App\Filament\Resources\CityResource\Pages\EditCity;
use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string | \UnitEnum | null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 4])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('City Information')
                                    ->description('Basic city details and identification')
                                    ->schema([
                                        Grid::make(['default' => 1, 'lg' => 2])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->helperText('URL-friendly version of the name'),
                                                Select::make('state_id')
                                                    ->relationship('state', 'name')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload(),
                                                TextInput::make('type')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->default('city')
                                                    ->helperText('City, town, village, etc.'),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Section::make('Description')
                                    ->description('Additional information about the city')
                                    ->schema([
                                        Textarea::make('description')
                                            ->rows(4)
                                            ->helperText('Brief description of the city'),
                                    ])
                                    ->collapsible(),

                                Section::make('Location Details')
                                    ->description('Geographic coordinates and postal information')
                                    ->schema([
                                        Grid::make(['default' => 1, 'lg' => 3])
                                            ->schema([
                                                TextInput::make('postal_code')
                                                    ->maxLength(255)
                                                    ->label('Primary Postal Code'),
                                                TextInput::make('latitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->helperText('Decimal degrees'),
                                                TextInput::make('longitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->helperText('Decimal degrees'),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 3]),

                        Group::make()
                            ->schema([
                                Section::make('Status & Configuration')
                                    ->schema([
                                        Fieldset::make('Visibility')
                                            ->schema([
                                                Toggle::make('is_active')
                                                    ->required()
                                                    ->default(true)
                                                    ->helperText('Show in public listings'),
                                            ]),

                                        Fieldset::make('Ordering')
                                            ->schema([
                                                TextInput::make('sort_order')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0)
                                                    ->helperText('Lower numbers appear first'),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Section::make('System Info')
                                    ->schema([
                                        Fieldset::make('Record Details')
                                            ->schema([
                                                Placeholder::make('created_at')
                                                    ->label('Created')
                                                    ->content(fn($record): string => $record?->created_at?->diffForHumans() ?? 'Not created yet'),
                                                Placeholder::make('updated_at')
                                                    ->label('Last Modified')
                                                    ->content(fn($record): string => $record?->updated_at?->diffForHumans() ?? 'Not modified yet'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->hidden(fn(string $operation): bool => $operation === 'create'),
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
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('type')
                    ->badge()
                    ->label('Type')
                    ->colors([
                        'primary' => 'city',
                        'success' => 'town',
                        'warning' => 'village',
                        'info' => 'municipality',
                        'gray' => 'district',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('postal_code')
                    ->label('Postal Code')
                    ->searchable()
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('is_active')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),

                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('areas_count')
                    ->label('Areas')
                    ->counts('areas')
                    ->sortable(),

                TextColumn::make('properties_count')
                    ->label('Properties')
                    ->counts('properties')
                    ->sortable(),

                TextColumn::make('agencies_count')
                    ->label('Agencies')
                    ->counts('agencies')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->options([
                        'city' => 'City',
                        'town' => 'Town',
                        'village' => 'Village',
                        'municipality' => 'Municipality',
                        'district' => 'District',
                    ])
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),

                Filter::make('has_coordinates')
                    ->label('Has Coordinates')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('latitude')->whereNotNull('longitude'))
                    ->toggle(),

                Filter::make('has_postal_code')
                    ->label('Has Postal Code')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('postal_code'))
                    ->toggle(),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Created from'),
                        DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
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
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }
}
