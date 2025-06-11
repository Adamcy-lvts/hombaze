<?php

namespace App\Filament\Resources\AgencyResource\RelationManagers;

use App\Models\Agent;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentsRelationManager extends RelationManager
{
    protected static string $relationship = 'agents';

    protected static ?string $recordTitleAttribute = 'user.name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Agent Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select a user to make an agent')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(User::class),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required(),
                                Forms\Components\Select::make('user_type')
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
                        
                        Forms\Components\TextInput::make('license_number')
                            ->label('License Number')
                            ->maxLength(255),
                        
                        Forms\Components\DatePicker::make('license_expiry_date')
                            ->label('License Expiry Date'),
                        
                        Forms\Components\Textarea::make('bio')
                            ->label('Biography')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
                
                Forms\Components\Section::make('Professional Details')
                    ->schema([
                        Forms\Components\TextInput::make('years_experience')
                            ->label('Years of Experience')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50),
                        
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('Commission Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%'),
                        
                        Forms\Components\TagsInput::make('languages')
                            ->label('Languages Spoken')
                            ->placeholder('Add languages...'),
                        
                        Forms\Components\TextInput::make('specializations')
                            ->label('Specializations')
                            ->placeholder('e.g., Residential, Commercial, Luxury Properties')
                            ->maxLength(500),
                    ]),
                
                Forms\Components\Section::make('Status & Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Available for New Clients')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified Agent')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Agent')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('accepts_new_clients')
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
                Tables\Columns\ImageColumn::make('user.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl('/images/default-avatar.png'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Agent Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),
                
                Tables\Columns\TextColumn::make('license_number')
                    ->label('License')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Not set'),
                
                Tables\Columns\TextColumn::make('years_experience')
                    ->label('Experience')
                    ->suffix(' years')
                    ->sortable()
                    ->placeholder('Not set'),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 4.0 => 'info',
                        $state >= 3.5 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5.0' : 'No rating'),
                
                Tables\Columns\TextColumn::make('total_properties')
                    ->label('Properties')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning'),
                
                Tables\Columns\IconColumn::make('accepts_new_clients')
                    ->label('New Clients')
                    ->boolean()
                    ->trueIcon('heroicon-o-user-plus')
                    ->falseIcon('heroicon-o-user-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('last_active_at')
                    ->label('Last Active')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Never'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->trueLabel('Verified Only')
                    ->falseLabel('Unverified Only')
                    ->placeholder('All Agents'),
                
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->trueLabel('Available Only')
                    ->falseLabel('Unavailable Only')
                    ->placeholder('All Agents'),
                
                Tables\Filters\TernaryFilter::make('accepts_new_clients')
                    ->label('Accepting Clients')
                    ->trueLabel('Accepting Only')
                    ->falseLabel('Not Accepting Only')
                    ->placeholder('All Agents'),
                
                Tables\Filters\Filter::make('experienced')
                    ->query(fn (Builder $query): Builder => $query->where('years_experience', '>=', 5))
                    ->label('Experienced (5+ years)'),
                
                Tables\Filters\Filter::make('high_rated')
                    ->query(fn (Builder $query): Builder => $query->where('rating', '>=', 4.0))
                    ->label('Highly Rated (4.0+)'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Agent')
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading('Add New Agent to Agency')
                    ->successNotificationTitle('Agent added successfully!')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Ensure the agent is associated with this agency
                        $data['agency_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),
                
                Tables\Actions\Action::make('toggle_availability')
                    ->label(fn (Agent $record): string => $record->is_available ? 'Mark Unavailable' : 'Mark Available')
                    ->icon(fn (Agent $record): string => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Agent $record): string => $record->is_available ? 'danger' : 'success')
                    ->action(function (Agent $record): void {
                        $record->update(['is_available' => !$record->is_available]);
                        $this->getTable()->refreshRecords();
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to change this agent\'s availability status?'),
                
                Tables\Actions\DeleteAction::make()
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('Mark Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records): void {
                            $records->each(fn (Agent $record) => $record->update(['is_available' => true]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('mark_unavailable')
                        ->label('Mark Unavailable')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records): void {
                            $records->each(fn (Agent $record) => $record->update(['is_available' => false]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('remove_from_agency')
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
