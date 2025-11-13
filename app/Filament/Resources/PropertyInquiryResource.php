<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PropertyInquiryResource\Pages\ListPropertyInquiries;
use App\Filament\Resources\PropertyInquiryResource\Pages\CreatePropertyInquiry;
use App\Filament\Resources\PropertyInquiryResource\Pages\EditPropertyInquiry;
use App\Filament\Resources\PropertyInquiryResource\Pages;
use App\Filament\Resources\PropertyInquiryResource\RelationManagers;
use App\Models\PropertyInquiry;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyInquiryResource extends Resource
{
    protected static ?string $model = PropertyInquiry::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 7;

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
                                // Property & Inquiry Details
                                Section::make('Inquiry Information')
                                    ->description('Property inquiry details and inquirer information')
                                    ->schema([
                                        Select::make('property_id')
                                            ->label('Property')
                                            ->relationship('property', 'title')
                                            ->required()
                                            ->searchable()
                                            ->columnSpan(2),
                                        Select::make('inquirer_id')
                                            ->label('Registered User')
                                            ->relationship('inquirer', 'name')
                                            ->searchable()
                                            ->columnSpan(2),
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('inquirer_name')
                                                    ->label('Inquirer Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('inquirer_email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('inquirer_phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->maxLength(255),
                                            ]),
                                        DatePicker::make('preferred_viewing_date')
                                            ->label('Preferred Viewing Date'),
                                        Textarea::make('message')
                                            ->label('Inquiry Message')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])->columns(4)->collapsible(),

                                // Response Management
                                Section::make('Response Details')
                                    ->description('Agent response and follow-up information')
                                    ->schema([
                                        Select::make('responded_by')
                                            ->label('Responded By')
                                            ->relationship('responder', 'name')
                                            ->searchable(),
                                        DateTimePicker::make('responded_at')
                                            ->label('Response Date')
                                            ->disabled(),
                                        Textarea::make('response')
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
                        Group::make()
                            ->schema([
                                // Status Management - Sidebar
                                Section::make('Status & Priority')
                                    ->description('Inquiry status and priority settings')
                                    ->schema([
                                        Select::make('status')
                                            ->required()
                                            ->options([
                                                'pending' => 'Pending',
                                                'responded' => 'Responded',
                                                'scheduled' => 'Viewing Scheduled',
                                                'completed' => 'Completed',
                                                'closed' => 'Closed'
                                            ])
                                            ->default('pending'),
                                        Select::make('priority')
                                            ->options([
                                                'low' => 'Low',
                                                'medium' => 'Medium',
                                                'high' => 'High',
                                                'urgent' => 'Urgent'
                                            ])
                                            ->default('medium'),
                                        Toggle::make('is_follow_up_required')
                                            ->label('Follow-up Required')
                                            ->default(false),
                                        DateTimePicker::make('follow_up_date')
                                            ->label('Follow-up Date'),
                                    ])->columns(1)->collapsible(),

                                // Activity Tracking - Sidebar
                                Section::make('Activity Tracking')
                                    ->description('Inquiry tracking and timestamps')
                                    ->schema([
                                        DateTimePicker::make('created_at')
                                            ->label('Inquiry Date')
                                            ->disabled(),
                                        DateTimePicker::make('first_response_at')
                                            ->label('First Response')
                                            ->disabled(),
                                        DateTimePicker::make('last_activity_at')
                                            ->label('Last Activity')
                                            ->disabled(),
                                        TextInput::make('response_time_hours')
                                            ->label('Response Time (Hours)')
                                            ->numeric()
                                            ->disabled(),
                                    ])->columns(1)->collapsible(),

                                // Additional Notes - Sidebar
                                Section::make('Internal Notes')
                                    ->description('Internal notes and follow-up details')
                                    ->schema([
                                        Textarea::make('internal_notes')
                                            ->label('Agent Notes')
                                            ->rows(3),
                                        TagsInput::make('tags')
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
                TextColumn::make('property.title')
                    ->label('Property')
                    ->sortable()
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),

                TextColumn::make('inquirer_name')
                    ->label('Inquirer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('inquirer_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('inquirer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'responded',
                        'info' => 'scheduled',
                        'success' => 'completed',
                        'gray' => 'closed',
                    ]),

                TextColumn::make('priority')
                    ->badge()
                    ->label('Priority')
                    ->colors([
                        'gray' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('inquirer.name')
                    ->label('Registered User')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Guest User')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('preferred_viewing_date')
                    ->label('Viewing Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('responder.name')
                    ->label('Responded By')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Not responded'),

                TextColumn::make('responded_at')
                    ->label('Response Time')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Not responded'),

                TextColumn::make('response_time_hours')
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

                IconColumn::make('is_follow_up_required')
                    ->label('Follow-up')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('follow_up_date')
                    ->label('Follow-up Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('first_response_at')
                    ->label('First Response')
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
                    ->label('Inquiry Date')
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
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'responded' => 'Responded',
                        'scheduled' => 'Viewing Scheduled',
                        'completed' => 'Completed',
                        'closed' => 'Closed'
                    ])
                    ->native(false),

                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent'
                    ])
                    ->native(false),

                SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('responded_by')
                    ->label('Responded By')
                    ->relationship('responder', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('inquirer_id')
                    ->label('User Type')
                    ->trueLabel('Registered Users')
                    ->falseLabel('Guest Inquiries')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('inquirer_id'),
                        false: fn(Builder $query) => $query->whereNull('inquirer_id'),
                    )
                    ->native(false),

                TernaryFilter::make('is_follow_up_required')
                    ->label('Follow-up Required')
                    ->trueLabel('Requires Follow-up')
                    ->falseLabel('No Follow-up Needed')
                    ->native(false),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Inquiry from'),
                        DatePicker::make('created_until')
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

                Filter::make('response_time')
                    ->schema([
                        TextInput::make('response_hours')
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

                Filter::make('pending_inquiries')
                    ->label('Pending Inquiries')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'pending'))
                    ->toggle(),

                Filter::make('urgent_inquiries')
                    ->label('Urgent Inquiries')
                    ->query(fn(Builder $query): Builder => $query->where('priority', 'urgent'))
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
            'index' => ListPropertyInquiries::route('/'),
            'create' => CreatePropertyInquiry::route('/create'),
            'edit' => EditPropertyInquiry::route('/{record}/edit'),
        ];
    }
}
