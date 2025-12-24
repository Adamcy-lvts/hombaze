<?php

namespace App\Filament\Landlord\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Landlord\Resources\PropertyResource\Pages\ListProperties;
use App\Filament\Landlord\Resources\PropertyResource\Pages\CreateProperty;
use App\Filament\Landlord\Resources\PropertyResource\Pages\EditProperty;
use App\Filament\Landlord\Resources\PropertyResource\Pages;
use App\Filament\Landlord\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use App\Rules\OptimalImageResolution;
use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Models\PropertyFeature;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertyOwner;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Str;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Enums\PropertyStatus;
use Filament\Infolists\Components\TextEntry;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Properties';

    protected static ?string $modelLabel = 'Property';

    protected static ?string $pluralModelLabel = 'Properties';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Main content area (2/3 width) and Sidebar (1/3 width)
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])
                    ->schema([
                        // Main Content Area (spans 2 columns)
                        Group::make()
                            ->schema([
                                // Basic Property Information
                                Section::make('Property Information')
                                    ->description('Basic details about the property')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                                    ->columnSpanFull(),

                                                TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->hidden()
                                                    ->columnSpanFull(),

                                                Select::make('property_type_id')
                                                    ->label('Property Type')
                                                    ->relationship('propertyType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn(Set $set) => $set('property_subtype_id', null))
                                                    ->columnSpan(1),

                                                Select::make('property_subtype_id')
                                                    ->label('Property Subtype')
                                                    ->options(fn(Get $get): array => PropertySubtype::query()
                                                        ->where('property_type_id', $get('property_type_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Select subtype (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                        Textarea::make('description')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Location Information
                                Section::make('Location Details')
                                    ->description('Property location and address information')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 3,
                                        ])
                                            ->schema([
                                                Select::make('state_id')
                                                    ->label('State')
                                                    ->relationship('state', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set) {
                                                        $set('city_id', null);
                                                        $set('area_id', null);
                                                    })
                                                    ->columnSpan(1),

                                                Select::make('city_id')
                                                    ->label('City')
                                                    ->options(fn(Get $get): array => City::query()
                                                        ->where('state_id', $get('state_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn(Set $set) => $set('area_id', null))
                                                    ->columnSpan(1),

                                                Select::make('area_id')
                                                    ->label('Area')
                                                    ->options(fn(Get $get): array => Area::query()
                                                        ->where('city_id', $get('city_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Select area (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                        Textarea::make('address')
                                            ->label('Street Address')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('Enter the full street address')
                                            ->columnSpanFull(),
                                    ])->collapsible(),


                                // Property Features & Amenities
                                Section::make('Property Features & Amenities')
                                    ->description('Select features and amenities available with this property')
                                    ->schema([
                                        CheckboxList::make('features')
                                            ->label('Property Features')
                                            ->relationship('features', 'name')
                                            ->options(function () {
                                                return PropertyFeature::active()
                                                    ->ordered()
                                                    ->pluck('name', 'id')
                                                    ->toArray();
                                            })
                                            ->descriptions(function () {
                                                return PropertyFeature::active()
                                                    ->ordered()
                                                    ->pluck('description', 'id')
                                                    ->toArray();
                                            })
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->bulkToggleable()
                                            ->helperText('Select all features and amenities that apply to this property. Features are grouped by category for easy selection.')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Additional Property Details
                                Section::make('Additional Details')
                                    ->description('Optional additional property information')
                                    ->collapsed()
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('year_built')
                                                    ->label('Year Built')
                                                    ->numeric()
                                                    ->minValue(1800)
                                                    ->maxValue(date('Y') + 5)
                                                    ->placeholder('e.g., 2020')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('landmark')
                                                    ->label('Nearby Landmark')
                                                    ->maxLength(255)
                                                    ->placeholder('e.g., Near Shopping Mall, Close to School')
                                                    ->columnSpan(1),
                                            ]),
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('latitude')
                                                    ->label('Latitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->placeholder('e.g., 6.524379')
                                                    ->helperText('GPS coordinates for map display')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('longitude')
                                                    ->label('Longitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->placeholder('e.g., 3.379206')
                                                    ->helperText('GPS coordinates for map display')
                                                    ->columnSpan(1),
                                            ]),
                                        TextInput::make('video_url')
                                            ->label('Property Video URL')
                                            ->url()
                                            ->placeholder('https://youtube.com/watch?v=...')
                                            ->helperText('YouTube or Vimeo video link for property tour')
                                            ->columnSpanFull(),
                                            
                                        TextInput::make('virtual_tour_url')
                                            ->label('Virtual Tour URL')
                                            ->url()
                                            ->placeholder('https://...')
                                            ->helperText('Link to 360° virtual tour or 3D walkthrough')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Property Media
                                Section::make('Property Media')
                                    ->description('Upload images, videos, documents and floor plans')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->collection('featured')
                                            ->image()
                                            ->imageEditor()
                                            ->imageEditorAspectRatios(['3:2', '16:9', '4:3'])
                                            ->customProperties([
                                                'caption' => null,
                                                'alt_text' => null,
                                            ])
                                            ->acceptedFileTypes(getOptimalImageResolution()['formats'])
                                            ->maxSize(getOptimalImageResolution()['max_file_size'])
                                            ->required()
                                            ->rules([
                                                new OptimalImageResolution(false)
                                            ])
                                            ->validationMessages([
                                                'required' => '🖼️ A featured image is required to showcase your property effectively.',
                                            ])
                                            ->live(onBlur: true)
                                            ->helperText('Upload a high-quality featured image for this property. ' . getOptimalImageResolution()['quality_note'])
                                            ->columnSpanFull(),
                                            
                                        SpatieMediaLibraryFileUpload::make('gallery_images')
                                            ->label('Gallery Images')
                                            ->collection('gallery')
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->imageEditor()
                                            ->acceptedFileTypes(getOptimalImageResolution()['formats'])
                                            ->customProperties([
                                                'caption' => null,
                                                'alt_text' => null,
                                            ])
                                            ->maxFiles(function (Get $get) {
                                                $propertyTypeId = $get('property_type_id');
                                                if ($propertyTypeId) {
                                                    $propertyType = PropertyType::find($propertyTypeId);
                                                    if ($propertyType) {
                                                        return getPropertyImageConfig($propertyType->slug)['gallery_max_files'];
                                                    }
                                                }
                                                return getPropertyImageConfig()['gallery_max_files'];
                                            })
                                            ->maxSize(getOptimalImageResolution()['max_file_size'])
                                            ->minFiles(1)
                                            ->rules([
                                                new OptimalImageResolution(true)
                                            ])
                                            ->validationMessages([
                                                'min' => '📸 Please add at least one gallery image to showcase your property.',
                                            ])
                                            ->live(onBlur: true)
                                            ->helperText(function (Get $get) {
                                                $propertyTypeId = $get('property_type_id');
                                                $resolutionInfo = getOptimalImageResolution();
                                                if ($propertyTypeId) {
                                                    $propertyType = PropertyType::find($propertyTypeId);
                                                    if ($propertyType) {
                                                        return getPropertyImageConfig($propertyType->slug)['gallery_helper_text'] . ' ' . $resolutionInfo['quality_note'];
                                                    }
                                                }
                                                return getPropertyImageConfig()['gallery_helper_text'] . ' ' . $resolutionInfo['quality_note'];
                                            })
                                            ->columnSpanFull(),
                                            
                                        SpatieMediaLibraryFileUpload::make('floor_plans')
                                            ->label('Floor Plans')
                                            ->collection('floor_plans')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                            ->multiple()
                                            ->reorderable()
                                            ->maxFiles(10)
                                            ->maxSize(10240) // 10MB per file
                                            ->helperText('Upload floor plan images or PDF documents')
                                            ->columnSpanFull(),
                                            
                                        SpatieMediaLibraryFileUpload::make('documents')
                                            ->label('Property Documents')
                                            ->collection('documents')
                                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                            ->multiple()
                                            ->reorderable()
                                            ->maxFiles(10)
                                            ->maxSize(20480) // 20MB per file
                                            ->helperText('Upload property documents, certificates, contracts, etc.')
                                            ->columnSpanFull(),
                                    ])->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                            ]),

                        // Sidebar (spans 1 column)
                        Group::make()
                            ->schema([
                                // Pricing & Listing Details - Sidebar
                                Section::make('Pricing & Listing')
                                    ->description('Property pricing and listing information')
                                    ->schema([
                                        Select::make('listing_type')
                                            ->options([
                                                'sale' => 'For Sale',
                                                'rent' => 'For Rent',
                                                'lease' => 'For Lease',
                                                'shortlet' => 'Shortlet',
                                            ])
                                            ->required()
                                            ->live(),

                                        TextInput::make('price')
                                            ->numeric()
                                            ->prefix('₦')
                                            ->required(),

                                        Select::make('price_period')
                                            ->label('Price Period')
                                            ->options([
                                                'per_month' => 'Per Month',
                                                'per_year' => 'Per Year',
                                                'per_night' => 'Per Night',
                                                'total' => 'Total',
                                            ])
                                            ->visible(fn(Get $get): bool => in_array($get('listing_type'), ['rent', 'lease', 'shortlet'])),

                                        Select::make('status')
                                            ->options(PropertyStatus::class)
                                            ->required()
                                            ->default('available'),
                                    ])->columns(1)->collapsible(),


                                // Property Features - Sidebar
                                Section::make('Property Features')
                                    ->description('Physical characteristics and specifications')
                                    ->schema([
                                        TextInput::make('bedrooms')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->required(fn (Get $get): bool => static::isFieldRequired('bedrooms', $get))
                                            ->visible(fn (Get $get): bool => static::isFieldVisible('bedrooms', $get)),

                                        TextInput::make('bathrooms')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->required(fn (Get $get): bool => static::isFieldRequired('bathrooms', $get))
                                            ->visible(fn (Get $get): bool => static::isFieldVisible('bathrooms', $get)),

                                        TextInput::make('toilets')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->visible(fn (Get $get): bool => static::isFieldVisible('toilets', $get)),

                                        TextInput::make('parking_spaces')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->visible(fn (Get $get): bool => static::isFieldVisible('parking_spaces', $get)),

                                        TextInput::make('size_sqm')
                                            ->label('Size (sqm)')
                                            ->numeric()
                                            ->suffix('sqm'),

                                        Select::make('furnishing_status')
                                            ->options([
                                                'unfurnished' => 'Unfurnished',
                                                'semi_furnished' => 'Semi Furnished',
                                                'furnished' => 'Fully Furnished',
                                            ])
                                            ->required(fn (Get $get): bool => static::isFieldRequired('furnishing_status', $get))
                                            ->visible(fn (Get $get): bool => static::isFieldVisible('furnishing_status', $get)),
                                    ])->columns(1)->collapsible(),

                                // Property Management - Sidebar (Landlord Context)
                                Section::make('Property Management')
                                    ->description('Additional property management settings')
                                    ->schema([
                                        TextEntry::make('owner_info')
                                            ->label('Property Owner')
                                            ->content('You are the owner of this property')
                                            ->helperText('As a landlord, you are automatically set as the owner of properties you create')
                                            ->columnSpanFull(),
                                    ])->columns(1)->collapsible(),

                                // Status & Settings - Sidebar
                                Section::make('Property Settings')
                                    ->description('Property status and visibility settings')
                                    ->schema([
                                        Toggle::make('is_published')
                                            ->label('Published Listing')
                                            ->helperText('Make this property visible to the public')
                                            ->default(true),
                                    ])->columns(1)->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Property Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('listing_type')
                    ->badge()
                    ->label('Listing Type')
                    ->colors([
                        'primary' => 'rent',
                        'success' => 'sale',
                        'warning' => 'lease',
                        'info' => 'shortlet',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),


                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => PropertyStatus::from($state)->getLabel())
                    ->color(fn (string $state): string => PropertyStatus::from($state)->getColor())
                    ->icon(fn (string $state): string => PropertyStatus::from($state)->getIcon())
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->prefix('₦')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('price_period')
                    ->label('Period')
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),

                TextColumn::make('bedrooms')
                    ->label('Beds')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('bathrooms')
                    ->label('Baths')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('propertyType.name')
                    ->label('Type')
                    ->sortable(),

                // Tables\Columns\TextColumn::make('features.name')
                //     ->label('Features')
                //     ->badge()
                //     ->separator(',')
                //     ->limit(20)
                //     ->tooltip(function ($record) {
                //         return $record->features->pluck('name')->join(', ');
                //     })
                //     ->toggleable(),

                TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('city.name')
                    ->label('City')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('listing_type')
                    ->options([
                        'sale' => 'For Sale',
                        'rent' => 'For Rent',
                        'lease' => 'For Lease',
                        'shortlet' => 'Shortlet',
                    ]),

                SelectFilter::make('status')
                    ->options(PropertyStatus::class),

                SelectFilter::make('property_type_id')
                    ->label('Property Type')
                    ->relationship('propertyType', 'name'),

                SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name'),

                Filter::make('is_featured')
                    ->query(fn(Builder $query): Builder => $query->where('is_featured', true))
                    ->label('Featured Only'),

                Filter::make('is_published')
                    ->query(fn(Builder $query): Builder => $query->where('is_published', true))
                    ->label('Published Only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                ActionGroup::make([
                    Action::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->schema([
                            Select::make('status')
                                ->options(PropertyStatus::class)
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update(['status' => $data['status']]);
                        }),

                    Action::make('mark_sold')
                        ->label('Mark Sold')
                        ->icon('heroicon-o-banknotes')
                        ->color('danger')
                        ->action(fn ($record) => $record->update(['status' => PropertyStatus::SOLD->value]))
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== PropertyStatus::SOLD->value),

                    Action::make('mark_off_market')
                        ->label('Take Off Market')
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->action(fn ($record) => $record->update(['status' => PropertyStatus::OFF_MARKET->value]))
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== PropertyStatus::OFF_MARKET->value),
                ])->label('Status')->icon('heroicon-o-ellipsis-vertical')->color('info'),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('owner', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProperties::route('/'),
            'create' => CreateProperty::route('/create'),
            'edit' => EditProperty::route('/{record}/edit'),
        ];
    }

    /**
     * Check if field should be visible based on property type
     */
    public static function isFieldVisible(string $fieldName, Get $get): bool
    {
        $propertyTypeId = $get('property_type_id');

        if (!$propertyTypeId) {
            return true; // Show all fields if no property type selected
        }

        $propertyType = PropertyType::find($propertyTypeId);
        if (!$propertyType) {
            return true;
        }

        $hiddenFields = Property::getHiddenFieldsForType($propertyType->slug);

        return !in_array($fieldName, $hiddenFields);
    }

    /**
     * Check if field should be required based on property type
     */
    public static function isFieldRequired(string $fieldName, Get $get): bool
    {
        $propertyTypeId = $get('property_type_id');

        if (!$propertyTypeId) {
            return false; // Don't require fields if no property type selected
        }

        $propertyType = PropertyType::find($propertyTypeId);
        if (!$propertyType) {
            return false;
        }

        $requiredFields = Property::getRequiredFieldsForType($propertyType->slug);

        return in_array($fieldName, $requiredFields);
    }
}
