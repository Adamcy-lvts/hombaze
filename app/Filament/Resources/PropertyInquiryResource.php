<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyInquiryResource\Pages;
use App\Filament\Resources\PropertyInquiryResource\RelationManagers;
use App\Models\PropertyInquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyInquiryResource extends Resource
{
    protected static ?string $model = PropertyInquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Two column layout: Main content (3/4) and Sidebar (1/4)
                Forms\Components\Grid::make([
                    'default' => 1,
                    'lg' => 4,
                ])
                    ->schema([
                        // Main Content Area (spans 3 columns)
                        Forms\Components\Group::make()
                            ->schema([
                                // Property & Inquiry Details
                                Forms\Components\Section::make('Inquiry Information')
                                    ->description('Property inquiry details and inquirer information')
                                    ->schema([
                                        Forms\Components\Select::make('property_id')
                                            ->label('Property')
                                            ->relationship('property', 'title')
                                            ->required()
                                            ->searchable()
                                            ->columnSpan(2),
                                        Forms\Components\Select::make('inquirer_id')
                                            ->label('Registered User')
                                            ->relationship('inquirer', 'name')
                                            ->searchable()
                                            ->columnSpan(2),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('inquirer_name')
                                                    ->label('Inquirer Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('inquirer_email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('inquirer_phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\DatePicker::make('preferred_viewing_date')
                                            ->label('Preferred Viewing Date'),
                                        Forms\Components\Textarea::make('message')
                                            ->label('Inquiry Message')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])->columns(4)->collapsible(),

                                // Response Management
                                Forms\Components\Section::make('Response Details')
                                    ->description('Agent response and follow-up information')
                                    ->schema([
                                        Forms\Components\Select::make('responded_by')
                                            ->label('Responded By')
                                            ->relationship('responder', 'name')
                                            ->searchable(),
                                        Forms\Components\DateTimePicker::make('responded_at')
                                            ->label('Response Date')
                                            ->disabled(),
                                        Forms\Components\Textarea::make('response')
                                            ->label('Response Message')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 3,
                            ]),

                        // Sidebar (spans 1 column)
                        Forms\Components\Group::make()
                            ->schema([
                                // Status Management - Sidebar
                                Forms\Components\Section::make('Status & Priority')
                                    ->description('Inquiry status and priority settings')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->required()
                                            ->options([
                                                'pending' => 'Pending',
                                                'responded' => 'Responded',
                                                'scheduled' => 'Viewing Scheduled',
                                                'completed' => 'Completed',
                                                'closed' => 'Closed'
                                            ])
                                            ->default('pending'),
                                        Forms\Components\Select::make('priority')
                                            ->options([
                                                'low' => 'Low',
                                                'medium' => 'Medium',
                                                'high' => 'High',
                                                'urgent' => 'Urgent'
                                            ])
                                            ->default('medium'),
                                        Forms\Components\Toggle::make('is_follow_up_required')
                                            ->label('Follow-up Required')
                                            ->default(false),
                                        Forms\Components\DateTimePicker::make('follow_up_date')
                                            ->label('Follow-up Date'),
                                    ])->columns(1)->collapsible(),

                                // Activity Tracking - Sidebar
                                Forms\Components\Section::make('Activity Tracking')
                                    ->description('Inquiry tracking and timestamps')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('created_at')
                                            ->label('Inquiry Date')
                                            ->disabled(),
                                        Forms\Components\DateTimePicker::make('first_response_at')
                                            ->label('First Response')
                                            ->disabled(),
                                        Forms\Components\DateTimePicker::make('last_activity_at')
                                            ->label('Last Activity')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('response_time_hours')
                                            ->label('Response Time (Hours)')
                                            ->numeric()
                                            ->disabled(),
                                    ])->columns(1)->collapsible(),

                                // Additional Notes - Sidebar
                                Forms\Components\Section::make('Internal Notes')
                                    ->description('Internal notes and follow-up details')
                                    ->schema([
                                        Forms\Components\Textarea::make('internal_notes')
                                            ->label('Agent Notes')
                                            ->rows(3),
                                        Forms\Components\TagsInput::make('tags')
                                            ->label('Tags')
                                            ->placeholder('e.g., vip, urgent, callback')
                                            ->separator(','),
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
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->sortable()
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('inquirer_name')
                    ->label('Inquirer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inquirer_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('inquirer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'responded',
                        'info' => 'scheduled',
                        'success' => 'completed',
                        'gray' => 'closed',
                    ]),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->label('Priority')
                    ->colors([
                        'gray' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('inquirer.name')
                    ->label('Registered User')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Guest User')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('preferred_viewing_date')
                    ->label('Viewing Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('responder.name')
                    ->label('Responded By')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Not responded'),

                Tables\Columns\TextColumn::make('responded_at')
                    ->label('Response Time')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Not responded'),

                Tables\Columns\TextColumn::make('response_time_hours')
                    ->label('Response Hours')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->suffix(' hrs')
                    ->color(fn($state) => match (true) {
                        $state === null => 'gray',
                        $state <= 1 => 'success',
                        $state <= 24 => 'warning',
                        default => 'danger',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_follow_up_required')
                    ->label('Follow-up')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('follow_up_date')
                    ->label('Follow-up Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('first_response_at')
                    ->label('First Response')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inquiry Date')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'responded' => 'Responded',
                        'scheduled' => 'Viewing Scheduled',
                        'completed' => 'Completed',
                        'closed' => 'Closed'
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent'
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('responded_by')
                    ->label('Responded By')
                    ->relationship('responder', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('inquirer_id')
                    ->label('User Type')
                    ->trueLabel('Registered Users')
                    ->falseLabel('Guest Inquiries')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('inquirer_id'),
                        false: fn(Builder $query) => $query->whereNull('inquirer_id'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_follow_up_required')
                    ->label('Follow-up Required')
                    ->trueLabel('Requires Follow-up')
                    ->falseLabel('No Follow-up Needed')
                    ->native(false),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Inquiry from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Inquiry until'),
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

                Tables\Filters\Filter::make('response_time')
                    ->form([
                        Forms\Components\TextInput::make('response_hours')
                            ->numeric()
                            ->label('Response time (hours)')
                            ->placeholder('Max response time'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['response_hours'],
                                fn(Builder $query, $hours): Builder => $query->where('response_time_hours', '<=', $hours),
                            );
                    }),

                Tables\Filters\Filter::make('pending_inquiries')
                    ->label('Pending Inquiries')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'pending'))
                    ->toggle(),

                Tables\Filters\Filter::make('urgent_inquiries')
                    ->label('Urgent Inquiries')
                    ->query(fn(Builder $query): Builder => $query->where('priority', 'urgent'))
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
            'index' => Pages\ListPropertyInquiries::route('/'),
            'create' => Pages\CreatePropertyInquiry::route('/create'),
            'edit' => Pages\EditPropertyInquiry::route('/{record}/edit'),
        ];
    }
}
