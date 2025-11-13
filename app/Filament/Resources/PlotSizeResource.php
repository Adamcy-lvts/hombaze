<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PlotSizeResource\Pages\ListPlotSizes;
use App\Filament\Resources\PlotSizeResource\Pages\CreatePlotSize;
use App\Filament\Resources\PlotSizeResource\Pages\ViewPlotSize;
use App\Filament\Resources\PlotSizeResource\Pages\EditPlotSize;
use App\Filament\Resources\PlotSizeResource\Pages;
use App\Filament\Resources\PlotSizeResource\RelationManagers;
use App\Models\PlotSize;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlotSizeResource extends Resource
{
    protected static ?string $model = PlotSize::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static string | \UnitEnum | null $navigationGroup = 'Property Management';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Plot Sizes';

    protected static ?string $modelLabel = 'Plot Size';

    protected static ?string $pluralModelLabel = 'Plot Sizes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plot Size Information')
                    ->description('Configure predefined plot/land sizes for property listings and searches')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Quarter Plot, Half Plot, 1 Acre')
                                    ->helperText('Descriptive name for this plot size'),

                                TextInput::make('description')
                                    ->maxLength(255)
                                    ->placeholder('e.g., 15x30m - Common small residential plot')
                                    ->helperText('Optional description with dimensions or usage'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('size_value')
                                    ->label('Size Value')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->placeholder('e.g., 1, 0.5, 2500')
                                    ->helperText('Numeric value for the size'),

                                Select::make('unit')
                                    ->required()
                                    ->options(PlotSize::getUnits())
                                    ->live()
                                    ->helperText('Unit of measurement'),

                                TextInput::make('size_in_sqm')
                                    ->label('Size in Sqm')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Auto-calculated equivalent in square meters'),
                            ]),

                        TextInput::make('display_text')
                            ->label('Custom Display Text')
                            ->maxLength(255)
                            ->placeholder('e.g., 1 Plot (1,800 sqm)')
                            ->helperText('Optional custom display text. If empty, will be auto-generated from size value and unit'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Only active plot sizes will be shown in forms'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('size_value')
                    ->label('Size')
                    ->alignCenter()
                    ->formatStateUsing(fn (PlotSize $record) => $record->size_value . ' ' . $record->unit),

                TextColumn::make('size_in_sqm')
                    ->label('Sqm Equivalent')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' sqm')
                    ->sortable(),

                TextColumn::make('unit')
                    ->badge()
                    ->colors([
                        'primary' => 'plot',
                        'success' => 'sqm',
                        'warning' => 'acre',
                        'info' => 'hectare',
                    ])
                    ->sortable(),

                TextColumn::make('display_text')
                    ->label('Display')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('unit')
                    ->options(PlotSize::getUnits()),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All plot sizes')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
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
            'index' => ListPlotSizes::route('/'),
            'create' => CreatePlotSize::route('/create'),
            'view' => ViewPlotSize::route('/{record}'),
            'edit' => EditPlotSize::route('/{record}/edit'),
        ];
    }
}
