<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\PropertyResource\Pages;
use App\Filament\Landlord\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Models\PropertyFeature;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertyOwner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Properties';

    protected static ?string $modelLabel = 'Property';

    protected static ?string $pluralModelLabel = 'Properties';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main content area (2/3 width) and Sidebar (1/3 width)
                Forms\Components\Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])
                    ->schema([
                        // Main Content Area (spans 2 columns)
                        Forms\Components\Group::make()
                            ->schema([
                                // Basic Property Information
                                Forms\Components\Section::make('Property Information')
                                    ->description('Basic details about the property')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                                    ->columnSpanFull(),

                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->disabled()
                                                    ->columnSpanFull(),

                                                Forms\Components\Select::make('property_type_id')
                                                    ->label('Property Type')
                                                    ->relationship('propertyType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn(Set $set) => $set('property_subtype_id', null))
                                                    ->columnSpan(1),

                                                Forms\Components\Select::make('property_subtype_id')
                                                    ->label('Property Subtype')
                                                    ->options(fn(Get $get): array => PropertySubtype::query()
                                                        ->where('property_type_id', $get('property_type_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Textarea::make('description')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Location Information
                                Forms\Components\Section::make('Location Details')
                                    ->description('Property location and address information')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 3,
                                        ])
                                            ->schema([
                                                Forms\Components\Select::make('state_id')
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

                                                Forms\Components\Select::make('city_id')
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

                                                Forms\Components\Select::make('area_id')
                                                    ->options(fn(Get $get): array => Area::query()
                                                        ->where('city_id', $get('city_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Textarea::make('address')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Property Features
                                Forms\Components\Section::make('Property Features')
                                    ->description('Physical characteristics and specifications')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 4,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('bedrooms')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(20)
                                                    ->required()
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('bathrooms')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(20)
                                                    ->required()
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('toilets')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(20)
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('parking_spaces')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('size_sqm')
                                                    ->label('Size (sqm)')
                                                    ->numeric()
                                                    ->suffix('sqm')
                                                    ->columnSpan(1),

                                                Forms\Components\Select::make('furnishing_status')
                                                    ->options([
                                                        'unfurnished' => 'Unfurnished',
                                                        'semi_furnished' => 'Semi Furnished',
                                                        'furnished' => 'Fully Furnished',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                            ]),
                                    ])->collapsible(),

                                // Property Features & Amenities
                                Forms\Components\Section::make('Property Features & Amenities')
                                    ->description('Select features and amenities available with this property')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('features')
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
                                            ->searchable()
                                            ->noSearchResultsMessage('No features found matching your search.')
                                            ->helperText('Select all features and amenities that apply to this property. Features are grouped by category for easy selection.')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Additional Fees & Charges
                                Forms\Components\Section::make('Additional Fees & Charges')
                                    ->description('Optional additional costs associated with this property')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('service_charge')
                                                    ->label('Service Charge')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->helperText('Monthly or annual service charge')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('agency_fee')
                                                    ->label('Agency Fee')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->helperText('One-time agency commission fee')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('legal_fee')
                                                    ->label('Legal Fee')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->helperText('Legal documentation fees')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('caution_deposit')
                                                    ->label('Caution Deposit')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->helperText('Refundable security deposit')
                                                    ->columnSpan(1),
                                            ]),
                                    ])->collapsible(),

                                // Additional Property Details
                                Forms\Components\Section::make('Additional Details')
                                    ->description('Optional additional property information')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('year_built')
                                                    ->label('Year Built')
                                                    ->numeric()
                                                    ->minValue(1800)
                                                    ->maxValue(date('Y') + 5)
                                                    ->placeholder('e.g., 2020')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('landmark')
                                                    ->label('Nearby Landmark')
                                                    ->maxLength(255)
                                                    ->placeholder('e.g., Near Shopping Mall, Close to School')
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('latitude')
                                                    ->label('Latitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->placeholder('e.g., 6.524379')
                                                    ->helperText('GPS coordinates for map display')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('longitude')
                                                    ->label('Longitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->placeholder('e.g., 3.379206')
                                                    ->helperText('GPS coordinates for map display')
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\TextInput::make('video_url')
                                            ->label('Property Video URL')
                                            ->url()
                                            ->placeholder('https://youtube.com/watch?v=...')
                                            ->helperText('YouTube or Vimeo video link for property tour')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\TextInput::make('virtual_tour_url')
                                            ->label('Virtual Tour URL')
                                            ->url()
                                            ->placeholder('https://...')
                                            ->helperText('Link to 360° virtual tour or 3D walkthrough')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Property Media
                                Forms\Components\Section::make('Property Media')
                                    ->description('Upload images, videos, documents and floor plans')
                                    ->schema([
                                        Forms\Components\SpatieMediaLibraryFileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->collection('featured')
                                            ->image()
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '16:9',
                                                '4:3', 
                                                '1:1',
                                            ])
                                            ->customProperties([
                                                'caption' => null,
                                                'alt_text' => null,
                                            ])
                                            // ->responsiveImages() // Test basic conversions first
                                            ->maxSize(5120) // 5MB
                                            ->helperText('Upload a high-quality featured image for this property')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\SpatieMediaLibraryFileUpload::make('gallery_images')
                                            ->label('Gallery Images')
                                            ->collection('gallery')
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->imageEditor()
                                            ->customProperties([
                                                'caption' => null,
                                                'alt_text' => null,
                                            ])
                                            // ->responsiveImages() // Test basic conversions first
                                            ->maxFiles(20)
                                            ->maxSize(5120) // 5MB per file
                                            ->helperText('Upload up to 20 high-quality images showcasing the property. You can add captions to each image after upload.')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\SpatieMediaLibraryFileUpload::make('floor_plans')
                                            ->label('Floor Plans')
                                            ->collection('floor_plans')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                            ->multiple()
                                            ->reorderable()
                                            ->maxFiles(10)
                                            ->maxSize(10240) // 10MB per file
                                            ->helperText('Upload floor plan images or PDF documents')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
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
                        Forms\Components\Group::make()
                            ->schema([
                                // Pricing & Listing Details - Sidebar
                                Forms\Components\Section::make('Pricing & Listing')
                                    ->description('Property pricing and listing information')
                                    ->schema([
                                        Forms\Components\Select::make('listing_type')
                                            ->options([
                                                'sale' => 'For Sale',
                                                'rent' => 'For Rent',
                                                'lease' => 'For Lease',
                                                'shortlet' => 'Shortlet',
                                            ])
                                            ->required()
                                            ->live(),

                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->prefix('₦')
                                            ->required(),

                                        Forms\Components\Select::make('price_period')
                                            ->label('Price Period')
                                            ->options([
                                                'per_month' => 'Per Month',
                                                'per_year' => 'Per Year',
                                                'per_night' => 'Per Night',
                                                'total' => 'Total',
                                            ])
                                            ->visible(fn(Get $get): bool => in_array($get('listing_type'), ['rent', 'lease', 'shortlet'])),

                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'available' => 'Available',
                                                'sold' => 'Sold',
                                                'rented' => 'Rented',
                                                'under_offer' => 'Under Offer',
                                                'withdrawn' => 'Withdrawn',
                                            ])
                                            ->required()
                                            ->default('available'),
                                    ])->columns(1)->collapsible(),

                                // Property Management - Sidebar (Landlord Context)
                                Forms\Components\Section::make('Property Management')
                                    ->description('Additional property management settings')
                                    ->schema([
                                        Forms\Components\Placeholder::make('owner_info')
                                            ->label('Property Owner')
                                            ->content('You are the owner of this property')
                                            ->helperText('As a landlord, you are automatically set as the owner of properties you create')
                                            ->columnSpanFull(),
                                    ])->columns(1)->collapsible(),

                                // Status & Settings - Sidebar
                                Forms\Components\Section::make('Property Settings')
                                    ->description('Property status and visibility settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured Property')
                                            ->default(false),

                                        Forms\Components\Toggle::make('is_verified')
                                            ->label('Verified Property')
                                            ->default(false),

                                        Forms\Components\Toggle::make('is_published')
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Property Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('listing_type')
                    ->badge()
                    ->label('Listing Type')
                    ->colors([
                        'primary' => 'rent',
                        'success' => 'sale',
                        'warning' => 'lease',
                        'info' => 'shortlet',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'available',
                        'danger' => 'sold',
                        'warning' => 'rented',
                        'gray' => 'under_offer',
                        'secondary' => 'withdrawn',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('price')
                    ->prefix('₦')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_period')
                    ->label('Period')
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('bedrooms')
                    ->label('Beds')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bathrooms')
                    ->label('Baths')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('propertyType.name')
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

                Tables\Columns\TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('City')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('listing_type')
                    ->options([
                        'sale' => 'For Sale',
                        'rent' => 'For Rent',
                        'lease' => 'For Lease',
                        'shortlet' => 'Shortlet',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'sold' => 'Sold',
                        'rented' => 'Rented',
                        'under_offer' => 'Under Offer',
                        'withdrawn' => 'Withdrawn',
                    ]),

                Tables\Filters\SelectFilter::make('property_type_id')
                    ->label('Property Type')
                    ->relationship('propertyType', 'name'),

                Tables\Filters\SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name'),

                Tables\Filters\Filter::make('is_featured')
                    ->query(fn(Builder $query): Builder => $query->where('is_featured', true))
                    ->label('Featured Only'),

                Tables\Filters\Filter::make('is_published')
                    ->query(fn(Builder $query): Builder => $query->where('is_published', true))
                    ->label('Published Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
