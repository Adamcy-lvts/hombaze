<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ReviewResource\Pages\ListReviews;
use App\Filament\Resources\ReviewResource\Pages\CreateReview;
use App\Filament\Resources\ReviewResource\Pages\EditReview;
use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

    protected static string | \UnitEnum | null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Two column layout: Main content (3/4) and Sidebar (1/4)
                Grid::make([
                    'default' => 1,
                    'lg' => 4,
                ])
                    ->schema([
                        // Main Content Area (spans 3 columns)
                        Group::make()
                            ->schema([
                                // Review Target & Author
                                Section::make('Review Information')
                                    ->description('What and who is being reviewed')
                                    ->schema([
                                        Select::make('reviewable_type')
                                            ->label('Review Type')
                                            ->required()
                                            ->options([
                                                'App\\Models\\Property' => 'Property',
                                                'App\\Models\\Agent' => 'Agent',
                                                'App\\Models\\Agency' => 'Agency'
                                            ])
                                            ->reactive(),
                                        TextInput::make('reviewable_id')
                                            ->label('Item ID')
                                            ->required()
                                            ->numeric()
                                            ->helperText('The ID of the property, agent, or agency being reviewed'),
                                        Select::make('reviewer_id')
                                            ->label('Reviewer')
                                            ->relationship('reviewer', 'name')
                                            ->required()
                                            ->searchable(),
                                    ])->columns(3)->collapsible(),

                                // Review Content
                                Section::make('Review Content')
                                    ->description('The actual review details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('rating')
                                                    ->label('Rating')
                                                    ->required()
                                                    ->options([
                                                        1 => '1 Star - Very Poor',
                                                        2 => '2 Stars - Poor',
                                                        3 => '3 Stars - Average',
                                                        4 => '4 Stars - Good',
                                                        5 => '5 Stars - Excellent'
                                                    ])
                                                    ->default(5),
                                                TextInput::make('title')
                                                    ->label('Review Title')
                                                    ->maxLength(255)
                                                    ->placeholder('Brief summary of your review'),
                                            ]),
                                        Textarea::make('comment')
                                            ->label('Review Comment')
                                            ->required()
                                            ->rows(5)
                                            ->placeholder('Share your detailed experience...')
                                            ->columnSpanFull(),
                                    ])->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 3,
                            ]),

                        // Sidebar (spans 1 column)
                        Group::make()
                            ->schema([
                                // Review Management - Sidebar
                                Section::make('Review Management')
                                    ->description('Review status and moderation')
                                    ->schema([
                                        Select::make('status')
                                            ->required()
                                            ->options([
                                                'pending' => 'Pending Review',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                'flagged' => 'Flagged for Review'
                                            ])
                                            ->default('pending'),
                                        Toggle::make('is_verified')
                                            ->label('Verified Review')
                                            ->default(false)
                                            ->helperText('Mark as verified purchase/interaction'),
                                        Toggle::make('is_featured')
                                            ->label('Featured Review')
                                            ->default(false),
                                        Toggle::make('is_anonymous')
                                            ->label('Anonymous Review')
                                            ->default(false),
                                    ])->columns(1)->collapsible(),

                                // Moderation Details - Sidebar
                                Section::make('Moderation Details')
                                    ->description('Moderation and approval information')
                                    ->schema([
                                        Select::make('moderated_by')
                                            ->label('Moderated By')
                                            ->relationship('moderator', 'name')
                                            ->searchable(),
                                        DateTimePicker::make('moderated_at')
                                            ->label('Moderated At')
                                            ->disabled(),
                                        Textarea::make('moderation_notes')
                                            ->label('Moderation Notes')
                                            ->rows(3)
                                            ->placeholder('Internal moderation notes...'),
                                    ])->columns(1)->collapsible(),

                                // Review Analytics - Sidebar
                                Section::make('Review Analytics')
                                    ->description('Review engagement and metrics')
                                    ->schema([
                                        TextInput::make('helpful_count')
                                            ->label('Helpful Votes')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled(),
                                        TextInput::make('not_helpful_count')
                                            ->label('Not Helpful Votes')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled(),
                                        TextInput::make('response_count')
                                            ->label('Responses')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled(),
                                        DateTimePicker::make('last_activity_at')
                                            ->label('Last Activity')
                                            ->disabled(),
                                    ])->columns(1)->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ]),
                    ]),

                // Review Management
                Section::make('Review Management')
                    ->description('Moderation and engagement metrics')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_approved')
                                    ->label('Approved')
                                    ->default(false),
                                Toggle::make('is_verified')
                                    ->label('Verified')
                                    ->default(false),
                                TextInput::make('helpful_count')
                                    ->label('Helpful Votes')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),
                            ]),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reviewable_type')
                    ->badge()
                    ->label('Review Type')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\\Models\\Property' => 'Property',
                        'App\\Models\\Agent' => 'Agent',
                        'App\\Models\\Agency' => 'Agency',
                        default => $state,
                    })
                    ->colors([
                        'info' => 'App\\Models\\Property',
                        'success' => 'App\\Models\\Agent',
                        'warning' => 'App\\Models\\Agency',
                    ]),

                TextColumn::make('reviewable_id')
                    ->label('Item ID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Anonymous'),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->color(fn($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'danger' => 'flagged',
                    ]),

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

                IconColumn::make('is_anonymous')
                    ->label('Anonymous')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('helpful_count')
                    ->label('Helpful')
                    ->numeric()
                    ->sortable()
                    ->suffix(' votes')
                    ->color(fn($state) => $state > 10 ? 'success' : 'gray'),

                TextColumn::make('not_helpful_count')
                    ->label('Not Helpful')
                    ->numeric()
                    ->sortable()
                    ->suffix(' votes')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('response_count')
                    ->label('Responses')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('moderator.name')
                    ->label('Moderated By')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Not moderated')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('moderated_at')
                    ->label('Moderated At')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_activity_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Review Date')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('reviewable_type')
                    ->label('Review Type')
                    ->options([
                        'App\\Models\\Property' => 'Property',
                        'App\\Models\\Agent' => 'Agent',
                        'App\\Models\\Agency' => 'Agency'
                    ])
                    ->native(false),

                SelectFilter::make('rating')
                    ->options([
                        5 => '5 Stars',
                        4 => '4 Stars',
                        3 => '3 Stars',
                        2 => '2 Stars',
                        1 => '1 Star',
                    ])
                    ->native(false),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'flagged' => 'Flagged for Review'
                    ])
                    ->native(false),

                SelectFilter::make('reviewer_id')
                    ->label('Reviewer')
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('moderated_by')
                    ->label('Moderated By')
                    ->relationship('moderator', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->trueLabel('Verified Only')
                    ->falseLabel('Unverified Only')
                    ->native(false),

                TernaryFilter::make('is_approved')
                    ->label('Approval Status')
                    ->trueLabel('Approved Only')
                    ->falseLabel('Unapproved Only')
                    ->native(false),

                TernaryFilter::make('is_featured')
                    ->label('Featured Status')
                    ->trueLabel('Featured Only')
                    ->falseLabel('Non-Featured Only')
                    ->native(false),

                TernaryFilter::make('is_anonymous')
                    ->label('Anonymous Reviews')
                    ->trueLabel('Anonymous Only')
                    ->falseLabel('Named Only')
                    ->native(false),

                Filter::make('helpful_count')
                    ->schema([
                        TextInput::make('helpful_min')
                            ->numeric()
                            ->label('Min helpful votes')
                            ->placeholder('Minimum helpful votes'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['helpful_min'],
                                fn(Builder $query, $count): Builder => $query->where('helpful_count', '>=', $count),
                            );
                    }),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Reviews from'),
                        DatePicker::make('created_until')
                            ->label('Reviews until'),
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

                Filter::make('high_rated')
                    ->label('High Rated (4+ Stars)')
                    ->query(fn(Builder $query): Builder => $query->where('rating', '>=', 4))
                    ->toggle(),

                Filter::make('pending_approval')
                    ->label('Pending Approval')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'pending'))
                    ->toggle(),

                Filter::make('flagged_reviews')
                    ->label('Flagged Reviews')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'flagged'))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
