<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\SmartSearchPlan;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\SmartSearchPlanResource\Pages;

class SmartSearchPlanResource extends Resource
{
    protected static ?string $model = SmartSearchPlan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string | \UnitEnum | null $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'SmartSearch Plans';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Plan Details')
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
                                                TextInput::make('searches_limit')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('duration_days')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('priority_order')
                                                    ->numeric()
                                                    ->default(0),
                                                TextInput::make('delay_hours')
                                                    ->numeric()
                                                    ->default(0),
                                                TextInput::make('exclusive_window_hours')
                                                    ->numeric()
                                                    ->nullable(),
                                                TextInput::make('sort_order')
                                                    ->numeric()
                                                    ->default(0),
                                            ]),
                                        Textarea::make('description')
                                            ->rows(3),
                                        CheckboxList::make('notification_channels')
                                            ->options([
                                                'email' => 'Email',
                                                'whatsapp' => 'WhatsApp',
                                                'sms' => 'SMS',
                                            ])
                                            ->columns(3),
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
                TextColumn::make('searches_limit')
                    ->label('Searches')
                    ->sortable(),
                TextColumn::make('duration_days')
                    ->label('Days')
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
            'index' => Pages\ListSmartSearchPlans::route('/'),
            'create' => Pages\CreateSmartSearchPlan::route('/create'),
            'view' => Pages\ViewSmartSearchPlan::route('/{record}'),
            'edit' => Pages\EditSmartSearchPlan::route('/{record}/edit'),
        ];
    }
}
