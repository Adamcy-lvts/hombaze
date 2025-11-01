<?php

namespace App\Filament\Agency\Pages\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Auth\Register as BaseRegister;
use App\Models\User;
use App\Models\Agency;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;

class Register extends BaseRegister
{
    use WithRateLimiting;
    
    protected static string $view = 'filament.agency.pages.auth.register';

    public ?array $data = [];

    public static function getLabel(): string
    {
        return 'Register Agency';
    }

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->callHook('beforeFill');
        $this->form->fill();
        $this->callHook('afterFill');
    }

    /**
     * Override register method to handle tenant registration properly
     */
    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);

            $this->callHook('beforeValidate');
            $data = $this->form->getState();
            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            DB::beginTransaction();
            try {
                $this->callHook('beforeRegister');

                // Create user first
                $user = $this->createAgencyOwner($data);

                // Create agency with owner_id
                $agency = $this->createAgency($data, $user->id);

                // Associate user with agency
                $agency->users()->attach($user->id, [
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);

                // Update user type
                $user->update(['user_type' => 'agency_owner']);

                // Create agent profile for the agency owner
                $agent = $this->createAgentProfile($user, $agency);

                // Assign agency owner role
                $this->assignAgencyOwnerRole($user, $agency);

                $this->callHook('afterRegister');

                DB::commit();

                // Handle post-registration tasks
                event(new Registered($user));
                $this->sendEmailVerificationNotification($user);
                
                // Login user with tenant context
                Filament::auth()->login($user);
                session()->regenerate();

                // Show success notification
                Notification::make()
                    ->title('Welcome to HomeBaze!')
                    ->body('Your agency has been successfully registered.')
                    ->success()
                    ->send();

                // Redirect to agency dashboard with proper tenant context
                return $this->redirectToAgencyDashboard($user, $agency);

            } catch (\Exception $e) {
                Log::error('Agency registration error:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $data
                ]);
                DB::rollBack();
                
                // Clean up uploaded files if any
                if (isset($data['logo'])) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($data['logo']);
                }
                throw $e;
            }

        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(__('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->danger()
                ->send();

            return null;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Registration failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Agency Registration')
                    ->tabs([
                        Tabs\Tab::make('Super Admin Details')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Agency Super Admin Information')
                                    ->description('Personal details for the agency Super Admin account')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('owner_name')
                                                    ->label('Full Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Enter your full name')
                                                    ->columnSpanFull(),
                                            ]),
                                        
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('owner_email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->required()
                                                    ->unique('users', 'email')
                                                    ->placeholder('your.email@example.com')
                                                    ->helperText('This will be your login email')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('owner_phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->required()
                                                    ->placeholder('Enter your phone number')
                                                    ->columnSpan(1),
                                            ]),
                                        
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('owner_password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->required()
                                                    ->revealable()
                                                    ->rule(Password::default())
                                                    ->placeholder('Create a secure password')
                                                    ->helperText('Minimum 8 characters')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('owner_password_confirmation')
                                                    ->label('Confirm Password')
                                                    ->password()
                                                    ->required()
                                                    ->revealable()
                                                    ->same('owner_password')
                                                    ->placeholder('Confirm your password')
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tabs\Tab::make('Agency Details')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Agency Information')
                                    ->description('Basic details about your real estate agency')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Agency Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Enter your agency name')
                                                    ->helperText('Your agency\'s official business name')
                                                    ->columnSpanFull(),
                                                    
                                                TextInput::make('email')
                                                    ->label('Business Email')
                                                    ->email()
                                                    ->required()
                                                    ->placeholder('info@youragency.com')
                                                    ->helperText('Primary business email address')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->required()
                                                    ->placeholder('Enter phone number')
                                                    ->helperText('Primary business phone number')
                                                    ->columnSpan(1),
                                            ]),
                                        
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('license_number')
                                                    ->label('Business License Number')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter business license number')
                                                    ->helperText('Official business/real estate license number')
                                                    ->columnSpan(1),
                                                    
                                                DatePicker::make('license_expiry_date')
                                                    ->label('License Expiry Date')
                                                    ->placeholder('Select expiry date')
                                                    ->helperText('When does your license expire?')
                                                    ->columnSpan(1),
                                            ]),

                                        TextInput::make('website')
                                            ->label('Website URL')
                                            ->url()
                                            ->placeholder('https://www.youragency.com')
                                            ->helperText('Your agency website (optional)')
                                            ->columnSpanFull(),

                                        Textarea::make('description')
                                            ->label('Agency Description')
                                            ->required()
                                            ->rows(4)
                                            ->maxLength(1000)
                                            ->placeholder('Describe your agency, services, and specializations...')
                                            ->helperText('Brief description of your agency and what makes you unique')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tabs\Tab::make('Location')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Agency Location')
                                    ->description('Where is your agency located?')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('state_id')
                                                    ->label('State')
                                                    ->options(State::pluck('name', 'id'))
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                                                    ->columnSpan(1),
                                                    
                                                Select::make('city_id')
                                                    ->label('City')
                                                    ->options(fn (Get $get): array => City::query()
                                                        ->where('state_id', $get('state_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set) => $set('area_id', null))
                                                    ->columnSpan(1),
                                                    
                                                Select::make('area_id')
                                                    ->label('Area')
                                                    ->options(fn (Get $get): array => Area::query()
                                                        ->where('city_id', $get('city_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(1),
                                            ]),
                                        
                                        Textarea::make('address')
                                            ->label('Street Address')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('Enter complete street address')
                                            ->helperText('Full street address including building number and street name')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tabs\Tab::make('Business Details')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Business Information')
                                    ->description('Additional business details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('years_in_business')
                                                    ->label('Years in Business')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->placeholder('5')
                                                    ->helperText('How many years has your agency been operating?')
                                                    ->columnSpan(1),
                                                    
                                                Select::make('specializations')
                                                    ->label('Specializations')
                                                    ->multiple()
                                                    ->options([
                                                        'residential_sales' => 'Residential Sales',
                                                        'residential_rentals' => 'Residential Rentals',
                                                        'commercial_sales' => 'Commercial Sales',
                                                        'commercial_rentals' => 'Commercial Rentals',
                                                        'land_sales' => 'Land Sales',
                                                        'luxury_properties' => 'Luxury Properties',
                                                        'affordable_housing' => 'Affordable Housing',
                                                        'property_management' => 'Property Management',
                                                        'investment_properties' => 'Investment Properties',
                                                    ])
                                                    ->searchable()
                                                    ->helperText('Select your agency\'s areas of expertise')
                                                    ->columnSpan(1),
                                            ]),
                                        
                                        FileUpload::make('logo')
                                            ->label('Agency Logo')
                                            ->image()
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '1:1',
                                                '16:9',
                                                '4:3',
                                            ])
                                            ->directory('agency-logos')
                                            ->visibility('public')
                                            ->helperText('Upload your agency logo (JPG, PNG, SVG)')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tabs\Tab::make('Online Presence')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Social Media & Online Presence')
                                    ->description('Your agency\'s online presence')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('facebook_url')
                                                    ->label('Facebook Page')
                                                    ->url()
                                                    ->placeholder('https://facebook.com/youragency')
                                                    ->helperText('Facebook business page URL (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('twitter_url')
                                                    ->label('Twitter/X Profile')
                                                    ->url()
                                                    ->placeholder('https://twitter.com/youragency')
                                                    ->helperText('Twitter/X profile URL (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('linkedin_url')
                                                    ->label('LinkedIn Company Page')
                                                    ->url()
                                                    ->placeholder('https://linkedin.com/company/youragency')
                                                    ->helperText('LinkedIn company page URL (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('instagram_url')
                                                    ->label('Instagram Profile')
                                                    ->url()
                                                    ->placeholder('https://instagram.com/youragency')
                                                    ->helperText('Instagram business profile URL (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->compact(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->contained(false),
            ]);
    }

    /**
     * Create the agency
     */
    protected function createAgency(array $data, int $ownerId): Agency
    {
        // Prepare social media data
        $socialMedia = [];
        if (!empty($data['facebook_url'])) {
            $socialMedia['facebook'] = $data['facebook_url'];
        }
        if (!empty($data['twitter_url'])) {
            $socialMedia['twitter'] = $data['twitter_url'];
        }
        if (!empty($data['linkedin_url'])) {
            $socialMedia['linkedin'] = $data['linkedin_url'];
        }
        if (!empty($data['instagram_url'])) {
            $socialMedia['instagram'] = $data['instagram_url'];
        }

        // Prepare specializations as comma-separated string
        $specializations = '';
        if (!empty($data['specializations']) && is_array($data['specializations'])) {
            $specializations = implode(',', $data['specializations']);
        }

        // Prepare address as array for JSON storage
        $address = [
            'street' => $data['address'] ?? '',
            'city_id' => $data['city_id'],
            'state_id' => $data['state_id'],
            'area_id' => $data['area_id'] ?? null,
        ];

        $agency = Agency::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'license_number' => $data['license_number'] ?? null,
            'license_expiry_date' => $data['license_expiry_date'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'website' => $data['website'] ?? null,
            'address' => $address,
            'logo' => $data['logo'] ?? null,
            'social_media' => $socialMedia,
            'specializations' => $specializations,
            'years_in_business' => $data['years_in_business'] ?? 0,
            'rating' => 0.0,
            'total_reviews' => 0,
            'total_properties' => 0,
            'total_agents' => 1, // The owner counts as the first agent
            'is_verified' => false,
            'is_featured' => false,
            'is_active' => true,
            'owner_id' => $ownerId,
            'state_id' => $data['state_id'],
            'city_id' => $data['city_id'],
            'area_id' => $data['area_id'] ?? null,
        ]);

        Log::info('Agency created successfully', [
            'agency_id' => $agency->id,
            'agency_name' => $agency->name,
        ]);

        return $agency;
    }

    /**
     * Create the agency owner user
     */
    protected function createAgencyOwner(array $data): User
    {
        $user = User::create([
            'name' => $data['owner_name'],
            'email' => $data['owner_email'],
            'phone' => $data['owner_phone'],
            'password' => Hash::make($data['owner_password']),
            'user_type' => 'agency_owner',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Log::info('Agency owner user created', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);

        return $user;
    }

    /**
     * Create agent profile for the agency owner
     */
    protected function createAgentProfile(User $user, Agency $agency): \App\Models\Agent
    {
        $agent = \App\Models\Agent::create([
            'user_id' => $user->id,
            'agency_id' => $agency->id,
            'first_name' => explode(' ', $user->name)[0],
            'last_name' => implode(' ', array_slice(explode(' ', $user->name), 1)) ?: explode(' ', $user->name)[0],
            'email' => $user->email,
            'phone' => $user->phone,
            'bio' => "Agency owner and super admin for {$agency->name}.",
            'specializations' => $agency->specializations,
            'years_experience' => $agency->years_in_business,
            'languages' => 'English',
            'is_verified' => true,
            'is_featured' => true,
            'is_active' => true,
            'state_id' => $agency->state_id,
            'city_id' => $agency->city_id,
            'area_id' => $agency->area_id,
        ]);

        Log::info('Agent profile created for agency owner', [
            'agent_id' => $agent->id,
            'user_id' => $user->id,
            'agency_id' => $agency->id,
        ]);

        return $agent;
    }

    /**
     * Assign super_admin role to agency owner with appropriate permissions
     */
    protected function assignAgencyOwnerRole(User $user, Agency $agency): void
    {
        try {
            // Use Artisan command to create super admin with all permissions
            Artisan::call('shield:super-admin', [
                '--user' => $user->id,
                '--tenant' => $agency->id
            ]);

            // Get the super admin and give all permissions
            $superAdmin = $agency->getSuperAdmin();
            if ($superAdmin) {
                $superAdmin->givePermissionTo(Permission::all());
                Log::info("Assigned super_admin role with all permissions to agency owner: {$user->email} for agency: {$agency->name}");
            } else {
                Log::warning("Could not find super admin for agency: {$agency->name}");
            }

            // Create agent role for this agency
            $this->ensureAgentRoleExists($agency);
            
        } catch (\Exception $e) {
            Log::error("Failed to assign super_admin role to agency owner {$user->email}: " . $e->getMessage());
            // Don't fail registration if role assignment fails
        }
    }

    /**
     * Create the 'agent' role for the new agency with appropriate permissions
     */
    protected function ensureAgentRoleExists(Agency $agency): void
    {
        try {
            // Define agent permissions
            $agentPermissions = [
                // Property permissions (limited)
                'view_property', 'view_any_property', 'create_property', 'update_property',
                // Property inquiry permissions
                'view_property::inquiry', 'view_any_property::inquiry', 'create_property::inquiry', 'update_property::inquiry',
                // Property viewing permissions
                'view_property::viewing', 'view_any_property::viewing', 'create_property::viewing', 'update_property::viewing',
                // Dashboard access
                'page_AgencyDashboard', 'widget_AgencyStatsWidget', 'widget_PropertiesChartWidget',
                // Basic tenant menu access
                'view_tenant_menu'
            ];

            // Create role with team_id (similar to school approach)
            $agentRole = Role::firstOrCreate([
                'name' => 'agent',
                'guard_name' => 'web',
                'agency_id' => $agency->id // Use team_id for multi-tenancy like the school example
            ]);

            // Get existing permissions and assign them to role
            $permissionModels = Permission::whereIn('name', $agentPermissions)->get();
            
            $agentRole->givePermissionTo($permissionModels);

            Log::info("Created 'agent' role for agency: {$agency->name} (ID: {$agency->id}) with " . count($agentPermissions) . " permissions");
            
        } catch (\Exception $e) {
            Log::error("Failed to create agent role for agency {$agency->name}: " . $e->getMessage());
            throw $e; // Re-throw since this is critical for a new agency
        }
    }

    /**
     * Get the login action for the registration form
     */
    public function loginAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('login')
            ->link()
            ->label(__('filament-panels::pages/auth/register.actions.login.label'))
            ->url(filament()->getLoginUrl());
    }

    /**
     * Redirect to agency dashboard with proper tenant context
     */
    protected function redirectToAgencyDashboard(User $user, Agency $agency)
    {
        // Use Livewire redirect for Filament compatibility
        return $this->redirect(
            route('filament.agency.pages.agency-dashboard', ['tenant' => $agency]),
            navigate: true
        );
    }
}
