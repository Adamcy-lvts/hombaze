<?php

namespace App\Filament\Agency\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Spatie\Permission\PermissionRegistrar;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use App\Filament\Agency\Resources\AgentResource\Pages\ListAgents;
use App\Filament\Agency\Resources\AgentResource\Pages\CreateAgent;
use App\Filament\Agency\Resources\AgentResource\Pages\EditAgent;
use App\Filament\Agency\Resources\AgentResource\Pages;
use App\Filament\Agency\Resources\AgentResource\RelationManagers;
use App\Models\Agent;
use App\Models\User;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Team Agents';
    
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
                                // Basic User Information
                                Section::make('Agent Account Information')
                                    ->description('Basic account details and login credentials')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Full Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Enter agent\'s full name')
                                                    ->helperText('Agent\'s complete name as it should appear on listings')
                                                    ->columnSpanFull(),
                                                    
                                                TextInput::make('email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->required()
                                                    ->unique(User::class)
                                                    ->placeholder('Enter email address')
                                                    ->helperText('This will be used for login and client communications')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->required()
                                                    ->placeholder('Enter phone number')
                                                    ->helperText('Primary contact number for clients and office')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->required()
                                                    ->minLength(8)
                                                    ->revealable()
                                                    ->default('password123!')
                                                    ->placeholder('Enter secure password')
                                                    ->helperText('Agent can change this after first login. Default: password123!')
                                                    ->columnSpanFull(),
                                            ]),
                                    ])->collapsible(),

                                // Personal Details
                                Section::make('Personal Details')
                                    ->description('Additional personal information and profile details')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter first name')
                                                    ->helperText('First name (optional - will be extracted from full name if not provided)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter last name')
                                                    ->helperText('Last name (optional - will be extracted from full name if not provided)')
                                                    ->columnSpan(1),
                                                    
                                                DatePicker::make('date_of_birth')
                                                    ->label('Date of Birth (Optional)')
                                                    ->native(false)
                                                    ->maxDate(now()->subYears(18))
                                                    ->placeholder('Select date of birth')
                                                    ->helperText('Agent\'s date of birth (optional)')
                                                    ->columnSpan(1),
                                                    
                                                Select::make('gender')
                                                    ->label('Gender (Optional)')
                                                    ->options([
                                                        'male' => 'Male',
                                                        'female' => 'Female',
                                                        'other' => 'Other',
                                                        'prefer_not_to_say' => 'Prefer not to say',
                                                    ])
                                                    ->placeholder('Select gender')
                                                    ->helperText('Gender identification (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('alternate_phone')
                                                    ->label('Alternate Phone (Optional)')
                                                    ->tel()
                                                    ->placeholder('Enter alternate phone number')
                                                    ->helperText('Secondary contact number (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('occupation')
                                                    ->label('Previous Occupation (Optional)')
                                                    ->maxLength(255)
                                                    ->placeholder('e.g., Sales Manager, Teacher, etc.')
                                                    ->helperText('Previous occupation before real estate (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                        Textarea::make('bio')
                                            ->label('Professional Biography')
                                            ->rows(4)
                                            ->maxLength(1000)
                                            ->placeholder('Enter a brief professional biography...')
                                            ->helperText('Professional bio that will be displayed to clients (max 1000 characters)')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Emergency Contact Information
                                Section::make('Emergency Contact (Optional)')
                                    ->description('Emergency contact information for safety purposes')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('emergency_contact_name')
                                                    ->label('Emergency Contact Name')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter emergency contact name')
                                                    ->helperText('Name of emergency contact person (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('emergency_contact_phone')
                                                    ->label('Emergency Contact Phone')
                                                    ->tel()
                                                    ->placeholder('Enter emergency contact phone')
                                                    ->helperText('Phone number of emergency contact (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                    ])->collapsible()->collapsed(),

                                // Professional Real Estate Information
                                Section::make('Real Estate Professional Details')
                                    ->description('Real estate license and professional experience information')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('license_number')
                                                    ->label('Real Estate License Number (Optional)')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter real estate license number')
                                                    ->helperText('Real estate license number (if applicable)')
                                                    ->columnSpan(1),
                                                    
                                                DatePicker::make('license_expiry_date')
                                                    ->label('License Expiry Date (Optional)')
                                                    ->minDate(now())
                                                    ->placeholder('Select license expiry date')
                                                    ->helperText('License expiry date (if applicable)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('years_experience')
                                                    ->label('Years of Experience')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(50)
                                                    ->default(0)
                                                    ->placeholder('0')
                                                    ->helperText('Number of years in real estate (0-50)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('commission_rate')
                                                    ->label('Commission Rate (%) (Optional)')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->step(0.01)
                                                    ->suffix('%')
                                                    ->placeholder('3.0')
                                                    ->helperText('Default commission rate percentage (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                        TagsInput::make('languages')
                                            ->label('Languages Spoken (Optional)')
                                            ->placeholder('Add languages...')
                                            ->helperText('Languages the agent can speak (e.g., English, Spanish, French)')
                                            ->columnSpanFull(),
                                            
                                        TextInput::make('specializations')
                                            ->label('Specializations (Optional)')
                                            ->placeholder('e.g., Residential, Commercial, Luxury Properties')
                                            ->maxLength(500)
                                            ->helperText('Areas of expertise and property types specialized in (optional)')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Social Media & Online Presence
                                Section::make('Online Presence (Optional)')
                                    ->description('Social media profiles and website information')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('linkedin_url')
                                                    ->label('LinkedIn Profile')
                                                    ->url()
                                                    ->placeholder('https://linkedin.com/in/username')
                                                    ->helperText('LinkedIn profile URL (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('website_url')
                                                    ->label('Personal Website')
                                                    ->url()
                                                    ->placeholder('https://www.yourwebsite.com')
                                                    ->helperText('Personal or professional website (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('facebook_url')
                                                    ->label('Facebook Profile')
                                                    ->url()
                                                    ->placeholder('https://facebook.com/username')
                                                    ->helperText('Facebook profile URL (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('twitter_url')
                                                    ->label('Twitter/X Profile')
                                                    ->url()
                                                    ->placeholder('https://twitter.com/username')
                                                    ->helperText('Twitter/X profile URL (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                    ])->collapsible()->collapsed(),
                            ])
                            ->columnSpan(['lg' => 2]),

                        // Sidebar (1/3 width)
                        Group::make()
                            ->schema([
                                // Agent Status & Settings
                                Section::make('Agent Status')
                                    ->description('Availability and verification settings')
                                    ->schema([
                                        Toggle::make('is_available')
                                            ->label('Available for New Clients')
                                            ->helperText('Toggle agent availability for new client assignments')
                                            ->default(true),
                                            
                                        Toggle::make('accepts_new_clients')
                                            ->label('Accepting New Clients')
                                            ->helperText('Whether agent is currently accepting new clients')
                                            ->default(true),
                                            
                                        Toggle::make('is_verified')
                                            ->label('Verified Agent')
                                            ->helperText('Mark agent as verified (admin use)')
                                            ->default(false),
                                            
                                        Toggle::make('is_featured')
                                            ->label('Featured Agent')
                                            ->helperText('Feature agent on website and listings')
                                            ->default(false),
                                    ]),

                                // Financial Information
                                Section::make('Financial Information (Optional)')
                                    ->description('Income and budget information')
                                    ->schema([
                                        TextInput::make('annual_income')
                                            ->label('Annual Income')
                                            ->numeric()
                                            ->prefix('$')
                                            ->placeholder('50000')
                                            ->helperText('Estimated annual income (will be auto-calculated based on experience if not provided)'),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),

                                // ID Verification
                                Section::make('Identity Verification (Optional)')
                                    ->description('Government ID verification details')
                                    ->schema([
                                        Select::make('id_type')
                                            ->label('ID Type')
                                            ->options([
                                                'drivers_license' => 'Driver\'s License',
                                                'passport' => 'Passport',
                                                'national_id' => 'National ID',
                                                'other' => 'Other',
                                            ])
                                            ->placeholder('Select ID type')
                                            ->helperText('Type of government-issued ID'),
                                            
                                        TextInput::make('id_number')
                                            ->label('ID Number')
                                            ->maxLength(255)
                                            ->placeholder('Enter ID number')
                                            ->helperText('Government ID number (will be encrypted)'),
                                            
                                        Toggle::make('is_id_verified')
                                            ->label('ID Verified')
                                            ->helperText('Mark ID as verified (admin use)')
                                            ->default(false),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                
                IconColumn::make('user.is_active')
                    ->label('Account Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->user->is_active ? 'Account Active' : 'Account Blocked'),
                
                TextColumn::make('user_roles')
                    ->label('Roles')
                    ->badge()
                    ->getStateUsing(function (Agent $record): array {
                        $agency = Filament::getTenant();
                        return $record->user->roles()
                            ->where('roles.agency_id', $agency->id)
                            ->pluck('name')
                            ->toArray();
                    })
                    ->separator(', ')
                    ->placeholder('No roles assigned'),
                
                TextColumn::make('last_active_at')
                    ->label('Last Active')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Never'),
            ])
            ->filters([
                TernaryFilter::make('user.is_active')
                    ->label('Account Status')
                    ->trueLabel('Active Accounts Only')
                    ->falseLabel('Blocked Accounts Only')
                    ->placeholder('All Accounts'),
                
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
                
                Filter::make('has_roles')
                    ->label('Has Roles Assigned')
                    ->query(function (Builder $query): Builder {
                        $agency = Filament::getTenant();
                        return $query->whereHas('user.roles', function ($q) use ($agency) {
                            $q->where('roles.agency_id', $agency->id);
                        });
                    }),
                
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
                    // Filament tenancy will automatically handle agency_id association
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),
                
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),
                
                Action::make('manage_roles')
                    ->label('Manage Roles')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->can('update_role'))
                    ->schema([
                        Section::make('Role Management')
                            ->description('Manage agent roles and account access')
                            ->schema([
                                CheckboxList::make('roles')
                                    ->label('Assigned Roles')
                                    ->options(function (Agent $record) {
                                        $agency = Filament::getTenant();
                                        return Role::where('roles.agency_id', $agency->id)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->default(function (Agent $record) {
                                        $agency = Filament::getTenant();
                                        return $record->user->roles()
                                            ->where('roles.agency_id', $agency->id)
                                            ->pluck('id')
                                            ->toArray();
                                    })
                                    ->helperText('Select roles for this agent'),
                                
                                Toggle::make('is_active')
                                    ->label('Account Active')
                                    ->helperText('Uncheck to block agent login')
                                    ->default(fn (Agent $record) => $record->user->is_active)
                            ])
                    ])
                    ->action(function (Agent $record, array $data): void {
                        $agency = Filament::getTenant();
                        $user = $record->user;
                        
                        // Update account status
                        $user->update(['is_active' => $data['is_active']]);
                        
                        // Get current roles for this agency
                        $currentRoles = $user->roles()
                            ->where('roles.agency_id', $agency->id)
                            ->pluck('id')
                            ->toArray();
                        
                        // Get new roles
                        $newRoles = $data['roles'] ?? [];
                        
                        // Remove roles that are no longer selected
                        $rolesToRemove = array_diff($currentRoles, $newRoles);
                        if (!empty($rolesToRemove)) {
                            $rolesToRemoveModels = Role::whereIn('id', $rolesToRemove)->get();
                            foreach ($rolesToRemoveModels as $role) {
                                // Use setTenant to set the team context
                                app(PermissionRegistrar::class)->setPermissionsTeamId($agency->id);
                                $user->removeRole($role);
                            }
                        }
                        
                        // Add new roles
                        $rolesToAdd = array_diff($newRoles, $currentRoles);
                        if (!empty($rolesToAdd)) {
                            $rolesToAddModels = Role::whereIn('id', $rolesToAdd)->get();
                            foreach ($rolesToAddModels as $role) {
                                // Use setTenant to set the team context
                                app(PermissionRegistrar::class)->setPermissionsTeamId($agency->id);
                                $user->assignRole($role);
                            }
                        }
                        
                        Notification::make()
                            ->title('Agent roles updated successfully')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Manage Agent Roles and Access')
                    ->modalWidth('md'),
                
                Action::make('toggle_availability')
                    ->label(fn (Agent $record): string => $record->is_available ? 'Mark Unavailable' : 'Mark Available')
                    ->icon(fn (Agent $record): string => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Agent $record): string => $record->is_available ? 'danger' : 'success')
                    ->action(function (Agent $record): void {
                        $record->update(['is_available' => !$record->is_available]);
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to change this agent\'s availability status?'),
                
                Action::make('remove_from_agency')
                    ->label('Remove')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->modalHeading('Remove Agent from Agency')
                    ->modalDescription('This will remove the agent from your agency. The agent will become independent.')
                    ->successNotificationTitle('Agent removed from agency')
                    ->action(function (Agent $record) {
                        $record->update(['agency_id' => null]);
                    })
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_manage_roles')
                        ->label('Manage Roles')
                        ->icon('heroicon-o-shield-check')
                        ->color('warning')
                        ->visible(fn () => auth()->user()->can('update_role'))
                        ->form([
                            Section::make('Bulk Role Management')
                                ->description('Apply role changes to selected agents')
                                ->schema([
                                    Select::make('action_type')
                                        ->label('Action')
                                        ->options([
                                            'add' => 'Add Roles',
                                            'remove' => 'Remove Roles',
                                            'replace' => 'Replace All Roles',
                                        ])
                                        ->required()
                                        ->live(),
                                    
                                    CheckboxList::make('roles')
                                        ->label('Roles')
                                        ->options(function () {
                                            $agency = Filament::getTenant();
                                            return Role::where('roles.agency_id', $agency->id)
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        })
                                        ->required(),
                                ])
                        ])
                        ->action(function ($records, array $data): void {
                            $agency = Filament::getTenant();
                            $roleModels = Role::whereIn('id', $data['roles'])->get();
                            
                            foreach ($records as $agent) {
                                $user = $agent->user;
                                
                                switch ($data['action_type']) {
                                    case 'add':
                                        foreach ($roleModels as $role) {
                                            app(PermissionRegistrar::class)->setPermissionsTeamId($agency->id);
                                            if (!$user->hasRole($role)) {
                                                $user->assignRole($role);
                                            }
                                        }
                                        break;
                                        
                                    case 'remove':
                                        foreach ($roleModels as $role) {
                                            app(PermissionRegistrar::class)->setPermissionsTeamId($agency->id);
                                            if ($user->hasRole($role)) {
                                                $user->removeRole($role);
                                            }
                                        }
                                        break;
                                        
                                    case 'replace':
                                        // Set team context
                                        app(PermissionRegistrar::class)->setPermissionsTeamId($agency->id);
                                        
                                        // Remove all existing roles for this agency
                                        $existingRoles = $user->roles()
                                            ->where('roles.agency_id', $agency->id)
                                            ->get();
                                        foreach ($existingRoles as $role) {
                                            $user->removeRole($role);
                                        }
                                        
                                        // Add new roles
                                        foreach ($roleModels as $role) {
                                            $user->assignRole($role);
                                        }
                                        break;
                                }
                            }
                            
                            Notification::make()
                                ->title('Roles updated for selected agents')
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Bulk Manage Agent Roles')
                        ->modalWidth('md')
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('bulk_toggle_status')
                        ->label('Toggle Account Status')
                        ->icon('heroicon-o-user')
                        ->color('info')
                        ->visible(fn () => auth()->user()->can('update_user'))
                        ->form([
                            Section::make('Account Status')
                                ->description('Change account status for selected agents')
                                ->schema([
                                    Select::make('status')
                                        ->label('Set Status')
                                        ->options([
                                            'active' => 'Activate Accounts',
                                            'inactive' => 'Block Accounts',
                                        ])
                                        ->required(),
                                ])
                        ])
                        ->action(function ($records, array $data): void {
                            $isActive = $data['status'] === 'active';
                            
                            foreach ($records as $agent) {
                                $agent->user->update(['is_active' => $isActive]);
                            }
                            
                            $statusText = $isActive ? 'activated' : 'blocked';
                            Notification::make()
                                ->title("Selected agent accounts have been {$statusText}")
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Change Account Status')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    
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
            ->poll('60s');
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
            'index' => ListAgents::route('/'),
            'create' => CreateAgent::route('/create'),
            'edit' => EditAgent::route('/{record}/edit'),
        ];
    }
}
