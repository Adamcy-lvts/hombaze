<?php

namespace App\Filament\Agent\Resources;

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
use Filament\Forms\Components\Placeholder;
use App\Models\PropertyOwner;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Agent\Resources\PropertyResource\Pages\ListProperties;
use App\Filament\Agent\Resources\PropertyResource\Pages\CreateProperty;
use App\Filament\Agent\Resources\PropertyResource\Pages\EditProperty;
use Filament\Forms;
use App\Models\Area;
use App\Models\City;
use Filament\Tables;
use App\Models\State;
use App\Models\Property;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\PropertyType;
use App\Models\PropertyFeature;
use App\Models\PropertySubtype;
use App\Models\PlotSize;
use App\Enums\PropertyStatus;
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
use App\Rules\OptimalImageResolution;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home-modern';

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
                                Section::make('Features & Amenities')
                                    ->description('Select property features and amenities to highlight')
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

                                // Media Files
                                Section::make('Property Media')
                                    ->description('Upload images, videos, documents and floor plans')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->collection('featured')
                                            ->image()
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '3:2',
                                                '16:9',
                                                '4:3',
                                            ])
                                            ->acceptedFileTypes(getOptimalImageResolution()['formats'])
                                            ->maxSize(getOptimalImageResolution()['max_file_size'])
                                            ->required()
                                            ->rules([
                                                new OptimalImageResolution(false)
                                            ])
                                            ->helperText('Upload a high-quality featured image for this property. ' . getOptimalImageResolution()['quality_note'])
                                            ->live(onBlur: true)
                                            ->columnSpanFull(),

                                        SpatieMediaLibraryFileUpload::make('gallery_images')
                                            ->label('Gallery Images')
                                            ->collection('gallery')
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
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
                                            ->live(onBlur: true)
                                            ->columnSpanFull(),

                                        // Forms\Components\SpatieMediaLibraryFileUpload::make('floor_plans')
                                        //     ->label('Floor Plans')
                                        //     ->collection('floor_plans')
                                        //     ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                        //     ->multiple()
                                        //     ->reorderable()
                                        //     ->maxFiles(10)
                                        //     ->maxSize(10240) // 10MB per file
                                        //     ->helperText('Upload floor plan images or PDF documents')
                                        //     ->columnSpanFull(),
                                        //
                                        // Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
                                        //     ->label('Property Documents')
                                        //     ->collection('documents')
                                        //     ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                        //     ->multiple()
                                        //     ->reorderable()
                                        //     ->maxFiles(10)
                                        //     ->maxSize(20480) // 20MB per file
                                        //     ->helperText('Upload property documents, certificates, contracts, etc.')
                                        //     ->columnSpanFull(),
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
                                            ->options([
                                                'available' => 'Available',
                                                'sold' => 'Sold',
                                                'rented' => 'Rented',
                                                'under_offer' => 'Under Offer',
                                                'withdrawn' => 'Withdrawn',
                                            ])
                                            ->required()
                                            ->default('available'),

                                        Toggle::make('price_negotiable')
                                            ->label('Price Negotiable')
                                            ->default(false),
                                    ])->columns(1)->collapsible(),


                                // Property Features - Sidebar
                                Section::make('Property Features')
                                    ->description('Physical characteristics and specifications')
                                    ->schema([
                                        TextInput::make('bedrooms')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->required(fn(Get $get): bool => static::isFieldRequired('bedrooms', $get))
                                            ->visible(fn(Get $get): bool => static::isFieldVisible('bedrooms', $get)),

                                        TextInput::make('bathrooms')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->required(fn(Get $get): bool => static::isFieldRequired('bathrooms', $get))
                                            ->visible(fn(Get $get): bool => static::isFieldVisible('bathrooms', $get)),

                                        TextInput::make('toilets')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(20)
                                            ->visible(fn(Get $get): bool => static::isFieldVisible('toilets', $get)),

                                        TextInput::make('parking_spaces')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->visible(fn(Get $get): bool => static::isFieldVisible('parking_spaces', $get)),

                                        TextInput::make('size_sqm')
                                            ->label('Size (sqm)')
                                            ->numeric()
                                            ->suffix('sqm')
                                            ->visible(fn(Get $get): bool => !in_array($get('property_type_id'), [3])), // Hide for land properties

                                        // Plot Size Selection for Land Properties
                                        Select::make('plot_size_id')
                                            ->label('Standard Plot Size')
                                            ->options(PlotSize::getFormOptions())
                                            ->searchable()
                                            ->placeholder('Select a standard plot size...')
                                            ->visible(fn(Get $get): bool => in_array($get('property_type_id'), [3])) // Show only for land properties
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                if ($state) {
                                                    $plotSize = PlotSize::find($state);
                                                    if ($plotSize) {
                                                        $set('size_sqm', $plotSize->size_in_sqm);
                                                        $set('custom_plot_size', null);
                                                        $set('custom_plot_unit', null);
                                                    }
                                                }
                                            })
                                            ->helperText('Select from predefined plot sizes'),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('custom_plot_size')
                                                    ->label('Custom Plot Size')
                                                    ->numeric()
                                                    ->step(0.01)
                                                    ->placeholder('e.g., 1200')
                                                    ->visible(fn(Get $get): bool => in_array($get('property_type_id'), [3]) && !$get('plot_size_id'))
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                                        if ($state && $get('custom_plot_unit')) {
                                                            $sqm = PlotSize::convertToSquareMeters((float) $state, $get('custom_plot_unit'));
                                                            $set('size_sqm', $sqm);
                                                        }
                                                    })
                                                    ->helperText('Enter custom size value'),

                                                Select::make('custom_plot_unit')
                                                    ->label('Unit')
                                                    ->options(PlotSize::getUnits())
                                                    ->default('sqm')
                                                    ->visible(fn(Get $get): bool => in_array($get('property_type_id'), [3]) && !$get('plot_size_id'))
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                                        if ($state && $get('custom_plot_size')) {
                                                            $sqm = PlotSize::convertToSquareMeters((float) $get('custom_plot_size'), $state);
                                                            $set('size_sqm', $sqm);
                                                        }
                                                    })
                                                    ->helperText('Select unit of measurement'),
                                            ])
                                            ->visible(fn(Get $get): bool => in_array($get('property_type_id'), [3]) && !$get('plot_size_id')),

                                        Placeholder::make('calculated_sqm')
                                            ->label('Calculated Size in SQM')
                                            ->content(fn(Get $get): string => $get('size_sqm') ? number_format($get('size_sqm'), 0) . ' sqm' : 'Not calculated')
                                            ->visible(fn(Get $get): bool => in_array($get('property_type_id'), [3])),

                                        Select::make('furnishing_status')
                                            ->options([
                                                'unfurnished' => 'Unfurnished',
                                                'semi_furnished' => 'Semi Furnished',
                                                'furnished' => 'Fully Furnished',
                                            ])
                                            ->required(fn(Get $get): bool => static::isFieldRequired('furnishing_status', $get))
                                            ->visible(fn(Get $get): bool => static::isFieldVisible('furnishing_status', $get)),

                                        Select::make('compound_type')
                                            ->label('Compound/Estate Type')
                                            ->options(Property::getCompoundTypeOptions())
                                            ->searchable()
                                            ->placeholder('Select compound type...')
                                            ->helperText('Specify if the property is in a compound, estate, or standalone'),
                                    ])->columns(1)->collapsible(),

                                // Property Owner - Sidebar
                                Section::make('Property Owner')
                                    ->description('Property ownership information')
                                    ->schema([
                                        Select::make('owner_id')
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
                                            ->getOptionLabelFromRecordUsing(fn($record) => $record->display_name)
                                            ->searchable(['first_name', 'last_name', 'company_name', 'email'])
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                Select::make('type')
                                                    ->label('Owner Type')
                                                    ->options(PropertyOwner::getTypes())
                                                    ->default(PropertyOwner::TYPE_INDIVIDUAL)
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn($state, Set $set) => $set('company_name', null)),

                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('first_name')
                                                            ->label('First Name')
                                                            ->required(fn(Get $get) => $get('type') === PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->visible(fn(Get $get) => $get('type') === PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->maxLength(255),
                                                        TextInput::make('last_name')
                                                            ->label('Last Name')
                                                            ->required(fn(Get $get) => $get('type') === PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->visible(fn(Get $get) => $get('type') === PropertyOwner::TYPE_INDIVIDUAL)
                                                            ->maxLength(255),
                                                    ]),

                                                TextInput::make('company_name')
                                                    ->label('Company/Organization Name')
                                                    ->required(fn(Get $get) => $get('type') !== PropertyOwner::TYPE_INDIVIDUAL)
                                                    ->visible(fn(Get $get) => $get('type') !== PropertyOwner::TYPE_INDIVIDUAL)
                                                    ->maxLength(255),

                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('email')
                                                            ->label('Email Address')
                                                            ->email()
                                                            ->maxLength(255)
                                                            ->placeholder('e.g., owner@example.com'),
                                                        TextInput::make('phone')
                                                            ->label('Phone Number')
                                                            ->tel()
                                                            ->maxLength(20)
                                                            ->placeholder('e.g., +234 801 234 5678'),
                                                    ]),

                                                Textarea::make('address')
                                                    ->label('Address')
                                                    ->maxLength(500)
                                                    ->placeholder('Full address of the property owner'),

                                                Grid::make(3)
                                                    ->schema([
                                                        TextInput::make('city')
                                                            ->label('City')
                                                            ->maxLength(100),
                                                        TextInput::make('state')
                                                            ->label('State')
                                                            ->maxLength(100),
                                                        TextInput::make('country')
                                                            ->label('Country')
                                                            ->default('Nigeria')
                                                            ->maxLength(100),
                                                    ]),

                                                TextInput::make('tax_id')
                                                    ->label('Tax ID / Business Registration')
                                                    ->maxLength(50)
                                                    ->placeholder('Optional tax identification number'),

                                                Textarea::make('notes')
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

                                                $propertyOwner = PropertyOwner::create($data);

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
                                Section::make('Property Settings')
                                    ->description('Property status and visibility settings')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Active Listing')
                                            ->default(true)
                                            ->helperText('Inactive listings are hidden from public search'),
                                    ])->columns(1)->collapsible(),

                                // Contact Information - Sidebar
                                Section::make('Contact Information')
                                    ->description('Property viewing and contact details')
                                    ->collapsed()
                                    ->schema([
                                        TextInput::make('contact_phone')
                                            ->label('Contact Phone')
                                            ->tel()
                                            ->helperText('Phone number for property inquiries'),

                                        TextInput::make('contact_email')
                                            ->label('Contact Email')
                                            ->email()
                                            ->helperText('Email for property inquiries'),

                                        Textarea::make('viewing_instructions')
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
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('Image')
                    ->collection('featured')
                    ->circular()
                    ->defaultImageUrl('/images/property-placeholder.svg'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->weight('medium'),

                TextColumn::make('propertyType.name')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('location_summary')
                    ->label('Location')
                    ->getStateUsing(fn($record) => $record->area?->name . ', ' . $record->city?->name)
                    ->searchable(['area.name', 'city.name'])
                    ->limit(25),

                TextColumn::make('bedrooms')
                    ->label('Bed')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('bathrooms')
                    ->label('Bath')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn($state) => formatNaira($state ?? 0))
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('listing_type')
                    ->badge()
                    ->colors([
                        'success' => 'rent',
                        'warning' => 'sale',
                        'info' => 'shortlet',
                        'primary' => 'lease',
                    ])
                    ->sortable(),


                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => PropertyStatus::from($state)->getLabel())
                    ->color(fn(string $state): string => PropertyStatus::from($state)->getColor())
                    ->icon(fn(string $state): string => PropertyStatus::from($state)->getIcon())
                    ->sortable()
                    ->searchable(),

                IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->toggleable(),

                IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Verified')
                    ->toggleable(),

                TextColumn::make('created_at')
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
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                        'under_offer' => 'Under Offer',
                        'withdrawn' => 'Withdrawn',
                    ]),
                SelectFilter::make('property_type_id')
                    ->relationship('propertyType', 'name')
                    ->label('Property Type')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('city_id')
                    ->relationship('city', 'name')
                    ->label('City')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('price_range')
                    ->schema([
                        TextInput::make('price_from')
                            ->label('Price from')
                            ->numeric()
                            ->prefix('₦'),
                        TextInput::make('price_to')
                            ->label('Price to')
                            ->numeric()
                            ->prefix('₦'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn(Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn(Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                Filter::make('bedrooms_range')
                    ->schema([
                        Select::make('bedrooms_min')
                            ->label('Min bedrooms')
                            ->options([
                                1 => '1+',
                                2 => '2+',
                                3 => '3+',
                                4 => '4+',
                                5 => '5+',
                            ]),
                        Select::make('bedrooms_max')
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
                                fn(Builder $query, $bedrooms): Builder => $query->where('bedrooms', '>=', $bedrooms),
                            )
                            ->when(
                                $data['bedrooms_max'],
                                fn(Builder $query, $bedrooms): Builder => $query->where('bedrooms', '<=', $bedrooms),
                            );
                    }),
                TernaryFilter::make('is_featured')
                    ->label('Featured Properties')
                    ->placeholder('All properties')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),
                TernaryFilter::make('is_verified')
                    ->label('Verified Properties')
                    ->placeholder('All properties')
                    ->trueLabel('Verified only')
                    ->falseLabel('Not verified'),
            ])
            ->recordActions([


                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
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
                        ->action(fn($record) => $record->update(['status' => PropertyStatus::SOLD->value]))
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status !== PropertyStatus::SOLD->value),

                    Action::make('mark_off_market')
                        ->label('Take Off Market')
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->action(fn($record) => $record->update(['status' => PropertyStatus::OFF_MARKET->value]))
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status !== PropertyStatus::OFF_MARKET->value),
                    DeleteAction::make(),
                ])->label('Status')->icon('heroicon-o-ellipsis-vertical')->color('info'),

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
