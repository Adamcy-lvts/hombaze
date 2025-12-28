<?php

namespace App\Filament\Landlord\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Landlord\Resources\PropertyResource\Pages\ListProperties;
use App\Filament\Landlord\Resources\PropertyResource\Pages\CreateProperty;
use App\Filament\Landlord\Resources\PropertyResource\Pages\EditProperty;
use App\Filament\Landlord\Resources\SalesAgreementResource;
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
use Filament\Actions\ReplicateAction;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Enums\FontWeight;

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
                Wizard::make([
                    // Step 1: Basic Information
                    Step::make('Basic Info')
                        ->icon('heroicon-o-home')
                        ->description('Property title, type & description')
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 2])
                                ->schema([
                                    TextInput::make('title')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('e.g., Beautiful 3 Bedroom Apartment in Lekki')
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
                                        ->afterStateUpdated(fn(Set $set) => $set('property_subtype_id', null)),

                                    Select::make('property_subtype_id')
                                        ->label('Property Subtype')
                                        ->options(fn(Get $get): array => PropertySubtype::query()
                                            ->where('property_type_id', $get('property_type_id'))
                                            ->pluck('name', 'id')
                                            ->toArray())
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('Select subtype (optional)'),
                                ]),

                            Textarea::make('description')
                                ->required()
                                ->rows(5)
                                ->placeholder('Describe the property in detail. Include key features, condition, and any unique selling points.')
                                ->helperText('A good description helps attract more inquiries')
                                ->columnSpanFull(),
                        ]),

                    // Step 2: Location
                    Step::make('Location')
                        ->icon('heroicon-o-map-pin')
                        ->description('Property address & location')
                        ->schema([
                            Grid::make(['default' => 1, 'sm' => 3])
                                ->schema([
                                    Select::make('state_id')
                                        ->label('State')
                                        ->relationship('state', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->default(fn (): ?int => static::getDefaultStateId())
                                        ->live()
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('city_id', null);
                                            $set('area_id', null);
                                        }),

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
                                                ->schema([
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
                                        ->afterStateUpdated(fn (Set $set) => $set('area_id', null)),

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
                                                ->schema([
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
                                        ->placeholder('Select area (optional)'),
                                ]),

                            Textarea::make('address')
                                ->label('Street Address')
                                ->required()
                                ->rows(2)
                                ->placeholder('Enter the full street address')
                                ->columnSpanFull(),

                            TextInput::make('landmark')
                                ->label('Nearby Landmark')
                                ->maxLength(255)
                                ->placeholder('e.g., Near Shoprite, Close to VGC'),
                        ]),

                    // Step 3: Features & Specifications
                    Step::make('Features')
                        ->icon('heroicon-o-square-3-stack-3d')
                        ->description('Rooms, size & amenities')
                        ->schema([
                            Section::make('Property Specifications')
                                ->description('Physical characteristics of the property')
                                ->schema([
                                    Grid::make(['default' => 1, 'sm' => 2])
                                        ->schema([
                                            TextInput::make('bedrooms')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(20)
                                                ->suffix('beds')
                                                ->required(fn (Get $get): bool => static::isFieldRequired('bedrooms', $get))
                                                ->visible(fn (Get $get): bool => static::isFieldVisible('bedrooms', $get)),

                                            TextInput::make('bathrooms')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(20)
                                                ->suffix('baths')
                                                ->required(fn (Get $get): bool => static::isFieldRequired('bathrooms', $get))
                                                ->visible(fn (Get $get): bool => static::isFieldVisible('bathrooms', $get)),
                                        ]),

                                    Select::make('furnishing_status')
                                        ->label('Furnishing')
                                        ->options([
                                            'unfurnished' => 'Unfurnished',
                                            'semi_furnished' => 'Semi Furnished',
                                            'furnished' => 'Fully Furnished',
                                        ])
                                        ->required(fn (Get $get): bool => static::isFieldRequired('furnishing_status', $get))
                                        ->visible(fn (Get $get): bool => static::isFieldVisible('furnishing_status', $get)),
                                ])->columns(1),

                            Section::make('Amenities & Features')
                                ->description('Select all that apply')
                                ->collapsed()
                                ->schema([
                                    CheckboxList::make('features')
                                        ->label('')
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
                                        ->columns(['default' => 2, 'sm' => 3, 'lg' => 4])
                                        ->gridDirection('row')
                                        ->bulkToggleable()
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // Step 4: Pricing & Settings
                    Step::make('Pricing')
                        ->icon('heroicon-o-currency-dollar')
                        ->description('Price & listing type')
                        ->schema([
                            Section::make('Listing Details')
                                ->schema([
                                    Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])
                                        ->schema([
                                            Select::make('listing_type')
                                                ->label('Listing Type')
                                                ->options([
                                                    'sale' => 'For Sale',
                                                    'rent' => 'For Rent',
                                                    'lease' => 'For Lease',
                                                    'shortlet' => 'Shortlet',
                                                ])
                                                ->required()
                                                ->default(fn (): string => static::getDefaultListingType())
                                                ->live()
                                                ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                                    $options = static::getStatusOptionsForListingType($state);
                                                    if (!array_key_exists($get('status'), $options)) {
                                                        $set('status', array_key_first($options));
                                                    }
                                                    $set('price_period', static::getDefaultPricePeriod($state));
                                                }),

                                            TextInput::make('price')
                                                ->label('Price')
                                                ->numeric()
                                                ->prefix('₦')
                                                ->required()
                                                ->placeholder('e.g., 5000000'),

                                            Select::make('price_period')
                                                ->label('Price Period')
                                                ->options([
                                                    'per_month' => 'Per Month',
                                                    'per_year' => 'Per Year',
                                                    'per_night' => 'Per Night',
                                                    'total' => 'Total',
                                                ])
                                                ->default(fn (Get $get): string => static::getDefaultPricePeriod($get('listing_type')))
                                                ->visible(fn (Get $get): bool => in_array($get('listing_type'), ['rent', 'lease', 'shortlet'])),

                                            Select::make('status')
                                                ->label('Status')
                                                ->options(fn (Get $get): array => static::getStatusOptionsForListingType($get('listing_type')))
                                                ->required()
                                                ->default('available'),
                                        ]),
                                ]),

                            Section::make('Settings')
                                ->collapsed()
                                ->schema([
                                    Placeholder::make('owner_info')
                                        ->label('Property Owner')
                                        ->content('You are the owner of this property'),

                                    Toggle::make('is_published')
                                        ->label('Published Listing')
                                        ->helperText('Make this property visible to the public')
                                        ->default(true),
                                ]),
                        ]),

                    // Step 5: Media
                    Step::make('Media')
                        ->icon('heroicon-o-photo')
                        ->description('Photos & images')
                        ->schema([
                            Section::make('Featured Image')
                                ->description('The main image that represents this property')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('featured_image')
                                        ->label('')
                                        ->collection('featured')
                                        ->image()
                                        ->imageEditor()
                                        ->required()
                                        ->acceptedFileTypes(getOptimalImageResolution()['formats'])
                                        ->maxSize(getOptimalImageResolution()['max_file_size'])
                                        ->rules([
                                            new OptimalImageResolution(false)
                                        ])
                                        ->validationMessages([
                                            'required' => 'A featured image is required to showcase your property.',
                                        ])
                                        ->helperText('Upload a high-quality image. ' . getOptimalImageResolution()['quality_note'])
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Gallery Images')
                                ->description('Additional photos to showcase the property')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('gallery_images')
                                        ->label('')
                                        ->collection('gallery')
                                        ->image()
                                        ->multiple()
                                        ->reorderable()
                                        ->acceptedFileTypes(getOptimalImageResolution()['formats'])
                                        ->maxFiles(20)
                                        ->maxSize(getOptimalImageResolution()['max_file_size'])
                                        ->minFiles(1)
                                        ->rules([
                                            new OptimalImageResolution(true)
                                        ])
                                        ->validationMessages([
                                            'min' => 'Please add at least one gallery image.',
                                        ])
                                        ->helperText('Add multiple images from different angles. Drag to reorder.')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString()
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                            size="lg"
                            wire:loading.attr="disabled"
                        >
                            Create Property
                        </x-filament::button>
                    BLADE)))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('')
                    ->collection('featured')
                    ->circular()
                    ->defaultImageUrl('/images/property-placeholder.svg'),

                TextColumn::make('title')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->limit(30)
                    ->description(fn ($record) => $record->area?->name . ', ' . $record->city?->name),

                TextColumn::make('propertyType.name')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bedrooms')
                    ->label('Beds')
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('bathrooms')
                    ->label('Baths')
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn($state) => formatNaira($state ?? 0))
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('listing_type')
                    ->label('Listing')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'info',
                        'rent' => 'warning',
                        'lease' => 'primary',
                        'shortlet' => 'success',
                        default => 'gray',
                    }),

                SelectColumn::make('status')
                    ->options(fn (Property $record): array => static::getStatusOptionsForListingType($record->listing_type))
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
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

                Action::make('sales_agreement')
                    ->label(fn ($record) => $record->salesAgreement ? 'View Sales Agreement' : 'Create Sales Agreement')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn ($record) => $record->listing_type === 'sale' && $record->status === PropertyStatus::SOLD->value)
                    ->url(function ($record): string {
                        return $record->salesAgreement
                            ? SalesAgreementResource::getUrl('view', ['record' => $record->salesAgreement])
                            : SalesAgreementResource::getUrl('create', ['property' => $record->id]);
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

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->form([
                            Select::make('status')
                                ->options(function (BulkAction $action): array {
                                    return static::getStatusOptionsForRecords($action->getSelectedRecords());
                                })
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['status' => $data['status']]);
                        }),
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

    private static function getDefaultListingType(): string
    {
        $user = auth()->user();
        $preferred = $user?->preferences['default_listing_type'] ?? null;

        return in_array($preferred, ['sale', 'rent', 'lease', 'shortlet'], true) ? $preferred : 'rent';
    }

    private static function getDefaultPricePeriod(?string $listingType): string
    {
        return in_array($listingType, ['rent', 'lease', 'shortlet'], true) ? 'per_month' : 'total';
    }

    private static function getDefaultStateId(): ?int
    {
        $ownerProfile = auth()->user()?->propertyOwnerProfile;

        return $ownerProfile?->state_id ?? auth()->user()?->profile?->state_id;
    }

    private static function getDefaultCityId(?int $stateId): ?int
    {
        $ownerProfile = auth()->user()?->propertyOwnerProfile;
        $cityId = $ownerProfile?->city_id ?? auth()->user()?->profile?->city_id;

        return $cityId;
    }

    private static function getDefaultAreaId(?int $cityId): ?int
    {
        $ownerProfile = auth()->user()?->propertyOwnerProfile;
        $areaId = $ownerProfile?->area_id ?? auth()->user()?->profile?->area_id;

        return $areaId;
    }
}
