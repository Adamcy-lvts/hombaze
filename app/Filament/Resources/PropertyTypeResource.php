<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyTypeResource\Pages;
use App\Filament\Resources\PropertyTypeResource\RelationManagers;
use App\Models\PropertyType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyTypeResource extends Resource
{
    protected static ?string $model = PropertyType::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(['default' => 1, 'lg' => 4])
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Property Type Information')
                                    ->description('Define the property type details and settings')
                                    ->schema([
                                        Forms\Components\Grid::make(['default' => 1, 'lg' => 2])
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->helperText('e.g., Residential, Commercial, Industrial'),
                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->helperText('URL-friendly version (auto-generated)')
                                                    ->disabled()
                                                    ->dehydrated(),
                                            ]),
                                        
                                        Forms\Components\Textarea::make('description')
                                            ->rows(4)
                                            ->helperText('Detailed description of this property type'),
                                        
                                        Forms\Components\TextInput::make('icon')
                                            ->maxLength(255)
                                            ->helperText('Heroicon or custom icon class (e.g., heroicon-o-home)'),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 3]),
                        
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Configuration')
                                    ->schema([
                                        Forms\Components\Fieldset::make('Status')
                                            ->schema([
                                                Forms\Components\Toggle::make('is_active')
                                                    ->required()
                                                    ->default(true)
                                                    ->helperText('Show in property forms'),
                                            ]),
                                        
                                        Forms\Components\Fieldset::make('Display Order')
                                            ->schema([
                                                Forms\Components\TextInput::make('sort_order')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0)
                                                    ->helperText('Lower numbers appear first'),
                                            ]),
                                    ])
                                    ->collapsible(),
                                
                                Forms\Components\Section::make('Statistics')
                                    ->schema([
                                        Forms\Components\Fieldset::make('Usage Count')
                                            ->schema([
                                                Forms\Components\Placeholder::make('properties_count')
                                                    ->label('Properties Using This Type')
                                                    ->content(fn ($record): string => $record?->properties_count ?? '0'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                                
                                Forms\Components\Section::make('System Info')
                                    ->schema([
                                        Forms\Components\Fieldset::make('Record Details')
                                            ->schema([
                                                Forms\Components\Placeholder::make('created_at')
                                                    ->label('Created')
                                                    ->content(fn ($record): string => $record?->created_at?->diffForHumans() ?? 'Not created yet'),
                                                Forms\Components\Placeholder::make('updated_at')
                                                    ->label('Last Modified')
                                                    ->content(fn ($record): string => $record?->updated_at?->diffForHumans() ?? 'Not modified yet'),
                                            ]),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPropertyTypes::route('/'),
            'create' => Pages\CreatePropertyType::route('/create'),
            'view' => Pages\ViewPropertyType::route('/{record}'),
            'edit' => Pages\EditPropertyType::route('/{record}/edit'),
        ];
    }
}
