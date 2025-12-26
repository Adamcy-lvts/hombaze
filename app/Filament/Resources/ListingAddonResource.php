<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\ListingAddon;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\ListingAddonResource\Pages;

class ListingAddonResource extends Resource
{
    protected static ?string $model = ListingAddon::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Listing Add-ons';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Add-on Details')
                                    ->schema([
                                        Grid::make(['default' => 1, 'sm' => 2])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('price')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->required(),
                                                TextInput::make('listing_credits')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('featured_credits')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('featured_expires_days')
                                                    ->numeric()
                                                    ->nullable(),
                                                TextInput::make('sort_order')
                                                    ->numeric()
                                                    ->default(0),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make('Status')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->default(true),
                                    ])
                                    ->collapsible(),
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
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('listing_credits')
                    ->label('Listings')
                    ->sortable(),
                TextColumn::make('featured_credits')
                    ->label('Featured')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('sort_order')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListingAddons::route('/'),
            'create' => Pages\CreateListingAddon::route('/create'),
            'view' => Pages\ViewListingAddon::route('/{record}'),
            'edit' => Pages\EditListingAddon::route('/{record}/edit'),
        ];
    }
}
