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
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PropertyFeatureResource\Pages\ListPropertyFeatures;
use App\Filament\Resources\PropertyFeatureResource\Pages\CreatePropertyFeature;
use App\Filament\Resources\PropertyFeatureResource\Pages\ViewPropertyFeature;
use App\Filament\Resources\PropertyFeatureResource\Pages\EditPropertyFeature;
use App\Filament\Resources\PropertyFeatureResource\Pages;
use App\Filament\Resources\PropertyFeatureResource\RelationManagers;
use App\Models\PropertyFeature;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyFeatureResource extends Resource
{
    protected static ?string $model = PropertyFeature::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-sparkles';

    protected static string | \UnitEnum | null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 4])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Feature Information')
                                    ->description('Define the property feature details and categorization')
                                    ->schema([
                                        Grid::make(['default' => 1, 'lg' => 2])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->helperText('e.g., Swimming Pool, Garage, Balcony'),
                                                TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->helperText('URL-friendly version'),
                                                Select::make('category')
                                                    ->required()
                                                    ->options([
                                                        'interior' => 'Interior Features',
                                                        'exterior' => 'Exterior Features',
                                                        'amenities' => 'Amenities',
                                                        'security' => 'Security Features',
                                                        'utilities' => 'Utilities',
                                                        'accessibility' => 'Accessibility',
                                                        'other' => 'Other',
                                                    ])
                                                    ->helperText('Group this feature belongs to'),
                                                TextInput::make('icon')
                                                    ->maxLength(255)
                                                    ->helperText('Heroicon class (e.g., heroicon-o-home)'),
                                            ]),
                                        
                                        Textarea::make('description')
                                            ->rows(4)
                                            ->helperText('Detailed description of this feature'),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 3]),
                        
                        Group::make()
                            ->schema([
                                Section::make('Configuration')
                                    ->schema([
                                        Fieldset::make('Status')
                                            ->schema([
                                                Toggle::make('is_active')
                                                    ->required()
                                                    ->default(true)
                                                    ->helperText('Show in property forms'),
                                            ]),
                                        
                                        Fieldset::make('Display Order')
                                            ->schema([
                                                TextInput::make('sort_order')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0)
                                                    ->helperText('Lower numbers appear first'),
                                            ]),
                                    ])
                                    ->collapsible(),
                                
                                Section::make('Usage Statistics')
                                    ->schema([
                                        Fieldset::make('Property Count')
                                            ->schema([
                                                Placeholder::make('properties_count')
                                                    ->label('Properties with Feature')
                                                    ->content(fn ($record): string => $record?->properties_count ?? '0'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                                
                                Section::make('System Info')
                                    ->schema([
                                        Fieldset::make('Record Details')
                                            ->schema([
                                                Placeholder::make('created_at')
                                                    ->label('Created')
                                                    ->content(fn ($record): string => $record?->created_at?->diffForHumans() ?? 'Not created yet'),
                                                Placeholder::make('updated_at')
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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('icon')
                    ->searchable(),
                TextColumn::make('category'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
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
            'index' => ListPropertyFeatures::route('/'),
            'create' => CreatePropertyFeature::route('/create'),
            'view' => ViewPropertyFeature::route('/{record}'),
            'edit' => EditPropertyFeature::route('/{record}/edit'),
        ];
    }
}
