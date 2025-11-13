<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AgencyResource\RelationManagers\AgentsRelationManager;
use App\Filament\Resources\AgencyResource\Pages\ListAgencies;
use App\Filament\Resources\AgencyResource\Pages\CreateAgency;
use App\Filament\Resources\AgencyResource\Pages\EditAgency;
use App\Filament\Resources\AgencyResource\Pages;
use App\Filament\Resources\AgencyResource\RelationManagers;
use App\Models\Agency;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgencyResource extends Resource
{
    protected static ?string $model = Agency::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'Business Management';

    protected static ?int $navigationSort = 2;

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
                                // Basic Agency Information
                                Section::make('Agency Information')
                                    ->description('Basic details about the real estate agency')
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Textarea::make('description')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('license_number')
                                            ->label('License Number')
                                            ->maxLength(255),
                                        DatePicker::make('license_expiry_date')
                                            ->label('License Expiry'),
                                        TextInput::make('years_in_business')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0),
                                        TextInput::make('logo')
                                            ->label('Logo URL')
                                            ->url()
                                            ->maxLength(255),
                                    ])->columns(4)->collapsible(),

                                // Contact Information
                                Section::make('Contact Details')
                                    ->description('How to reach this agency')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('phone')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('website')
                                                    ->url()
                                                    ->maxLength(255),
                                            ]),
                                        Textarea::make('social_media')
                                            ->label('Social Media Links')
                                            ->placeholder('Enter social media URLs (one per line)')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Location Information
                                Section::make('Location Details')
                                    ->description('Agency location and service areas')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('state_id')
                                                    ->label('State')
                                                    ->relationship('state', 'name')
                                                    ->required()
                                                    ->reactive(),
                                                Select::make('city_id')
                                                    ->label('City')
                                                    ->relationship('city', 'name')
                                                    ->required()
                                                    ->reactive(),
                                                Select::make('area_id')
                                                    ->label('Area')
                                                    ->relationship('area', 'name'),
                                            ]),
                                        Textarea::make('address')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TagsInput::make('service_areas')
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
                        Group::make()
                            ->schema([
                                // Business Profile - Sidebar
                                Section::make('Business Profile')
                                    ->description('Business specializations and services')
                                    ->schema([
                                        TagsInput::make('specializations')
                                            ->label('Specializations')
                                            ->placeholder('e.g., Residential, Commercial, Luxury')
                                            ->separator(','),
                                        TagsInput::make('services')
                                            ->label('Services Offered')
                                            ->placeholder('e.g., Sales, Rentals, Property Management')
                                            ->separator(','),
                                        TextInput::make('commission_rate')
                                            ->label('Commission Rate (%)')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('%'),
                                    ])->columns(1)->collapsible(),

                                // Performance Metrics - Sidebar
                                Section::make('Performance Metrics')
                                    ->description('Agency performance and statistics')
                                    ->schema([
                                        Fieldset::make('Property Statistics')
                                            ->schema([
                                                TextInput::make('total_properties')
                                                    ->label('Total Properties')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                                TextInput::make('active_listings')
                                                    ->label('Active Listings')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                                TextInput::make('total_agents')
                                                    ->label('Total Agents')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                            ])->columns(1),
                                        Fieldset::make('Reviews & Rating')
                                            ->schema([
                                                TextInput::make('rating')
                                                    ->label('Average Rating')
                                                    ->numeric()
                                                    ->step(0.1)
                                                    ->minValue(0)
                                                    ->maxValue(5)
                                                    ->default(0.00)
                                                    ->disabled()
                                                    ->suffix('/5'),
                                                TextInput::make('total_reviews')
                                                    ->label('Total Reviews')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                            ])->columns(1),
                                    ])->collapsible(),

                                // Status & Verification - Sidebar
                                Section::make('Status & Verification')
                                    ->description('Agency status and verification settings')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                        Toggle::make('is_verified')
                                            ->label('Verified')
                                            ->default(false),
                                        Toggle::make('is_featured')
                                            ->label('Featured')
                                            ->default(false),
                                        Toggle::make('accepts_new_properties')
                                            ->label('Accepting New Properties')
                                            ->default(true),
                                        DateTimePicker::make('verified_at')
                                            ->label('Verified At')
                                            ->disabled(),
                                        DateTimePicker::make('featured_until')
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('city.name')
                    ->label('City')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('area.name')
                    ->label('Area')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('years_in_business')
                    ->label('Years in Business')
                    ->numeric()
                    ->sortable()
                    ->suffix(' years'),

                TextColumn::make('rating')
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

                TextColumn::make('total_properties')
                    ->label('Properties')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_agents')
                    ->label('Agents')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_reviews')
                    ->label('Reviews')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('is_verified')
                    ->badge()
                    ->label('Verified')
                    ->colors([
                        'success' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Unverified'),

                TextColumn::make('is_featured')
                    ->badge()
                    ->label('Featured')
                    ->colors([
                        'warning' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Featured' : 'Standard')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),

                TextColumn::make('license_number')
                    ->label('License')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('license_expiry_date')
                    ->label('License Expiry')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn($record) => $record->website)
                    ->openUrlInNewTab(),

                TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('area_id')
                    ->label('Area')
                    ->relationship('area', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),

                TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->trueLabel('Verified Only')
                    ->falseLabel('Unverified Only')
                    ->native(false),

                TernaryFilter::make('is_featured')
                    ->label('Featured Status')
                    ->trueLabel('Featured Only')
                    ->falseLabel('Non-Featured Only')
                    ->native(false),

                Filter::make('years_in_business')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('years_from')
                                    ->numeric()
                                    ->label('Years from')
                                    ->placeholder('Min years'),
                                TextInput::make('years_to')
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

                Filter::make('rating_range')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('rating_from')
                                    ->numeric()
                                    ->step(0.1)
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->label('Rating from')
                                    ->placeholder('Min rating'),
                                TextInput::make('rating_to')
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

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Created from'),
                        DatePicker::make('created_until')
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

                Filter::make('high_performance')
                    ->label('High Performance Agencies')
                    ->query(fn(Builder $query): Builder => $query->where('rating', '>=', 4.0)->where('total_properties', '>=', 50))
                    ->toggle(),
            ])
            ->recordActions([
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
            AgentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgencies::route('/'),
            'create' => CreateAgency::route('/create'),
            'edit' => EditAgency::route('/{record}/edit'),
        ];
    }
}
