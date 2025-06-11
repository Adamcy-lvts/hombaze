<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(['default' => 1, 'lg' => 4])
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('City Information')
                                    ->description('Basic city details and identification')
                                    ->schema([
                                        Forms\Components\Grid::make(['default' => 1, 'lg' => 2])
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->helperText('URL-friendly version of the name'),
                                                Forms\Components\Select::make('state_id')
                                                    ->relationship('state', 'name')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload(),
                                                Forms\Components\TextInput::make('type')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->default('city')
                                                    ->helperText('City, town, village, etc.'),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Description')
                                    ->description('Additional information about the city')
                                    ->schema([
                                        Forms\Components\Textarea::make('description')
                                            ->rows(4)
                                            ->helperText('Brief description of the city'),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Location Details')
                                    ->description('Geographic coordinates and postal information')
                                    ->schema([
                                        Forms\Components\Grid::make(['default' => 1, 'lg' => 3])
                                            ->schema([
                                                Forms\Components\TextInput::make('postal_code')
                                                    ->maxLength(255)
                                                    ->label('Primary Postal Code'),
                                                Forms\Components\TextInput::make('latitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->helperText('Decimal degrees'),
                                                Forms\Components\TextInput::make('longitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->helperText('Decimal degrees'),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 3]),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Status & Configuration')
                                    ->schema([
                                        Forms\Components\Fieldset::make('Visibility')
                                            ->schema([
                                                Forms\Components\Toggle::make('is_active')
                                                    ->required()
                                                    ->default(true)
                                                    ->helperText('Show in public listings'),
                                            ]),

                                        Forms\Components\Fieldset::make('Ordering')
                                            ->schema([
                                                Forms\Components\TextInput::make('sort_order')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0)
                                                    ->helperText('Lower numbers appear first'),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('System Info')
                                    ->schema([
                                        Forms\Components\Fieldset::make('Record Details')
                                            ->schema([
                                                Forms\Components\Placeholder::make('created_at')
                                                    ->label('Created')
                                                    ->content(fn($record): string => $record?->created_at?->diffForHumans() ?? 'Not created yet'),
                                                Forms\Components\Placeholder::make('updated_at')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
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

                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Postal Code')
                    ->searchable()
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('is_active')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('areas_count')
                    ->label('Areas')
                    ->counts('areas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('properties_count')
                    ->label('Properties')
                    ->counts('properties')
                    ->sortable(),

                Tables\Columns\TextColumn::make('agencies_count')
                    ->label('Agencies')
                    ->counts('agencies')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'city' => 'City',
                        'town' => 'Town',
                        'village' => 'Village',
                        'municipality' => 'Municipality',
                        'district' => 'District',
                    ])
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),

                Tables\Filters\Filter::make('has_coordinates')
                    ->label('Has Coordinates')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('latitude')->whereNotNull('longitude'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_postal_code')
                    ->label('Has Postal Code')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('postal_code'))
                    ->toggle(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
