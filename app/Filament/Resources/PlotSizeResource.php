<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlotSizeResource\Pages;
use App\Filament\Resources\PlotSizeResource\RelationManagers;
use App\Models\PlotSize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlotSizeResource extends Resource
{
    protected static ?string $model = PlotSize::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Plot Sizes';

    protected static ?string $modelLabel = 'Plot Size';

    protected static ?string $pluralModelLabel = 'Plot Sizes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plot Size Information')
                    ->description('Configure predefined plot/land sizes for property listings and searches')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Quarter Plot, Half Plot, 1 Acre')
                                    ->helperText('Descriptive name for this plot size'),

                                Forms\Components\TextInput::make('description')
                                    ->maxLength(255)
                                    ->placeholder('e.g., 15x30m - Common small residential plot')
                                    ->helperText('Optional description with dimensions or usage'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('size_value')
                                    ->label('Size Value')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->placeholder('e.g., 1, 0.5, 2500')
                                    ->helperText('Numeric value for the size'),

                                Forms\Components\Select::make('unit')
                                    ->required()
                                    ->options(\App\Models\PlotSize::getUnits())
                                    ->live()
                                    ->helperText('Unit of measurement'),

                                Forms\Components\TextInput::make('size_in_sqm')
                                    ->label('Size in Sqm')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Auto-calculated equivalent in square meters'),
                            ]),

                        Forms\Components\TextInput::make('display_text')
                            ->label('Custom Display Text')
                            ->maxLength(255)
                            ->placeholder('e.g., 1 Plot (1,800 sqm)')
                            ->helperText('Optional custom display text. If empty, will be auto-generated from size value and unit'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),

                                Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('size_value')
                    ->label('Size')
                    ->alignCenter()
                    ->formatStateUsing(fn (PlotSize $record) => $record->size_value . ' ' . $record->unit),

                Tables\Columns\TextColumn::make('size_in_sqm')
                    ->label('Sqm Equivalent')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' sqm')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->badge()
                    ->colors([
                        'primary' => 'plot',
                        'success' => 'sqm',
                        'warning' => 'acre',
                        'info' => 'hectare',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('display_text')
                    ->label('Display')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit')
                    ->options(\App\Models\PlotSize::getUnits()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All plot sizes')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPlotSizes::route('/'),
            'create' => Pages\CreatePlotSize::route('/create'),
            'view' => Pages\ViewPlotSize::route('/{record}'),
            'edit' => Pages\EditPlotSize::route('/{record}/edit'),
        ];
    }
}
