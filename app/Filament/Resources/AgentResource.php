<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Filament\Resources\AgentResource\RelationManagers;
use App\Models\Agent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main content area (2/3 width) and Sidebar (1/3 width)
                Forms\Components\Grid::make([
                    'default' => 1,
                    'lg' => 4,
                ])
                    ->schema([
                        // Main Content Area (spans 3 columns)
                        Forms\Components\Group::make()
                            ->schema([
                                // Agent Information
                                Forms\Components\Section::make('Agent Information')
                                    ->description('Basic agent details and credentials')
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->label('User Account')
                                            ->relationship('user', 'name')
                                            ->required()
                                            ->searchable()
                                            ->columnSpan(2),
                                        Forms\Components\Select::make('agency_id')
                                            ->label('Agency')
                                            ->relationship('agency', 'name')
                                            ->searchable()
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('license_number')
                                            ->label('License Number')
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('license_expiry_date')
                                            ->label('License Expiry'),
                                        Forms\Components\TextInput::make('years_experience')
                                            ->label('Years of Experience')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0),
                                        Forms\Components\TextInput::make('commission_rate')
                                            ->label('Commission Rate (%)')
                                            ->required()
                                            ->numeric()
                                            ->step(0.1)
                                            ->default(2.50)
                                            ->suffix('%'),
                                        Forms\Components\Textarea::make('bio')
                                            ->label('Biography')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])->columns(4)->collapsible(),

                                // Professional Details
                                Forms\Components\Section::make('Professional Details')
                                    ->description('Specializations and service areas')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TagsInput::make('specializations')
                                                    ->label('Specializations')
                                                    ->placeholder('e.g., Residential, Commercial, Luxury')
                                                    ->separator(','),
                                                Forms\Components\TagsInput::make('languages')
                                                    ->label('Languages Spoken')
                                                    ->placeholder('e.g., English, Yoruba, Hausa')
                                                    ->separator(','),
                                            ]),
                                        Forms\Components\TagsInput::make('service_areas')
                                            ->label('Service Areas')
                                            ->placeholder('e.g., Lagos Island, Victoria Island')
                                            ->separator(',')
                                            ->columnSpanFull(),
                                    ])->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 3,
                            ]),

                        // Sidebar (spans 1 column)
                        Forms\Components\Group::make()
                            ->schema([
                                // Performance Statistics - Sidebar
                                Forms\Components\Section::make('Performance Statistics')
                                    ->description('Agent performance and ratings')
                                    ->schema([
                                        Forms\Components\Fieldset::make('Current Statistics')
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
                                                Forms\Components\TextInput::make('properties_sold')
                                                    ->label('Properties Sold')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make('properties_rented')
                                                    ->label('Properties Rented')
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

                                // Status & Availability - Sidebar
                                Forms\Components\Section::make('Status & Availability')
                                    ->description('Agent status and availability settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_available')
                                            ->label('Available')
                                            ->default(true),
                                        Forms\Components\Toggle::make('is_verified')
                                            ->label('Verified')
                                            ->default(false),
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured')
                                            ->default(false),
                                        Forms\Components\Toggle::make('accepts_new_clients')
                                            ->label('Accepting New Clients')
                                            ->default(true),
                                        Forms\Components\DateTimePicker::make('verified_at')
                                            ->label('Verified At')
                                            ->disabled(),
                                        Forms\Components\DateTimePicker::make('last_active_at')
                                            ->label('Last Active')
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Agent Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Agency')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('years_experience')
                    ->label('Experience')
                    ->numeric()
                    ->sortable()
                    ->suffix(' years'),

                Tables\Columns\TextColumn::make('commission_rate')
                    ->label('Commission')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->suffix('%'),

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
                    ->label('Total Properties')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('properties_sold')
                    ->label('Sold')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('properties_rented')
                    ->label('Rented')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('active_listings')
                    ->label('Active Listings')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_available')
                    ->badge()
                    ->label('Availability')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Available' : 'Unavailable'),

                Tables\Columns\TextColumn::make('is_verified')
                    ->badge()
                    ->label('Verified')
                    ->colors([
                        'success' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Unverified'),

                Tables\Columns\TextColumn::make('accepts_new_clients')
                    ->badge()
                    ->label('New Clients')
                    ->colors([
                        'success' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Accepting' : 'Not Accepting')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('is_featured')
                    ->badge()
                    ->label('Featured')
                    ->colors([
                        'warning' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Featured' : 'Standard')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_reviews')
                    ->label('Reviews')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('specializations')
                    ->label('Specializations')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(30),

                Tables\Columns\TextColumn::make('license_number')
                    ->label('License')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('license_expiry_date')
                    ->label('License Expiry')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_active_at')
                    ->label('Last Active')
                    ->dateTime()
                    ->sortable()
                    ->since()
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
                Tables\Filters\SelectFilter::make('agency_id')
                    ->label('Agency')
                    ->relationship('agency', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->trueLabel('Available Only')
                    ->falseLabel('Unavailable Only')
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

                Tables\Filters\TernaryFilter::make('accepts_new_clients')
                    ->label('Accepting New Clients')
                    ->trueLabel('Accepting Only')
                    ->falseLabel('Not Accepting Only')
                    ->native(false),

                Tables\Filters\Filter::make('years_experience')
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
                                fn(Builder $query, $years): Builder => $query->where('years_experience', '>=', $years),
                            )
                            ->when(
                                $data['years_to'],
                                fn(Builder $query, $years): Builder => $query->where('years_experience', '<=', $years),
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

                Tables\Filters\Filter::make('commission_rate')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('commission_from')
                                    ->numeric()
                                    ->step(0.1)
                                    ->label('Commission from (%)')
                                    ->placeholder('Min commission'),
                                Forms\Components\TextInput::make('commission_to')
                                    ->numeric()
                                    ->step(0.1)
                                    ->label('Commission to (%)')
                                    ->placeholder('Max commission'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['commission_from'],
                                fn(Builder $query, $rate): Builder => $query->where('commission_rate', '>=', $rate),
                            )
                            ->when(
                                $data['commission_to'],
                                fn(Builder $query, $rate): Builder => $query->where('commission_rate', '<=', $rate),
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

                Tables\Filters\Filter::make('top_performers')
                    ->label('Top Performing Agents')
                    ->query(fn(Builder $query): Builder => $query->where('rating', '>=', 4.0)->where('properties_sold', '>=', 10))
                    ->toggle(),

                Tables\Filters\Filter::make('recently_active')
                    ->label('Recently Active')
                    ->query(fn(Builder $query): Builder => $query->where('last_active_at', '>=', now()->subDays(7)))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'edit' => Pages\EditAgent::route('/{record}/edit'),
        ];
    }
}
