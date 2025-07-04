<?php

namespace App\Filament\Agent\Resources;

use Filament\Forms;
use App\Models\Area;
use App\Models\City;
use Filament\Tables;
use App\Models\State;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Property;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\PropertyType;
use App\Models\PropertyFeature;
use App\Models\PropertySubtype;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Agent\Resources\PropertyResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Agent\Resources\PropertyResource\RelationManagers;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'My Properties';

    protected static ?string $modelLabel = 'Property';

    protected static ?string $pluralModelLabel = 'Properties';

    protected static ?int $navigationSort = 1;

    /**
     * Scope queries to only show properties for the current independent agent
     */
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        Log::info('=== PropertyResource Query Started ===', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);
        
        // Get the agent profile for the current user
        $agent = $user->agentProfile;
        
        Log::info('Agent Profile Query Check', [
            'agent_exists' => $agent ? true : false,
            'agent_id' => $agent ? $agent->id : null,
            'agent_user_id' => $agent ? $agent->user_id : null,
        ]);
        
        if (!$agent) {
            Log::warning('No agent profile found - returning empty query');
            // If user has no agent profile, return empty query
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        $query = parent::getEloquentQuery()
            ->where('agent_id', $agent->id)
            ->whereNull('agency_id'); // Independent agents only (no agency)
            
        // Log the actual query being executed
        $queryCount = $query->count();
        Log::info('Property query results', [
            'agent_id_filter' => $agent->id,
            'properties_found' => $queryCount,
            'sql_query' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);
        
        return $query;
    }

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
                                                    ->options(fn (Get $get): array => Area::query()
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
                                            ->relationship('features', 'name')
                                            ->options(PropertyFeature::pluck('name', 'id'))
                                            ->columns(3)
                                            ->columnSpanFull()
                                            ->searchable(),
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
                                            ->responsiveImages()
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
                                            ->responsiveImages()
                                            ->maxFiles(20)
                                            ->maxSize(5120) // 5MB per file
                                            ->helperText('Upload up to 20 high-quality images showcasing the property')
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
                                            
                                        Forms\Components\Toggle::make('price_negotiable')
                                            ->label('Price Negotiable')
                                            ->default(false),
                                    ])->columns(1)->collapsible(),

                                // Property Owner - Sidebar
                                Forms\Components\Section::make('Property Owner')
                                    ->description('Property ownership information')
                                    ->schema([
                                        Forms\Components\Select::make('owner_id')
                                            ->label('Property Owner')
                                            ->relationship('owner', 'name', function ($query) {
                                                $user = auth()->user();
                                                $agentProfile = $user?->agentProfile;
                                                
                                                if ($agentProfile) {
                                                    // For independent agents, show only property owners they created
                                                    return $query->where('agent_id', $agentProfile->id);
                                                }
                                                
                                                // Fallback: show no property owners if agent profile not found
                                                return $query->whereRaw('1 = 0');
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
                                                    ->placeholder('Internal notes about this property owner')
                                                    ->helperText('These notes are for your internal use only'),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                $user = auth()->user();
                                                $agentProfile = $user?->agentProfile;
                                                
                                                // For independent agents, create property owners with agent_id but no agency_id
                                                $data['agency_id'] = null;
                                                $data['agent_id'] = $agentProfile?->id;
                                                
                                                Log::info('Creating new property owner from agent panel', [
                                                    'agent_id' => $agentProfile?->id,
                                                    'user_id' => $user?->id,
                                                    'owner_type' => $data['type'] ?? 'unknown',
                                                    'owner_name' => ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '') ?: ($data['company_name'] ?? 'unknown'),
                                                ]);
                                                
                                                $propertyOwner = \App\Models\PropertyOwner::create($data);
                                                
                                                Log::info('Property owner created successfully', [
                                                    'property_owner_id' => $propertyOwner->id,
                                                    'agent_id' => $propertyOwner->agent_id,
                                                ]);
                                                
                                                return $propertyOwner->id;
                                            })
                                            ->helperText('Select an existing property owner or click the + button to create a new owner profile. Property owners do not need user accounts.')
                                            ->columnSpanFull(),
                                    ])->columns(1)->collapsible(),

                                // Property Settings - Sidebar  
                                Forms\Components\Section::make('Property Settings')
                                    ->description('Property status and visibility settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured Property')
                                            ->default(false)
                                            ->helperText('Featured properties appear at the top of search results'),
                                            
                                        Forms\Components\Toggle::make('is_verified')
                                            ->label('Verified Property')
                                            ->default(false)
                                            ->helperText('Mark as verified after property inspection'),
                                            
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active Listing')
                                            ->default(true)
                                            ->helperText('Inactive listings are hidden from public search'),
                                    ])->columns(1)->collapsible(),

                                // Contact Information - Sidebar
                                Forms\Components\Section::make('Contact Information')
                                    ->description('Property viewing and contact details')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact_phone')
                                            ->label('Contact Phone')
                                            ->tel()
                                            ->helperText('Phone number for property inquiries'),
                                            
                                        Forms\Components\TextInput::make('contact_email')
                                            ->label('Contact Email')
                                            ->email()
                                            ->helperText('Email for property inquiries'),
                                            
                                        Forms\Components\Textarea::make('viewing_instructions')
                                            ->label('Viewing Instructions')
                                            ->rows(3)
                                            ->helperText('Special instructions for property viewings'),
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
                    ->colors([
                        'success' => 'rent',
                        'warning' => 'sale',
                        'info' => 'shortlet',
                        'primary' => 'lease',
                    ])
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'available',
                        'warning' => 'under_offer',
                        'danger' => ['sold', 'rented'],
                        'gray' => 'withdrawn',
                    ])
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Verified')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
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
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                        'under_offer' => 'Under Offer',
                        'withdrawn' => 'Withdrawn',
                    ]),
                Tables\Filters\SelectFilter::make('property_type_id')
                    ->relationship('propertyType', 'name')
                    ->label('Property Type')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('city_id')
                    ->relationship('city', 'name')
                    ->label('City')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Price from')
                            ->numeric()
                            ->prefix('₦'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Price to')
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
                Tables\Filters\Filter::make('bedrooms_range')
                    ->form([
                        Forms\Components\Select::make('bedrooms_min')
                            ->label('Min bedrooms')
                            ->options([
                                1 => '1+',
                                2 => '2+',
                                3 => '3+',
                                4 => '4+',
                                5 => '5+',
                            ]),
                        Forms\Components\Select::make('bedrooms_max')
                            ->label('Max bedrooms')
                            ->options([
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                                5 => '5+',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['bedrooms_min'],
                                fn (Builder $query, $bedrooms): Builder => $query->where('bedrooms', '>=', $bedrooms),
                            )
                            ->when(
                                $data['bedrooms_max'],
                                fn (Builder $query, $bedrooms): Builder => $query->where('bedrooms', '<=', $bedrooms),
                            );
                    }),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Properties')
                    ->placeholder('All properties')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified Properties')
                    ->placeholder('All properties')
                    ->trueLabel('Verified only')
                    ->falseLabel('Not verified'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
