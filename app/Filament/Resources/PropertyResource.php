<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ReplicateAction;
use App\Filament\Resources\PropertyResource\Pages\ListProperties;
use App\Filament\Resources\PropertyResource\Pages\CreateProperty;
use App\Filament\Resources\PropertyResource\Pages\EditProperty;
use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertyFeature;
use App\Models\PropertySubtype;
use App\Rules\OptimalImageResolution;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static string | \UnitEnum | null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Basics')
                        ->icon('heroicon-o-home')
                        ->schema([
                            Section::make('Property Information')
                                ->description('Basic details about the property')
                                ->schema([
                                    Grid::make([
                                        'default' => 1,
                                        'sm' => 2,
                                        'lg' => 4,
                                    ])
                                        ->schema([
                                            TextInput::make('title')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 2,
                                                    'lg' => 2,
                                                ]),
                                            TextInput::make('slug')
                                                ->required()
                                                ->maxLength(255)
                                                ->disabled()
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 2,
                                                    'lg' => 2,
                                                ]),
                                            Select::make('property_type_id')
                                                ->label('Property Type')
                                                ->relationship('propertyType', 'name')
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('property_subtype_id', null))
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            Select::make('property_subtype_id')
                                                ->label('Property Subtype')
                                                ->options(fn (Get $get): array => PropertySubtype::query()
                                                    ->where('property_type_id', $get('property_type_id'))
                                                    ->pluck('name', 'id')
                                                    ->toArray())
                                                ->required()
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            Select::make('listing_type')
                                                ->required()
                                                ->options([
                                                    'sale' => 'For Sale',
                                                    'rent' => 'For Rent',
                                                    'lease' => 'For Lease',
                                                    'shortlet' => 'Shortlet'
                                                ])
                                                ->default(fn (): string => static::getDefaultListingType())
                                                ->live()
                                                ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                                    $options = static::getStatusOptionsForListingType($state);
                                                    if (!array_key_exists($get('status'), $options)) {
                                                        $set('status', array_key_first($options));
                                                    }
                                                    $set('price_period', static::getDefaultPricePeriod($state));
                                                })
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            Select::make('status')
                                                ->required()
                                                ->options(fn (Get $get): array => static::getStatusOptionsForListingType($get('listing_type')))
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                        ]),
                                    Textarea::make('description')
                                        ->required()
                                        ->rows(4)
                                        ->afterStateUpdated(function (Get $get, $livewire): void {
                                            if (!static::shouldAutoAdvanceBasics($get)) {
                                                return;
                                            }
                                            $livewire->dispatch('next-wizard-step', key: static::getWizardKey());
                                        })
                                        ->columnSpanFull(),
                                ])->collapsible(),
                        ]),
                    Step::make('Location')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Section::make('Location Details')
                                ->description('Property location and address')
                                ->schema([
                                    Grid::make([
                                        'default' => 1,
                                        'sm' => 2,
                                        'lg' => 3,
                                    ])
                                        ->schema([
                                            Select::make('state_id')
                                                ->label('State')
                                                ->relationship('state', 'name')
                                                ->required()
                                                ->default(fn (): ?int => static::getDefaultStateId())
                                                ->searchable()
                                                ->preload()
                                                ->live()
                                                ->afterStateUpdated(function (Set $set): void {
                                                    $set('city_id', null);
                                                    $set('area_id', null);
                                                })
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            Select::make('city_id')
                                                ->label('City')
                                                ->options(fn (Get $get): array => City::query()
                                                    ->where('state_id', $get('state_id'))
                                                    ->pluck('name', 'id')
                                                    ->toArray())
                                                ->searchable()
                                                ->preload()
                                                ->suffixAction(
                                                    Action::make('add_city')
                                                        ->icon('heroicon-m-plus')
                                                        ->tooltip('Add city')
                                                        ->modalHeading('Add city')
                                                        ->form([
                                                            TextInput::make('name')
                                                                ->label('City name')
                                                                ->required()
                                                                ->maxLength(255),
                                                        ])
                                                        ->action(function (array $data, Set $set, Get $get): void {
                                                            $stateId = $get('state_id');
                                                            if (!$stateId) {
                                                                return;
                                                            }

                                                            $city = City::create([
                                                                'name' => $data['name'],
                                                                'slug' => Str::slug($data['name']),
                                                                'state_id' => $stateId,
                                                                'is_active' => true,
                                                            ]);

                                                            $set('city_id', $city->id);
                                                            $set('area_id', null);
                                                        })
                                                        ->disabled(fn (Get $get): bool => blank($get('state_id')))
                                                )
                                                ->required()
                                                ->default(fn (Get $get): ?int => static::getDefaultCityId($get('state_id')))
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('area_id', null))
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            Select::make('area_id')
                                                ->label('Area')
                                                ->options(fn (Get $get): array => Area::query()
                                                    ->where('city_id', $get('city_id'))
                                                    ->pluck('name', 'id')
                                                    ->toArray())
                                                ->searchable()
                                                ->preload()
                                                ->suffixAction(
                                                    Action::make('add_area')
                                                        ->icon('heroicon-m-plus')
                                                        ->tooltip('Add area')
                                                        ->modalHeading('Add area')
                                                        ->form([
                                                            TextInput::make('name')
                                                                ->label('Area name')
                                                                ->required()
                                                                ->maxLength(255),
                                                        ])
                                                        ->action(function (array $data, Set $set, Get $get): void {
                                                            $cityId = $get('city_id');
                                                            if (!$cityId) {
                                                                return;
                                                            }

                                                            $area = Area::create([
                                                                'name' => $data['name'],
                                                                'slug' => Str::slug($data['name']),
                                                                'city_id' => $cityId,
                                                                'is_active' => true,
                                                            ]);

                                                            $set('area_id', $area->id);
                                                        })
                                                        ->disabled(fn (Get $get): bool => blank($get('city_id')))
                                                )
                                                ->default(fn (Get $get): ?int => static::getDefaultAreaId($get('city_id')))
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 2,
                                                    'lg' => 1,
                                                ]),
                                        ]),
                                    Grid::make([
                                        'default' => 1,
                                        'sm' => 2,
                                        'lg' => 3,
                                    ])
                                        ->schema([
                                            TextInput::make('landmark')
                                                ->maxLength(255)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 2,
                                                    'lg' => 1,
                                                ]),
                                            TextInput::make('latitude')
                                                ->numeric()
                                                ->step(0.000001)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            TextInput::make('longitude')
                                                ->numeric()
                                                ->step(0.000001)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                        ]),
                                    Textarea::make('address')
                                        ->required()
                                        ->rows(3)
                                        ->afterStateUpdated(function (Get $get, $livewire): void {
                                            if (!static::shouldAutoAdvanceLocation($get)) {
                                                return;
                                            }
                                            $livewire->dispatch('next-wizard-step', key: static::getWizardKey());
                                        })
                                        ->columnSpanFull(),
                                ])->collapsible(),
                        ]),
                    Step::make('Details')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->schema([
                            Section::make('Property Features')
                                ->description('Physical characteristics of the property')
                                ->schema([
                                    Grid::make([
                                        'default' => 1,
                                        'sm' => 2,
                                        'lg' => 4,
                                    ])
                                        ->schema([
                                            TextInput::make('bedrooms')
                                                ->required(fn (Get $get): bool => static::isFieldRequired('bedrooms', $get))
                                                ->visible(fn (Get $get): bool => static::isFieldVisible('bedrooms', $get))
                                                ->numeric()
                                                ->minValue(0)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            TextInput::make('bathrooms')
                                                ->required(fn (Get $get): bool => static::isFieldRequired('bathrooms', $get))
                                                ->visible(fn (Get $get): bool => static::isFieldVisible('bathrooms', $get))
                                                ->numeric()
                                                ->minValue(0)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            TextInput::make('toilets')
                                                ->visible(fn (Get $get): bool => static::isFieldVisible('toilets', $get))
                                                ->numeric()
                                                ->minValue(0)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            TextInput::make('parking_spaces')
                                                ->visible(fn (Get $get): bool => static::isFieldVisible('parking_spaces', $get))
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                        ]),
                                    Grid::make([
                                        'default' => 1,
                                        'sm' => 2,
                                        'lg' => 3,
                                    ])
                                        ->schema([
                                            TextInput::make('size_sqm')
                                                ->label('Size (Sqm)')
                                                ->numeric()
                                                ->suffix('sqm')
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            TextInput::make('year_built')
                                                ->numeric()
                                                ->minValue(1900)
                                                ->maxValue(date('Y'))
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                    'lg' => 1,
                                                ]),
                                            Select::make('furnishing_status')
                                                ->required(fn (Get $get): bool => static::isFieldRequired('furnishing_status', $get))
                                                ->visible(fn (Get $get): bool => static::isFieldVisible('furnishing_status', $get))
                                                ->options([
                                                    'furnished' => 'Fully Furnished',
                                                    'semi_furnished' => 'Semi Furnished',
                                                    'unfurnished' => 'Unfurnished'
                                                ])
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 2,
                                                    'lg' => 1,
                                                ]),
                                            Select::make('compound_type')
                                                ->label('Compound/Estate Type')
                                                ->options(Property::getCompoundTypeOptions())
                                                ->searchable()
                                                ->placeholder('Select compound type...')
                                                ->helperText('Specify if the property is in a compound, estate, or standalone')
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 2,
                                                    'lg' => 1,
                                                ]),
                                        ]),
                                ])->collapsible(),
                            Section::make('Property Features & Amenities')
                                ->description('Select features and amenities available in this property')
                                ->schema([
                                    CheckboxList::make('features')
                                        ->relationship('features', 'name')
                                        ->options(function () {
                                            return PropertyFeature::active()
                                                ->ordered()
                                                ->get()
                                                ->mapWithKeys(function ($feature) {
                                                    $categoryLabel = match($feature->category) {
                                                        'interior' => '🏠',
                                                        'exterior' => '🌳', 
                                                        'amenities' => '🏊',
                                                        'utilities' => '⚡',
                                                        'security' => '🔒',
                                                        'accessibility' => '♿',
                                                        default => '📋'
                                                    };
                                                    return [$feature->id => $categoryLabel . ' ' . $feature->name];
                                                })
                                                ->toArray();
                                        })
                                        ->descriptions(function () {
                                            return PropertyFeature::active()
                                                ->ordered()
                                                ->whereNotNull('description')
                                                ->pluck('description', 'id')
                                                ->toArray();
                                        })
                                        ->columns([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 3,
                                        ])
                                        ->gridDirection('row')
                                        ->bulkToggleable()
                                        ->searchable()
                                        ->columnSpanFull()
                                        ->helperText('Select all features and amenities that apply to this property. Icons indicate categories: 🏠 Interior, 🌳 Exterior, 🏊 Amenities, ⚡ Utilities, 🔒 Security'),
                                ])->collapsible(),
                        ]),
                    Step::make('Media & Publish')
                        ->icon('heroicon-o-photo')
                        ->schema([
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
                                        ->acceptedFileTypes(getOptimalImageResolution()['formats'])
                                        ->customProperties([
                                            'caption' => null,
                                            'alt_text' => null,
                                        ])
                                        ->maxFiles(function (\Filament\Schemas\Components\Utilities\Get $get) {
                                            $propertyTypeId = $get('property_type_id');
                                            if ($propertyTypeId) {
                                                $propertyType = \App\Models\PropertyType::find($propertyTypeId);
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
                                        ->helperText(function (\Filament\Schemas\Components\Utilities\Get $get) {
                                            $propertyTypeId = $get('property_type_id');
                                            $resolutionInfo = getOptimalImageResolution();
                                            if ($propertyTypeId) {
                                                $propertyType = \App\Models\PropertyType::find($propertyTypeId);
                                                if ($propertyType) {
                                                    return getPropertyImageConfig($propertyType->slug)['gallery_helper_text'] . ' ' . $resolutionInfo['quality_note'];
                                                }
                                            }
                                            return getPropertyImageConfig()['gallery_helper_text'] . ' ' . $resolutionInfo['quality_note'];
                                        })
                                        ->columnSpanFull(),
                                ])->collapsible(),
                            Section::make('Media & SEO')
                                ->description('Media links and SEO optimization')
                                ->collapsed()
                                ->schema([
                                    Grid::make([
                                        'default' => 1,
                                        'sm' => 2,
                                    ])
                                        ->schema([
                                            TextInput::make('video_url')
                                                ->label('Video URL')
                                                ->url()
                                                ->maxLength(255)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                ]),
                                            TextInput::make('virtual_tour_url')
                                                ->label('Virtual Tour URL')
                                                ->url()
                                                ->maxLength(255)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                ]),
                                        ]),
                                    Grid::make([
                                        'default' => 1,
                                        'sm' => 2,
                                    ])
                                        ->schema([
                                            TextInput::make('meta_title')
                                                ->maxLength(255)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                ]),
                                            TextInput::make('meta_keywords')
                                                ->maxLength(255)
                                                ->columnSpan([
                                                    'default' => 1,
                                                    'sm' => 1,
                                                ]),
                                        ]),
                                    Textarea::make('meta_description')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])->collapsible(),
                            Section::make('Pricing Details')
                                ->description('Property pricing and fees')
                                ->schema([
                                    TextInput::make('price')
                                        ->required()
                                        ->numeric()
                                        ->prefix('₦')
                                        ->formatStateUsing(fn($state) => number_format($state, 2)),
                                    Select::make('price_period')
                                        ->options([
                                            'monthly' => 'Monthly',
                                            'yearly' => 'Yearly',
                                            'one-time' => 'One-time',
                                            'daily' => 'Daily'
                                        ])
                                        ->default(fn (Get $get): string => static::getDefaultPricePeriod($get('listing_type'))),
                                    TextInput::make('service_charge')
                                        ->numeric()
                                        ->prefix('₦'),
                                    TextInput::make('legal_fee')
                                        ->numeric()
                                        ->prefix('₦'),
                                    TextInput::make('agency_fee')
                                        ->numeric()
                                        ->prefix('₦'),
                                    TextInput::make('caution_deposit')
                                        ->numeric()
                                        ->prefix('₦'),
                                ])->columns(1)->collapsible(),
                            Section::make('Ownership & Management')
                                ->description('Property ownership and agent details')
                                ->schema([
                                    Select::make('owner_id')
                                        ->label('Property Owner')
                                        ->relationship('owner', 'name')
                                        ->required()
                                        ->searchable(),
                                    Select::make('agent_id')
                                        ->label('Assigned Agent')
                                        ->relationship('agent', 'name')
                                        ->searchable(),
                                    Select::make('agency_id')
                                        ->label('Managing Agency')
                                        ->relationship('agency', 'name')
                                        ->searchable(),
                                ])->columns(1)->collapsible(),
                            Section::make('Status & Publishing')
                                ->description('Property status and visibility settings')
                                ->schema([
                                    Toggle::make('is_featured')
                                        ->label('Featured Property')
                                        ->default(false),
                                    Toggle::make('is_verified')
                                        ->label('Verified Property')
                                        ->default(false),
                                    Toggle::make('allow_inquiries')
                                        ->label('Allow Inquiries')
                                        ->default(true),
                                    Toggle::make('show_phone')
                                        ->label('Show Phone Number')
                                        ->default(true),
                                    DateTimePicker::make('featured_until')
                                        ->label('Featured Until'),
                                    DateTimePicker::make('verified_at')
                                        ->label('Verified At')
                                        ->disabled(),
                                ])->columns(1)->collapsible(),
                            Section::make('Analytics')
                                ->description('Property performance metrics')
                                ->schema([
                                    TextInput::make('views_count')
                                        ->label('Total Views')
                                        ->numeric()
                                        ->default(0)
                                        ->disabled(),
                                    TextInput::make('inquiries_count')
                                        ->label('Total Inquiries')
                                        ->numeric()
                                        ->default(0)
                                        ->disabled(),
                                    TextInput::make('favorites_count')
                                        ->label('Times Favorited')
                                        ->numeric()
                                        ->default(0)
                                        ->disabled(),
                                    DateTimePicker::make('last_viewed_at')
                                        ->label('Last Viewed')
                                        ->disabled(),
                                ])->columns(1)->collapsible(),
                        ]),
                ])
                    ->key(static::getWizardKey()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('listing_type')
                    ->badge()
                    ->colors([
                        'success' => 'rent',
                        'warning' => 'sale',
                        'info' => 'shortlet',
                        'primary' => 'lease',
                    ])
                    ->sortable(),
                SelectColumn::make('status')
                    ->options(fn (Property $record): array => static::getStatusOptionsForListingType($record->listing_type))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->formatStateUsing(fn($state) => formatNaira($state ?? 0))
                    ->sortable(),
                TextColumn::make('bedrooms')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('bathrooms')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('propertyType.name')
                    ->label('Type')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('city.name')
                    ->label('City')
                    ->sortable(),
                TextColumn::make('area.name')
                    ->label('Area')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('agency.name')
                    ->label('Agency')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('features.name')
                    ->label('Features')
                    ->badge()
                    ->separator(', ')
                    ->limit(3)
                    ->limitedRemainingText()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->toggleable(),
                IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Verified')
                    ->toggleable(),
                TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('inquiries_count')
                    ->label('Inquiries')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('listing_type')
                    ->options([
                        'sale' => 'For Sale',
                        'rent' => 'For Rent',
                        'lease' => 'For Lease',
                        'shortlet' => 'Shortlet',
                    ]),
                SelectFilter::make('status')
                    ->options(PropertyStatus::options()),
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
                SelectFilter::make('agent_id')
                    ->relationship('agent', 'name')
                    ->label('Agent')
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('agency_id')
                    ->relationship('agency', 'name')
                    ->label('Agency')
                    ->multiple()
                    ->searchable(),
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
                                5 => '5',
                                10 => '10+',
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
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Listed from'),
                        DatePicker::make('created_until')
                            ->label('Listed until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Filter::make('high_performance')
                    ->query(fn(Builder $query): Builder => $query->where('views_count', '>', 100))
                    ->label('High Performance (100+ views)'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['title'] = $data['title'] . ' (Copy)';
                        $data['slug'] = null;
                        $data['status'] = PropertyStatus::OFF_MARKET->value;
                        $data['is_published'] = false;
                        $data['published_at'] = null;
                        $data['is_featured'] = false;
                        $data['is_verified'] = false;
                        $data['view_count'] = 0;
                        $data['inquiry_count'] = 0;
                        $data['favorite_count'] = 0;
                        return $data;
                    })
                    ->successRedirectUrl(fn (Property $replica): string => static::getUrl('edit', ['record' => $replica]))
                    ->after(function (Property $record, Property $replica): void {
                        $replica->features()->sync($record->features->pluck('id'));

                        foreach (['featured', 'gallery'] as $collection) {
                            $record->getMedia($collection)->each(
                                fn ($media) => $media->copy($replica, $collection)
                            );
                        }
                    }),

                ActionGroup::make([
                    Action::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->schema([
                            Select::make('status')
                                ->options(fn (Property $record): array => static::getStatusOptionsForListingType($record->listing_type))
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

    private static function getStatusOptionsForListingType(?string $listingType): array
    {
        $options = PropertyStatus::options();

        return match ($listingType) {
            'sale' => array_intersect_key($options, array_flip([
                PropertyStatus::AVAILABLE->value,
                PropertyStatus::UNDER_OFFER->value,
                PropertyStatus::SOLD->value,
                PropertyStatus::OFF_MARKET->value,
                PropertyStatus::WITHDRAWN->value,
            ])),
            'rent', 'lease', 'shortlet' => array_intersect_key($options, array_flip([
                PropertyStatus::AVAILABLE->value,
                PropertyStatus::RENTED->value,
                PropertyStatus::OFF_MARKET->value,
                PropertyStatus::WITHDRAWN->value,
            ])),
            default => $options,
        };
    }

    private static function getStatusOptionsForRecords(iterable $records): array
    {
        $options = PropertyStatus::options();
        $statusOptions = null;

        foreach ($records as $record) {
            $listingOptions = static::getStatusOptionsForListingType($record->listing_type ?? null);
            $statusOptions = $statusOptions === null
                ? $listingOptions
                : array_intersect_key($statusOptions, $listingOptions);
        }

        return $statusOptions ?? $options;
    }

    private static function getWizardKey(): string
    {
        return 'property-wizard';
    }

    private static function shouldAutoAdvanceBasics(Get $get): bool
    {
        return filled($get('title'))
            && filled($get('property_type_id'))
            && filled($get('property_subtype_id'))
            && filled($get('listing_type'))
            && filled($get('status'))
            && filled($get('description'));
    }

    private static function shouldAutoAdvanceLocation(Get $get): bool
    {
        return filled($get('state_id'))
            && filled($get('city_id'))
            && filled($get('address'));
    }

    private static function getDefaultListingType(): string
    {
        $user = auth()->user();
        $preferred = $user?->preferences['default_listing_type'] ?? null;

        return in_array($preferred, ['sale', 'rent', 'lease', 'shortlet'], true) ? $preferred : 'rent';
    }

    private static function getDefaultPricePeriod(?string $listingType): string
    {
        return in_array($listingType, ['rent', 'lease', 'shortlet'], true) ? 'monthly' : 'one-time';
    }

    private static function getDefaultStateId(): ?int
    {
        return auth()->user()?->profile?->state_id;
    }

    private static function getDefaultCityId(?int $stateId): ?int
    {
        return auth()->user()?->profile?->city_id;
    }

    private static function getDefaultAreaId(?int $cityId): ?int
    {
        return auth()->user()?->profile?->area_id;
    }
}
