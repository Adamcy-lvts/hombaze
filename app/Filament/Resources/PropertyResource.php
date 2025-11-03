<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use App\Models\PropertyFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Content Management';

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
                                            'lg' => 4,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 2,
                                                        'lg' => 2,
                                                    ]),
                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->disabled()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 2,
                                                        'lg' => 2,
                                                    ]),
                                                Forms\Components\Select::make('property_type_id')
                                                    ->label('Property Type')
                                                    ->relationship('propertyType', 'name')
                                                    ->required()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\Select::make('property_subtype_id')
                                                    ->label('Property Subtype')
                                                    ->relationship('propertySubtype', 'name')
                                                    ->required()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\Select::make('listing_type')
                                                    ->required()
                                                    ->options([
                                                        'sale' => 'For Sale',
                                                        'rent' => 'For Rent',
                                                        'lease' => 'For Lease',
                                                        'shortlet' => 'Shortlet'
                                                    ])
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\Select::make('status')
                                                    ->required()
                                                    ->options([
                                                        'available' => 'Available',
                                                        'rented' => 'Rented',
                                                        'sold' => 'Sold',
                                                        'pending' => 'Pending',
                                                        'inactive' => 'Inactive'
                                                    ])
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                            ]),
                                        Forms\Components\Textarea::make('description')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Property Features
                                Forms\Components\Section::make('Property Features')
                                    ->description('Physical characteristics of the property')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 4,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('bedrooms')
                                                    ->required(fn (Get $get): bool => static::isFieldRequired('bedrooms', $get))
                                                    ->visible(fn (Get $get): bool => static::isFieldVisible('bedrooms', $get))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('bathrooms')
                                                    ->required(fn (Get $get): bool => static::isFieldRequired('bathrooms', $get))
                                                    ->visible(fn (Get $get): bool => static::isFieldVisible('bathrooms', $get))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('toilets')
                                                    ->visible(fn (Get $get): bool => static::isFieldVisible('toilets', $get))
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('parking_spaces')
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
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 3,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('size_sqm')
                                                    ->label('Size (Sqm)')
                                                    ->numeric()
                                                    ->suffix('sqm')
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('year_built')
                                                    ->numeric()
                                                    ->minValue(1900)
                                                    ->maxValue(date('Y'))
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\Select::make('furnishing_status')
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
                                                Forms\Components\Select::make('compound_type')
                                                    ->label('Compound/Estate Type')
                                                    ->options(\App\Models\Property::getCompoundTypeOptions())
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

                                // Property Features & Amenities
                                Forms\Components\Section::make('Property Features & Amenities')
                                    ->description('Select features and amenities available in this property')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('features')
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

                                // Location Information
                                Forms\Components\Section::make('Location Details')
                                    ->description('Property location and address')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 3,
                                        ])
                                            ->schema([
                                                Forms\Components\Select::make('state_id')
                                                    ->label('State')
                                                    ->relationship('state', 'name')
                                                    ->required()
                                                    ->reactive()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\Select::make('city_id')
                                                    ->label('City')
                                                    ->relationship('city', 'name')
                                                    ->required()
                                                    ->reactive()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\Select::make('area_id')
                                                    ->label('Area')
                                                    ->relationship('area', 'name')
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 2,
                                                        'lg' => 1,
                                                    ]),
                                            ]),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 3,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('landmark')
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 2,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('latitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('longitude')
                                                    ->numeric()
                                                    ->step(0.000001)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                            ]),
                                        Forms\Components\Textarea::make('address')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Media & SEO
                                Forms\Components\Section::make('Media & SEO')
                                    ->description('Media links and SEO optimization')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('video_url')
                                                    ->label('Video URL')
                                                    ->url()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('virtual_tour_url')
                                                    ->label('Virtual Tour URL')
                                                    ->url()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                            ]),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('meta_title')
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('meta_keywords')
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                            ]),
                                        Forms\Components\Textarea::make('meta_description')
                                            ->rows(3)
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
                                // Pricing Details - Sidebar
                                Forms\Components\Section::make('Pricing Details')
                                    ->description('Property pricing and fees')
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('₦')
                                            ->formatStateUsing(fn($state) => number_format($state, 2)),
                                        Forms\Components\Select::make('price_period')
                                            ->options([
                                                'monthly' => 'Monthly',
                                                'yearly' => 'Yearly',
                                                'one-time' => 'One-time',
                                                'daily' => 'Daily'
                                            ]),
                                        Forms\Components\TextInput::make('service_charge')
                                            ->numeric()
                                            ->prefix('₦'),
                                        Forms\Components\TextInput::make('legal_fee')
                                            ->numeric()
                                            ->prefix('₦'),
                                        Forms\Components\TextInput::make('agency_fee')
                                            ->numeric()
                                            ->prefix('₦'),
                                        Forms\Components\TextInput::make('caution_deposit')
                                            ->numeric()
                                            ->prefix('₦'),
                                    ])->columns(1)->collapsible(),

                                // Ownership & Management - Sidebar
                                Forms\Components\Section::make('Ownership & Management')
                                    ->description('Property ownership and agent details')
                                    ->schema([
                                        Forms\Components\Select::make('owner_id')
                                            ->label('Property Owner')
                                            ->relationship('owner', 'name')
                                            ->required()
                                            ->searchable(),
                                        Forms\Components\Select::make('agent_id')
                                            ->label('Assigned Agent')
                                            ->relationship('agent', 'name')
                                            ->searchable(),
                                        Forms\Components\Select::make('agency_id')
                                            ->label('Managing Agency')
                                            ->relationship('agency', 'name')
                                            ->searchable(),
                                    ])->columns(1)->collapsible(),

                                // Status & Publishing - Sidebar
                                Forms\Components\Section::make('Status & Publishing')
                                    ->description('Property status and visibility settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured Property')
                                            ->default(false),
                                        Forms\Components\Toggle::make('is_verified')
                                            ->label('Verified Property')
                                            ->default(false),
                                        Forms\Components\Toggle::make('allow_inquiries')
                                            ->label('Allow Inquiries')
                                            ->default(true),
                                        Forms\Components\Toggle::make('show_phone')
                                            ->label('Show Phone Number')
                                            ->default(true),
                                        Forms\Components\DateTimePicker::make('featured_until')
                                            ->label('Featured Until'),
                                        Forms\Components\DateTimePicker::make('verified_at')
                                            ->label('Verified At')
                                            ->disabled(),
                                    ])->columns(1)->collapsible(),

                                // Analytics - Sidebar
                                Forms\Components\Section::make('Analytics')
                                    ->description('Property performance metrics')
                                    ->schema([
                                        Forms\Components\TextInput::make('views_count')
                                            ->label('Total Views')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled(),
                                        Forms\Components\TextInput::make('inquiries_count')
                                            ->label('Total Inquiries')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled(),
                                        Forms\Components\TextInput::make('favorites_count')
                                            ->label('Times Favorited')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled(),
                                        Forms\Components\DateTimePicker::make('last_viewed_at')
                                            ->label('Last Viewed')
                                            ->disabled(),
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
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
                        'warning' => 'pending',
                        'danger' => ['sold', 'rented'],
                        'gray' => 'inactive',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bedrooms')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('bathrooms')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('propertyType.name')
                    ->label('Type')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('City')
                    ->sortable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->label('Area')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Agency')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('features.name')
                    ->label('Features')
                    ->badge()
                    ->separator(', ')
                    ->limit(3)
                    ->limitedRemainingText()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Verified')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('inquiries_count')
                    ->label('Inquiries')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        'pending' => 'Pending',
                        'inactive' => 'Inactive',
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
                Tables\Filters\SelectFilter::make('agent_id')
                    ->relationship('agent', 'name')
                    ->label('Agent')
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('agency_id')
                    ->relationship('agency', 'name')
                    ->label('Agency')
                    ->multiple()
                    ->searchable(),
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
                                fn(Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn(Builder $query, $price): Builder => $query->where('price', '<=', $price),
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
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Listed from'),
                        Forms\Components\DatePicker::make('created_until')
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
                Tables\Filters\Filter::make('high_performance')
                    ->query(fn(Builder $query): Builder => $query->where('views_count', '>', 100))
                    ->label('High Performance (100+ views)'),
            ])
            ->actions([
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
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
