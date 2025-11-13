<?php

namespace App\Filament\Resources\AgencyResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use App\Models\Agent;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentsRelationManager extends RelationManager
{
    protected static string $relationship = 'agents';

    protected static ?string $recordTitleAttribute = 'user.name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Agent Information')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select a user to make an agent')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(User::class),
                                TextInput::make('phone')
                                    ->tel()
                                    ->required(),
                                Select::make('user_type')
                                    ->options([
                                        'agent' => 'Agent',
                                    ])
                                    ->default('agent')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $user = User::create($data);
                                return $user->id;
                            }),
                        
                        TextInput::make('license_number')
                            ->label('License Number')
                            ->maxLength(255),
                        
                        DatePicker::make('license_expiry_date')
                            ->label('License Expiry Date'),
                        
                        Textarea::make('bio')
                            ->label('Biography')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
                
                Section::make('Professional Details')
                    ->schema([
                        TextInput::make('years_experience')
                            ->label('Years of Experience')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50),
                        
                        TextInput::make('commission_rate')
                            ->label('Commission Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%'),
                        
                        TagsInput::make('languages')
                            ->label('Languages Spoken')
                            ->placeholder('Add languages...'),
                        
                        TextInput::make('specializations')
                            ->label('Specializations')
                            ->placeholder('e.g., Residential, Commercial, Luxury Properties')
                            ->maxLength(500),
                    ]),
                
                Section::make('Status & Settings')
                    ->schema([
                        Toggle::make('is_available')
                            ->label('Available for New Clients')
                            ->default(true),
                        
                        Toggle::make('is_verified')
                            ->label('Verified Agent')
                            ->default(false),
                        
                        Toggle::make('is_featured')
                            ->label('Featured Agent')
                            ->default(false),
                        
                        Toggle::make('accepts_new_clients')
                            ->label('Accepting New Clients')
                            ->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                ImageColumn::make('user.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl('/images/default-avatar.png'),

                TextColumn::make('user.name')
                    ->label('Agent Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('user.phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                TextColumn::make('license_number')
                    ->label('License')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Not set'),

                TextColumn::make('years_experience')
                    ->label('Experience')
                    ->suffix(' years')
                    ->sortable()
                    ->placeholder('Not set'),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 4.0 => 'info',
                        $state >= 3.5 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5.0' : 'No rating'),

                TextColumn::make('total_properties')
                    ->label('Properties')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning'),

                IconColumn::make('accepts_new_clients')
                    ->label('New Clients')
                    ->boolean()
                    ->trueIcon('heroicon-o-user-plus')
                    ->falseIcon('heroicon-o-user-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('last_active_at')
                    ->label('Last Active')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Never'),
            ])
            ->filters([
                TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->trueLabel('Verified Only')
                    ->falseLabel('Unverified Only')
                    ->placeholder('All Agents'),

                TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->trueLabel('Available Only')
                    ->falseLabel('Unavailable Only')
                    ->placeholder('All Agents'),

                TernaryFilter::make('accepts_new_clients')
                    ->label('Accepting Clients')
                    ->trueLabel('Accepting Only')
                    ->falseLabel('Not Accepting Only')
                    ->placeholder('All Agents'),

                Filter::make('experienced')
                    ->query(fn (Builder $query): Builder => $query->where('years_experience', '>=', 5))
                    ->label('Experienced (5+ years)'),

                Filter::make('high_rated')
                    ->query(fn (Builder $query): Builder => $query->where('rating', '>=', 4.0))
                    ->label('Highly Rated (4.0+)'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Agent')
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading('Add New Agent to Agency')
                    ->successNotificationTitle('Agent added successfully!')
                    ->mutateDataUsing(function (array $data): array {
                        // Ensure the agent is associated with this agency
                        $data['agency_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),

                Action::make('toggle_availability')
                    ->label(fn (Agent $record): string => $record->is_available ? 'Mark Unavailable' : 'Mark Available')
                    ->icon(fn (Agent $record): string => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Agent $record): string => $record->is_available ? 'danger' : 'success')
                    ->action(function (Agent $record): void {
                        $record->update(['is_available' => !$record->is_available]);
                        $this->getTable()->refreshRecords();
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to change this agent\'s availability status?'),

                DeleteAction::make()
                    ->label('Remove')
                    ->icon('heroicon-o-trash')
                    ->modalHeading('Remove Agent from Agency')
                    ->modalDescription('This will remove the agent from your agency. The agent will become independent.')
                    ->successNotificationTitle('Agent removed from agency')
                    ->before(function (Agent $record) {
                        // Make agent independent instead of deleting
                        $record->update(['agency_id' => null]);
                        return false; // Prevent actual deletion
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_available')
                        ->label('Mark Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records): void {
                            $records->each(fn (Agent $record) => $record->update(['is_available' => true]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('mark_unavailable')
                        ->label('Mark Unavailable')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records): void {
                            $records->each(fn (Agent $record) => $record->update(['is_available' => false]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('remove_from_agency')
                        ->label('Remove from Agency')
                        ->icon('heroicon-o-user-minus')
                        ->color('warning')
                        ->action(function ($records): void {
                            $records->each(fn (Agent $record) => $record->update(['agency_id' => null]));
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Remove Agents from Agency')
                        ->modalDescription('This will make the selected agents independent. Are you sure?')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s'); // Refresh every minute for real-time status updates
    }
}
