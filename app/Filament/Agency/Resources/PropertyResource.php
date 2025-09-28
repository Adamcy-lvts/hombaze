<?php

namespace App\Filament\Agency\Resources;

use Filament\Forms;
use App\Models\Area;
use App\Models\City;
use Filament\Tables;
use App\Models\Agent;
use App\Models\State;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Property;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Models\PropertyFeature;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\Agency\Resources\PropertyResource\Pages;
use App\Filament\Agency\Resources\PropertyResource\RelationManagers;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    
    protected static ?string $navigationLabel = 'Properties';
    
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
                                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                                    ->columnSpanFull(),
                                                    
                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->hidden()
                                                    ->columnSpanFull(),
                                                    
                                                Forms\Components\Select::make('property_type_id')
                                                    ->label('Property Type')
                                                    ->relationship('propertyType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set) => $set('property_subtype_id', null))
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Select::make('property_subtype_id')
                                                    ->label('Property Subtype')
                                                    ->options(fn (Get $get): array => PropertySubtype::query()
                                                        ->where('property_type_id', $get('property_type_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Select subtype (optional)')
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
                                                    
                                                Forms\Components\Select::make('city_id')
                                                    ->label('City')
                                                    ->options(fn (Get $get): array => City::query()
                                                        ->where('state_id', $get('state_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set) => $set('area_id', null))
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Select::make('area_id')
                                                    ->label('Area')
                                                    ->options(fn (Get $get): array => Area::query()
                                                        ->where('city_id', $get('city_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Select area (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Textarea::make('address')
                                            ->label('Street Address')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('Enter the full street address')
                                            ->columnSpanFull(),
                                    ])->collapsible(),


                                // Property Features & Amenities
                                Forms\Components\Section::make('Features & Amenities')
                                    ->description('Select property features and amenities to highlight')
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
                                            ->helperText('Select all features and amenities that apply to this property. Features are grouped by category for easy selection.')
                                            ->columnSpanFull(),
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

                                // Media Files
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
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->customProperties([
                                                'caption' => null,
                                                'alt_text' => null,
                                            ])
                                            ->maxFiles(20)
                                            ->maxSize(5120) // 5MB per file
                                            ->helperText('Upload up to 20 high-quality images showcasing the property.')
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
                                            
                                        // Forms\Components\SpatieMediaLibraryFileUpload::make('videos')
                                        //     ->label('Property Videos')
                                        //     ->collection('videos')
                                        //     ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                                        //     ->multiple()
                                        //     ->reorderable()
                                        //     ->maxFiles(5)
                                        //     ->maxSize(51200) // 50MB per file
                                        //     ->helperText('Upload property tour videos (max 5 videos, 50MB each)')
                                        //     ->columnSpanFull(),
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
                                            ->visible(fn (Get $get): bool => in_array($get('listing_type'), ['rent', 'lease', 'shortlet'])),
                                            
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

                                // Property Features - Sidebar
                                Forms\Components\Section::make('Property Features')
                                    ->description('Physical characteristics and specifications')
                                    ->schema([
                                        Forms\Components\TextInput::make('bedrooms')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->required(),

                                        Forms\Components\TextInput::make('bathrooms')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->required(),

                                        Forms\Components\TextInput::make('toilets')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20),

                                        Forms\Components\TextInput::make('parking_spaces')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0),

                                        Forms\Components\TextInput::make('size_sqm')
                                            ->label('Size (sqm)')
                                            ->numeric()
                                            ->suffix('sqm'),

                                        Forms\Components\Select::make('furnishing_status')
                                            ->options([
                                                'unfurnished' => 'Unfurnished',
                                                'semi_furnished' => 'Semi Furnished',
                                                'furnished' => 'Fully Furnished',
                                            ])
                                            ->required(),

                                        Forms\Components\Select::make('compound_type')
                                            ->label('Compound/Estate Type')
                                            ->options(\App\Models\Property::getCompoundTypeOptions())
                                            ->searchable()
                                            ->placeholder('Select compound type...')
                                            ->helperText('Specify if the property is in a compound, estate, or standalone'),
                                    ])->columns(1)->collapsible(),

                                // Assignment & Management - Sidebar
                                Forms\Components\Section::make('Assignment & Management')
                                    ->description('Property ownership and agent assignment')
                                    ->schema([
                                        Forms\Components\Select::make('owner_id')
                                            ->label('Property Owner')
                                            ->relationship('owner', 'name', function ($query) {
                                                $agency = \Filament\Facades\Filament::getTenant();
                                                return $query->where('agency_id', $agency->id);
                                            })
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                                            ->searchable(['first_name', 'last_name', 'company_name', 'email'])
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\Select::make('type')
                                                    ->label('Owner Type')
                                                    ->options(\App\Models\PropertyOwner::getTypes())
                                                    ->default(\App\Models\PropertyOwner::TYPE_INDIVIDUAL)
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('company_name', null)),
                                                
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('first_name')
                                                            ->label('First Name')
                                                            ->required(fn (Forms\Get $get) => $get('type') === \App\Models\PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->visible(fn (Forms\Get $get) => $get('type') === \App\Models\PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('last_name')
                                                            ->label('Last Name')
                                                            ->required(fn (Forms\Get $get) => $get('type') === \App\Models\PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->visible(fn (Forms\Get $get) => $get('type') === \App\Models\PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->maxLength(255),
                                                    ]),
                                                
                                                Forms\Components\TextInput::make('company_name')
                                                    ->label('Company/Organization Name')
                                                    ->required(fn (Forms\Get $get) => $get('type') !== \App\Models\PropertyOwner::TYPE_INDIVIDUAL)
                                                    ->visible(fn (Forms\Get $get) => $get('type') !== \App\Models\PropertyOwner::TYPE_INDIVIDUAL)
                                                    ->maxLength(255),
                                                
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('email')
                                                            ->label('Email Address')
                                                            ->email()
                                                            ->maxLength(255)
                                                            ->placeholder('e.g., owner@example.com'),
                                                        Forms\Components\TextInput::make('phone')
                                                            ->label('Phone Number')
                                                            ->tel()
                                                            ->maxLength(20)
                                                            ->placeholder('e.g., +234 801 234 5678'),
                                                    ]),
                                                
                                                Forms\Components\Textarea::make('address')
                                                    ->label('Address')
                                                    ->maxLength(500)
                                                    ->placeholder('Full address of the property owner'),
                                                
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('city')
                                                            ->label('City')
                                                            ->maxLength(100),
                                                        Forms\Components\TextInput::make('state')
                                                            ->label('State')
                                                            ->maxLength(100),
                                                        Forms\Components\TextInput::make('country')
                                                            ->label('Country')
                                                            ->default('Nigeria')
                                                            ->maxLength(100),
                                                    ]),
                                                
                                                Forms\Components\TextInput::make('tax_id')
                                                    ->label('Tax ID / Business Registration')
                                                    ->maxLength(50)
                                                    ->placeholder('Optional tax identification number'),
                                                
                                                Forms\Components\Textarea::make('notes')
                                                    ->label('Internal Notes')
                                                    ->maxLength(1000)
                                                    ->placeholder('Internal notes about this property owner (not visible to owner)')
                                                    ->helperText('These notes are for agency internal use only'),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                // Automatically set the agency_id to the current tenant agency
                                                $agency = \Filament\Facades\Filament::getTenant();
                                                $data['agency_id'] = $agency->id;
                                                
                                                $propertyOwner = \App\Models\PropertyOwner::create($data);
                                                return $propertyOwner->id;
                                            })
                                            ->helperText('Select an existing property owner or click the + button to create a new owner profile. Property owners do not need user accounts.')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\Select::make('agent_id')
                                            ->label('Assigned Agent')
                                            ->relationship('agent', 'id', function ($query) {
                                                $agency = \Filament\Facades\Filament::getTenant();
                                                return $query->where('agency_id', $agency->id);
                                            })
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name ?? 'Unknown Agent')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Agent who will manage this property'),
                                    ])->columns(1)->collapsible(),

                                // Status & Settings - Sidebar
                                Forms\Components\Section::make('Property Settings')
                                    ->description('Property status and visibility settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active Listing')
                                            ->default(true),
                                    ])->columns(1)->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('Image')
                    ->collection('featured')
                    ->circular()
                    ->defaultImageUrl('/images/property-placeholder.svg'),
                    
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->weight('medium'),
                    
                Tables\Columns\TextColumn::make('propertyType.name')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('location_summary')
                    ->label('Location')
                    ->getStateUsing(fn ($record) => $record->area?->name . ', ' . $record->city?->name)
                    ->searchable(['area.name', 'city.name'])
                    ->limit(25),
                    
                Tables\Columns\TextColumn::make('bedrooms')
                    ->label('Bed')
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('bathrooms')
                    ->label('Bath')
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('NGN')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('listing_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'info',
                        'rent' => 'warning',
                        'lease' => 'primary',
                        'shortlet' => 'success',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'sold' => 'danger',
                        'rented' => 'warning',
                        'under_offer' => 'info',
                        'withdrawn' => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('agent.user.name')
                    ->label('Agent')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Unassigned'),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('property_type_id')
                    ->relationship('propertyType', 'name')
                    ->label('Property Type')
                    ->preload(),
                    
                SelectFilter::make('state_id')
                    ->relationship('state', 'name')
                    ->label('State')
                    ->preload(),
                    
                SelectFilter::make('city_id')
                    ->relationship('city', 'name')
                    ->label('City')
                    ->preload(),
                    
                SelectFilter::make('listing_type')
                    ->options([
                        'sale' => 'For Sale',
                        'rent' => 'For Rent',
                        'lease' => 'For Lease',
                        'shortlet' => 'Shortlet',
                    ]),
                    
                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'sold' => 'Sold',
                        'rented' => 'Rented',
                        'under_offer' => 'Under Offer',
                        'withdrawn' => 'Withdrawn',
                    ]),
                    
                SelectFilter::make('agent_id')
                    ->relationship('agent.user', 'name')
                    ->label('Agent')
                    ->preload(),
                    
                Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->numeric()
                            ->prefix('₦'),
                        Forms\Components\TextInput::make('price_to')
                            ->numeric()
                            ->prefix('₦'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Properties'),
                    
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified Properties'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn ($record) => $record->is_featured ? 'Unfeature' : 'Feature')
                    ->icon(fn ($record) => $record->is_featured ? 'heroicon-o-star' : 'heroicon-s-star')
                    ->color(fn ($record) => $record->is_featured ? 'gray' : 'warning')
                    ->action(function ($record) {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->requiresConfirmation(),
                    
                Tables\Actions\Action::make('change_status')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'sold' => 'Sold',
                                'rented' => 'Rented',
                                'under_offer' => 'Under Offer',
                                'withdrawn' => 'Withdrawn',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['status' => $data['status']]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Mark as Featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_featured' => true]))
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('unmark_featured')
                        ->label('Remove from Featured')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->action(fn ($records) => $records->each->update(['is_featured' => false]))
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'available' => 'Available',
                                    'sold' => 'Sold',
                                    'rented' => 'Rented',
                                    'under_offer' => 'Under Offer',
                                    'withdrawn' => 'Withdrawn',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['status' => $data['status']]);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
