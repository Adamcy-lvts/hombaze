<?php

namespace App\Filament\Tenant\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use App\Models\User;
use App\Models\Tenant;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertyType;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Register extends \Filament\Auth\Pages\Register
{
    protected string $view = 'filament.tenant.pages.auth.register';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Registration')
                    ->tabs([
                        Tab::make('Account')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Login Credentials')
                                    ->description('Create your tenant account login details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                $this->getNameFormComponent()
                                                    ->columnSpan(1),
                                                $this->getEmailFormComponent()
                                                    ->columnSpan(1),
                                            ]),
                                        
                                        Grid::make(2)
                                            ->schema([
                                                $this->getPasswordFormComponent()
                                                    ->columnSpan(1),
                                                $this->getPasswordConfirmationFormComponent()
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tab::make('Personal Information')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Section::make('Personal Details')
                                    ->description('Tell us about yourself')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                
                                                TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),

                                                TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(20)
                                                    ->columnSpan(1),
                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                DatePicker::make('date_of_birth')
                                                    ->label('Date of Birth')
                                                    ->maxDate(now()->subYears(18))
                                                    ->columnSpan(1),

                                                TextInput::make('nationality')
                                                    ->label('Nationality')
                                                    ->maxLength(255)
                                                    ->default('Nigerian')
                                                    ->columnSpan(1),

                                                Select::make('gender')
                                                    ->label('Gender')
                                                    ->options([
                                                        'male' => 'Male',
                                                        'female' => 'Female',
                                                        'other' => 'Other',
                                                    ])
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tab::make('Employment')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Employment Information')
                                    ->description('Tell us about your employment status')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('employment_status')
                                                    ->label('Employment Status')
                                                    ->required()
                                                    ->options([
                                                        'employed' => 'Employed',
                                                        'self_employed' => 'Self Employed',
                                                        'student' => 'Student',
                                                        'unemployed' => 'Unemployed',
                                                        'retired' => 'Retired',
                                                    ])
                                                    ->live()
                                                    ->columnSpan(1),

                                                TextInput::make('occupation')
                                                    ->label('Occupation')
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('employer_name')
                                                    ->label('Employer/Company Name')
                                                    ->maxLength(255)
                                                    ->visible(fn ($get) => in_array($get('employment_status'), ['employed', 'self_employed']))
                                                    ->columnSpan(1),

                                                TextInput::make('monthly_income')
                                                    ->label('Monthly Income')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->helperText('Optional - helps with property recommendations')
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->compact(),
                            ]),

                        Tab::make('Preferences')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Section::make('Property Preferences')
                                    ->description('What type of properties are you looking for?')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('budget_min')
                                                    ->label('Minimum Budget')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->helperText('Minimum rent you\'re willing to pay')
                                                    ->columnSpan(1),

                                                TextInput::make('budget_max')
                                                    ->label('Maximum Budget')
                                                    ->numeric()
                                                    ->prefix('₦')
                                                    ->helperText('Maximum rent you\'re willing to pay')
                                                    ->columnSpan(1),
                                            ]),

                                        Select::make('preferred_property_types')
                                            ->label('Preferred Property Types')
                                            ->multiple()
                                            ->options(function () {
                                                return PropertyType::active()->pluck('name', 'id');
                                            })
                                            ->searchable()
                                            ->helperText('What types of properties interest you?')
                                            ->columnSpanFull(),

                                        Grid::make(2)
                                            ->schema([
                                                Select::make('preferred_bedrooms_min')
                                                    ->label('Minimum Bedrooms')
                                                    ->options([
                                                        1 => '1 Bedroom',
                                                        2 => '2 Bedrooms',
                                                        3 => '3 Bedrooms',
                                                        4 => '4 Bedrooms',
                                                        5 => '5+ Bedrooms',
                                                    ])
                                                    ->columnSpan(1),

                                                Select::make('preferred_bedrooms_max')
                                                    ->label('Maximum Bedrooms')
                                                    ->options([
                                                        1 => '1 Bedroom',
                                                        2 => '2 Bedrooms',
                                                        3 => '3 Bedrooms',
                                                        4 => '4 Bedrooms',
                                                        5 => '5+ Bedrooms',
                                                    ])
                                                    ->columnSpan(1),
                                            ]),

                                        Select::make('preferred_locations')
                                            ->label('Preferred Locations')
                                            ->multiple()
                                            ->options(function () {
                                                return Area::join('cities', 'areas.city_id', '=', 'cities.id')
                                                    ->join('states', 'cities.state_id', '=', 'states.id')
                                                    ->select('areas.id', 'areas.name as area_name', 'cities.name as city_name', 'states.name as state_name')
                                                    ->get()
                                                    ->mapWithKeys(function ($area) {
                                                        return [$area->id => "{$area->area_name}, {$area->city_name}, {$area->state_name}"];
                                                    });
                                            })
                                            ->searchable()
                                            ->helperText('Areas where you\'d like to live')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),

                        Tab::make('Emergency Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Emergency Contact Information')
                                    ->description('Someone we can contact in case of emergency')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('emergency_contact_name')
                                                    ->label('Contact Name')
                                                    ->maxLength(255)
                                                    ->columnSpan(1),

                                                TextInput::make('emergency_contact_phone')
                                                    ->label('Contact Phone')
                                                    ->tel()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('emergency_contact_relationship')
                                                    ->label('Relationship')
                                                    ->maxLength(255)
                                                    ->placeholder('e.g., Parent, Sibling, Friend')
                                                    ->columnSpan(1),

                                                Select::make('identification_type')
                                                    ->label('ID Type')
                                                    ->options([
                                                        'national_id' => 'National ID',
                                                        'international_passport' => 'International Passport',
                                                        'drivers_license' => 'Driver\'s License',
                                                        'voters_card' => 'Voter\'s Card',
                                                        'nin' => 'NIN',
                                                    ])
                                                    ->columnSpan(1),
                                            ]),

                                        TextInput::make('identification_number')
                                            ->label('ID Number')
                                            ->maxLength(255)
                                            ->helperText('Optional - for verification purposes')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->contained(false),
            ]);
    }

    /**
     * Handle the registration and create tenant profile
     */
    protected function handleRegistration(array $data): Model
    {
        Log::info('=== Tenant Registration Started ===', [
            'email' => $data['email'],
            'name' => $data['name'],
        ]);
        
        // Create the user account
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'email_verified_at' => now(),
            'user_type' => 'tenant',
        ]);
        
        Log::info('User created successfully', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        // Create Tenant record
        $this->createTenantRecord($user, $data);

        // Create or update user profile
        $this->createUserProfile($user, $data);

        // Assign Tenant role and permissions
        $this->assignTenantRole($user);

        Log::info('Tenant registration completed successfully', [
            'user_id' => $user->id,
        ]);

        return $user;
    }

    /**
     * Create Tenant record for the user
     */
    private function createTenantRecord(User $user, array $data): Tenant
    {
        $tenantData = [
            'user_id' => $user->id,
            'first_name' => $data['first_name'] ?? $this->getFirstName($user->name),
            'last_name' => $data['last_name'] ?? $this->getLastName($user->name),
            'email' => $user->email,
            'phone' => $user->phone,
            'is_active' => true,
        ];

        // Add optional employment fields
        if (!empty($data['employment_status'])) {
            $tenantData['employment_status'] = $data['employment_status'];
        }

        if (!empty($data['employer_name'])) {
            $tenantData['employer_name'] = $data['employer_name'];
        }

        if (!empty($data['occupation'])) {
            $tenantData['occupation'] = $data['occupation'];
        }

        if (!empty($data['monthly_income'])) {
            $tenantData['monthly_income'] = $data['monthly_income'];
        }

        if (!empty($data['date_of_birth'])) {
            $tenantData['date_of_birth'] = $data['date_of_birth'];
        }

        if (!empty($data['nationality'])) {
            $tenantData['nationality'] = $data['nationality'];
        }

        if (!empty($data['identification_type'])) {
            $tenantData['identification_type'] = $data['identification_type'];
        }

        if (!empty($data['identification_number'])) {
            $tenantData['identification_number'] = $data['identification_number'];
        }

        if (!empty($data['emergency_contact_name'])) {
            $tenantData['emergency_contact_name'] = $data['emergency_contact_name'];
        }

        if (!empty($data['emergency_contact_phone'])) {
            $tenantData['emergency_contact_phone'] = $data['emergency_contact_phone'];
        }

        $tenant = Tenant::create($tenantData);

        Log::info('Tenant record created', [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
        ]);

        return $tenant;
    }

    /**
     * Create or update user profile with preferences
     */
    private function createUserProfile(User $user, array $data): void
    {
        $profileData = [
            'user_id' => $user->id,
            'first_name' => $data['first_name'] ?? $this->getFirstName($user->name),
            'last_name' => $data['last_name'] ?? $this->getLastName($user->name),
        ];

        // Add optional personal fields
        if (!empty($data['date_of_birth'])) {
            $profileData['date_of_birth'] = $data['date_of_birth'];
        }

        if (!empty($data['gender'])) {
            $profileData['gender'] = $data['gender'];
        }

        if (!empty($data['occupation'])) {
            $profileData['occupation'] = $data['occupation'];
        }

        if (!empty($data['monthly_income'])) {
            $profileData['annual_income'] = $data['monthly_income'] * 12;
        }

        if (!empty($data['emergency_contact_name'])) {
            $profileData['emergency_contact_name'] = $data['emergency_contact_name'];
        }

        if (!empty($data['emergency_contact_phone'])) {
            $profileData['emergency_contact_phone'] = $data['emergency_contact_phone'];
        }

        // Add property preferences
        if (!empty($data['budget_min'])) {
            $profileData['budget_min'] = $data['budget_min'];
        }

        if (!empty($data['budget_max'])) {
            $profileData['budget_max'] = $data['budget_max'];
        }

        if (!empty($data['preferred_property_types'])) {
            $profileData['preferred_property_types'] = $data['preferred_property_types'];
        }

        if (!empty($data['preferred_locations'])) {
            $profileData['preferred_locations'] = $data['preferred_locations'];
        }

        if (!empty($data['preferred_bedrooms_min'])) {
            $profileData['preferred_bedrooms_min'] = $data['preferred_bedrooms_min'];
        }

        if (!empty($data['preferred_bedrooms_max'])) {
            $profileData['preferred_bedrooms_max'] = $data['preferred_bedrooms_max'];
        }

        if (!empty($data['identification_type'])) {
            $profileData['id_type'] = $data['identification_type'];
        }

        if (!empty($data['identification_number'])) {
            $profileData['id_number'] = $data['identification_number'];
        }

        // Create or update profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        Log::info('User profile created/updated', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Assign Tenant role with appropriate permissions
     */
    private function assignTenantRole(User $user): void
    {
        try {
            // Get the Tenant role
            $role = Role::where('name', 'tenant')
                ->where('guard_name', 'web')
                ->first();
            
            if ($role) {
                // Assign the role to the user
                $user->assignRole($role);
                Log::info("Assigned Tenant role to user: {$user->email}");
            } else {
                Log::warning("Tenant role not found. User registered without role assignment.");
            }
            
        } catch (Exception $e) {
            Log::error("Failed to assign Tenant role to user {$user->email}: " . $e->getMessage());
            // Don't fail registration if role assignment fails
        }
    }

    /**
     * Extract first name from full name
     */
    private function getFirstName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName), 2);
        return $parts[0] ?? '';
    }

    /**
     * Extract last name from full name
     */
    private function getLastName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName), 2);
        return $parts[1] ?? '';
    }

    /**
     * Get the redirect URL after successful registration
     */
    protected function getRedirectUrl(): string
    {
        return $this->getPanel()->getUrl();
    }
}
