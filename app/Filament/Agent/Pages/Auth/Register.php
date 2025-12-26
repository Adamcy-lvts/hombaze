<?php

namespace App\Filament\Agent\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\User;
use App\Models\Agent;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\ListingPackage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\ListingCreditService;

class Register extends \Filament\Auth\Pages\Register
{
    protected string $view = 'filament.agent.pages.auth.register';

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
                                    ->description('Create your account login details')
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
                        
                        Tab::make('Professional')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Professional Information')
                                    ->description('Tell us about your real estate background')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(20)
                                                    ->columnSpan(1),
                                                
                                                TextInput::make('years_experience')
                                                    ->label('Years of Experience')
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(0)
                                                    ->maxValue(50)
                                                    ->columnSpan(1),
                                                
                                                TextInput::make('license_number')
                                                    ->label('License Number')
                                                    ->maxLength(100)
                                                    ->helperText('Optional - Real estate license')
                                                    ->columnSpan(1),
                                            ]),
                                        
                                        Select::make('specializations')
                                            ->label('Areas of Specialization')
                                            ->multiple()
                                            ->required()
                                            ->options([
                                                'residential_sales' => 'Residential Sales',
                                                'residential_rentals' => 'Residential Rentals',
                                                'commercial_sales' => 'Commercial Sales',
                                                'commercial_rentals' => 'Commercial Rentals',
                                                'land_sales' => 'Land Sales',
                                                'luxury_properties' => 'Luxury Properties',
                                                'affordable_housing' => 'Affordable Housing',
                                                'student_housing' => 'Student Housing',
                                            ])
                                            ->searchable()
                                            ->helperText('Select multiple specializations')
                                            ->columnSpanFull(),
                                        
                                        Textarea::make('bio')
                                            ->label('Professional Bio')
                                            ->required()
                                            ->maxLength(500)
                                            ->rows(4)
                                            ->placeholder('Brief description of your experience, expertise, and what makes you unique as a real estate agent...')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tab::make('Location')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Service Area')
                                    ->description('Where do you primarily operate?')
                                    ->schema([
                                        Select::make('service_areas')
                                            ->label('Service Areas')
                                            ->multiple()
                                            ->required()
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
                                            ->helperText('Select the areas where you provide services')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tab::make('Additional')
                            ->icon('heroicon-o-plus-circle')
                            ->schema([
                                Section::make('Optional Information')
                                    ->description('Additional details to enhance your profile')
                                    ->schema([
                                        Select::make('languages')
                                            ->label('Languages Spoken')
                                            ->multiple()
                                            ->options([
                                                'english' => 'English',
                                                'hausa' => 'Hausa',
                                                'yoruba' => 'Yoruba',
                                                'igbo' => 'Igbo',
                                                'pidgin' => 'Nigerian Pidgin',
                                                'fulfulde' => 'Fulfulde',
                                                'kanuri' => 'Kanuri',
                                                'tiv' => 'Tiv',
                                                'french' => 'French',
                                                'arabic' => 'Arabic',
                                            ])
                                            ->searchable()
                                            ->helperText('Select languages you can communicate in')
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
     * Handle the registration and create agent profile
     */
    protected function handleRegistration(array $data): Model
    {
        Log::info('=== Agent Registration Started ===', [
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
            'user_type' => 'agent',
        ]);
        
        Log::info('User created successfully', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        // Create the agent profile
        $agent = Agent::create([
            'user_id' => $user->id,
            'license_number' => $data['license_number'] ?? null,
            'bio' => $data['bio'],
            'years_experience' => $data['years_experience'],
            'specializations' => implode(',', $data['specializations']), // Convert array to comma-separated string
            'service_areas' => json_encode($data['service_areas'] ?? []), // Store as JSON array
            'languages' => json_encode($data['languages'] ?? ['english']), // Store as JSON array
            'is_available' => true,
            'is_verified' => false,
            'accepts_new_clients' => true,
        ]);
        
        Log::info('Agent profile created successfully', [
            'agent_id' => $agent->id,
            'agent_user_id' => $agent->user_id,
            'agent_bio' => $agent->bio,
        ]);

        // Assign Independent Agent role and permissions
        $this->assignIndependentAgentRole($user);

        $this->grantStarterPackage($user);

        // Initialize profile completion tracking
        $user->initializeProfileCompletion();

        Log::info('Agent registration completed successfully', [
            'user_id' => $user->id,
            'agent_id' => $agent->id,
        ]);

        return $user;
    }

    /**
     * Assign Independent Agent role with appropriate permissions
     */
    private function assignIndependentAgentRole(User $user): void
    {
        try {
            // Ensure the Independent Agent role exists
            $role = $this->ensureIndependentAgentRoleExists();
            
            // Assign the role to the user
            $user->assignRole($role);
            
            Log::info("Assigned Independent Agent role to user: {$user->email}");
            
        } catch (Exception $e) {
            Log::error("Failed to assign Independent Agent role to user {$user->email}: " . $e->getMessage());
            // Don't fail registration if role assignment fails
        }
    }

    /**
     * Ensure the Independent Agent role exists with proper permissions
     */
    private function ensureIndependentAgentRoleExists(): Role
    {
        // Check if the role already exists (using Spatie models)
        $role = Role::where('name', 'independent_agent')
            ->where('guard_name', 'web')
            ->first();

        if (!$role) {
            // Create the role using Spatie model
            $role = Role::create([
                'name' => 'independent_agent',
                'guard_name' => 'web',
            ]);

            // Define permissions for independent agents
            $independentAgentPermissions = [
                // Property permissions - full CRUD for their own properties
                'view_property',
                'view_any_property',
                'create_property',
                'update_property',
                'delete_property',
                
                // Property inquiry permissions
                'view_property::inquiry',
                'view_any_property::inquiry',
                'create_property::inquiry',
                'update_property::inquiry',
                'delete_property::inquiry',
                
                // Property viewing permissions
                'view_property::viewing',
                'view_any_property::viewing',
                'create_property::viewing',
                'update_property::viewing',
                'delete_property::viewing',
                
                // Review permissions
                'view_review',
                'view_any_review',
                'create_review',
                'update_review',
                'delete_review',
                
                // Dashboard and widget access
                'page_AgentDashboard',
                'widget_AgentStatsWidget',
                'widget_PropertiesChartWidget',
            ];

            // Get existing permissions and assign them to the role (using Spatie models)
            $permissions = collect($independentAgentPermissions)->map(function ($permissionName) {
                return Permission::where('name', $permissionName)
                    ->where('guard_name', 'web')
                    ->first();
            })->filter(); // Remove any null permissions

            if ($permissions->isNotEmpty()) {
                $role->syncPermissions($permissions);
                Log::info("Created Independent Agent role with " . $permissions->count() . " permissions");
            } else {
                Log::warning("No permissions found for Independent Agent role");
            }
        }

        return $role;
    }

    private function grantStarterPackage(User $user): void
    {
        $starterPackage = ListingPackage::where('slug', 'starter')->where('is_active', true)->first();

        if (!$starterPackage) {
            Log::warning('Starter package not found for agent registration.', [
                'user_id' => $user->id,
            ]);
            return;
        }

        ListingCreditService::grantPackage($user, $starterPackage, 'self_service_free');
    }

    /**
     * Get the redirect URL after successful registration
     */
    protected function getRedirectUrl(): string
    {
        return $this->getPanel()->getUrl();
    }
}
