<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgencyResource\Pages;
use App\Filament\Resources\AgencyResource\RelationManagers;
use App\Models\Agency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgencyResource extends Resource
{
    protected static ?string $model = Agency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Business Management';

    protected static ?int $navigationSort = 2;

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
                                // Basic Agency Information
                                Forms\Components\Section::make('Agency Information')
                                    ->description('Basic details about the real estate agency')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Forms\Components\Textarea::make('description')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('license_number')
                                            ->label('License Number')
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('license_expiry_date')
                                            ->label('License Expiry'),
                                        Forms\Components\TextInput::make('years_in_business')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0),
                                        Forms\Components\TextInput::make('logo')
                                            ->label('Logo URL')
                                            ->url()
                                            ->maxLength(255),
                                    ])->columns(4)->collapsible(),

                                // Contact Information
                                Forms\Components\Section::make('Contact Details')
                                    ->description('How to reach this agency')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('phone')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('website')
                                                    ->url()
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Textarea::make('social_media')
                                            ->label('Social Media Links')
                                            ->placeholder('Enter social media URLs (one per line)')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Location Information
                                Forms\Components\Section::make('Location Details')
                                    ->description('Agency location and service areas')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('state_id')
                                                    ->label('State')
                                                    ->relationship('state', 'name')
                                                    ->required()
                                                    ->reactive(),
                                                Forms\Components\Select::make('city_id')
                                                    ->label('City')
                                                    ->relationship('city', 'name')
                                                    ->required()
                                                    ->reactive(),
                                                Forms\Components\Select::make('area_id')
                                                    ->label('Area')
                                                    ->relationship('area', 'name'),
                                            ]),
                                        Forms\Components\Textarea::make('address')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Forms\Components\TagsInput::make('service_areas')
                                            ->label('Service Areas')
                                            ->placeholder('e.g., Lagos Island, Victoria Island')
                                            ->separator(',')
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
                                // Business Profile - Sidebar
                                Forms\Components\Section::make('Business Profile')
                                    ->description('Business specializations and services')
                                    ->schema([
                                        Forms\Components\TagsInput::make('specializations')
                                            ->label('Specializations')
                                            ->placeholder('e.g., Residential, Commercial, Luxury')
                                            ->separator(','),
                                        Forms\Components\TagsInput::make('services')
                                            ->label('Services Offered')
                                            ->placeholder('e.g., Sales, Rentals, Property Management')
                                            ->separator(','),
                                        Forms\Components\TextInput::make('commission_rate')
                                            ->label('Commission Rate (%)')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('%'),
                                    ])->columns(1)->collapsible(),

                                // Performance Metrics - Sidebar
                                Forms\Components\Section::make('Performance Metrics')
                                    ->description('Agency performance and statistics')
                                    ->schema([
                                        Forms\Components\Fieldset::make('Property Statistics')
                                            ->schema([
                                                Forms\Components\TextInput::make('total_properties')
                                                    ->label('Total Properties')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make('active_listings')
                                                    ->label('Active Listings')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make('total_agents')
                                                    ->label('Total Agents')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                            ])->columns(1),
                                        Forms\Components\Fieldset::make('Reviews & Rating')
                                            ->schema([
                                                Forms\Components\TextInput::make('rating')
                                                    ->label('Average Rating')
                                                    ->numeric()
                                                    ->step(0.1)
                                                    ->minValue(0)
                                                    ->maxValue(5)
                                                    ->default(0.00)
                                                    ->disabled()
                                                    ->suffix('/5'),
                                                Forms\Components\TextInput::make('total_reviews')
                                                    ->label('Total Reviews')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                            ])->columns(1),
                                    ])->collapsible(),

                                // Status & Verification - Sidebar
                                Forms\Components\Section::make('Status & Verification')
                                    ->description('Agency status and verification settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                        Forms\Components\Toggle::make('is_verified')
                                            ->label('Verified')
                                            ->default(false),
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured')
                                            ->default(false),
                                        Forms\Components\Toggle::make('accepts_new_properties')
                                            ->label('Accepting New Properties')
                                            ->default(true),
                                        Forms\Components\DateTimePicker::make('verified_at')
                                            ->label('Verified At')
                                            ->disabled(),
                                        Forms\Components\DateTimePicker::make('featured_until')
                                            ->label('Featured Until'),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('City')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('area.name')
                    ->label('Area')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('years_in_business')
                    ->label('Years in Business')
                    ->numeric()
                    ->sortable()
                    ->suffix(' years'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->suffix('/5')
                    ->color(fn($state) => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 4.0 => 'warning',
                        $state >= 3.0 => 'gray',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('total_properties')
                    ->label('Properties')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_agents')
                    ->label('Agents')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_reviews')
                    ->label('Reviews')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('is_verified')
                    ->badge()
                    ->label('Verified')
                    ->colors([
                        'success' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Unverified'),

                Tables\Columns\TextColumn::make('is_featured')
                    ->badge()
                    ->label('Featured')
                    ->colors([
                        'warning' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Featured' : 'Standard')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),

                Tables\Columns\TextColumn::make('license_number')
                    ->label('License')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('license_expiry_date')
                    ->label('License Expiry')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn($record) => $record->website)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Area')
                    ->relationship('area', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->trueLabel('Verified Only')
                    ->falseLabel('Unverified Only')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Status')
                    ->trueLabel('Featured Only')
                    ->falseLabel('Non-Featured Only')
                    ->native(false),

                Tables\Filters\Filter::make('years_in_business')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('years_from')
                                    ->numeric()
                                    ->label('Years from')
                                    ->placeholder('Min years'),
                                Forms\Components\TextInput::make('years_to')
                                    ->numeric()
                                    ->label('Years to')
                                    ->placeholder('Max years'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['years_from'],
                                fn(Builder $query, $years): Builder => $query->where('years_in_business', '>=', $years),
                            )
                            ->when(
                                $data['years_to'],
                                fn(Builder $query, $years): Builder => $query->where('years_in_business', '<=', $years),
                            );
                    }),

                Tables\Filters\Filter::make('rating_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('rating_from')
                                    ->numeric()
                                    ->step(0.1)
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->label('Rating from')
                                    ->placeholder('Min rating'),
                                Forms\Components\TextInput::make('rating_to')
                                    ->numeric()
                                    ->step(0.1)
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->label('Rating to')
                                    ->placeholder('Max rating'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['rating_from'],
                                fn(Builder $query, $rating): Builder => $query->where('rating', '>=', $rating),
                            )
                            ->when(
                                $data['rating_to'],
                                fn(Builder $query, $rating): Builder => $query->where('rating', '<=', $rating),
                            );
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created until'),
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
                    ->label('High Performance Agencies')
                    ->query(fn(Builder $query): Builder => $query->where('rating', '>=', 4.0)->where('total_properties', '>=', 50))
                    ->toggle(),
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
            RelationManagers\AgentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgencies::route('/'),
            'create' => Pages\CreateAgency::route('/create'),
            'edit' => Pages\EditAgency::route('/{record}/edit'),
        ];
    }
}
