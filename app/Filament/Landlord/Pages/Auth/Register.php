<?php

namespace App\Filament\Landlord\Pages\Auth;

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
use App\Models\PropertyOwner;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\ListingPackage;
use App\Models\LeaseTemplate;
use App\Models\SalesAgreementTemplate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\ListingCreditService;
use App\Services\AdminRegistrationNotifier;

class Register extends \Filament\Auth\Pages\Register
{
    protected string $view = 'filament.landlord.pages.auth.register';

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
                                    ->description('Create your landlord account login details')
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
                        
                        Tab::make('Property Owner')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Section::make('Property Owner Information')
                                    ->description('Tell us about yourself as a property owner')
                                    ->schema([
                                        Select::make('owner_type')
                                            ->label('Owner Type')
                                            ->required()
                                            ->options([
                                                'individual' => 'Individual Property Owner',
                                                'company' => 'Company/Corporation',
                                                'trust' => 'Trust/Estate',
                                                'government' => 'Government Entity',
                                            ])
                                            ->live()
                                            ->columnSpanFull(),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(20)
                                                    ->columnSpan(1),
                                                
                                                TextInput::make('company_name')
                                                    ->label('Company/Organization Name')
                                                    ->maxLength(255)
                                                    ->visible(fn ($get) => in_array($get('owner_type'), ['company', 'trust', 'government']))
                                                    ->required(fn ($get) => in_array($get('owner_type'), ['company', 'trust', 'government']))
                                                    ->columnSpan(2),
                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('tax_id')
                                                    ->label('Tax ID/BVN')
                                                    ->maxLength(50)
                                                    ->helperText('Business registration number or BVN for individuals')
                                                    ->columnSpan(1),

                                                Select::make('property_types')
                                                    ->label('Property Types You Own')
                                                    ->multiple()
                                                    ->required()
                                                    ->options([
                                                        'residential' => 'Residential Properties',
                                                        'commercial' => 'Commercial Properties',
                                                        'industrial' => 'Industrial Properties',
                                                        'land' => 'Land/Plots',
                                                        'mixed_use' => 'Mixed-Use Properties',
                                                    ])
                                                    ->searchable()
                                                    ->helperText('Select the types of properties you own')
                                                    ->columnSpan(2),
                                            ]),

                                        Textarea::make('description')
                                            ->label('Property Portfolio Description')
                                            ->required()
                                            ->maxLength(500)
                                            ->rows(4)
                                            ->placeholder('Brief description of your property portfolio, experience as a landlord, and your investment goals...')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tab::make('Location')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Property Locations')
                                    ->description('Where are your properties located?')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('address')
                                                    ->label('Primary Address')
                                                    ->maxLength(500)
                                                    ->columnSpan(2),
                                                
                                                TextInput::make('city')
                                                    ->label('City')
                                                    ->maxLength(100)
                                                    ->columnSpan(1),
                                            ]),

                                        Select::make('property_locations')
                                            ->label('Property Locations')
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
                                            ->helperText('Select the areas where your properties are located')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact(),
                            ]),
                        
                        Tab::make('Preferences')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Management Preferences')
                                    ->description('How do you prefer to manage your properties?')
                                    ->schema([
                                        Select::make('management_style')
                                            ->label('Management Style')
                                            ->required()
                                            ->options([
                                                'self_managed' => 'Self-Managed',
                                                'agent_assisted' => 'Agent Assisted',
                                                'fully_managed' => 'Fully Managed by Agent/Agency',
                                            ])
                                            ->helperText('How hands-on do you want to be?')
                                            ->columnSpanFull(),

                                        Select::make('tenant_preferences')
                                            ->label('Tenant Preferences')
                                            ->multiple()
                                            ->options([
                                                'families' => 'Families',
                                                'professionals' => 'Young Professionals',
                                                'students' => 'Students',
                                                'corporate' => 'Corporate Tenants',
                                                'expats' => 'Expatriates',
                                                'any' => 'Any Qualified Tenant',
                                            ])
                                            ->searchable()
                                            ->helperText('What type of tenants do you prefer?')
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
     * Handle the registration and create landlord profile
     */
    protected function handleRegistration(array $data): Model
    {
        Log::info('=== Landlord Registration Started ===', [
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
            'user_type' => 'property_owner',
        ]);
        
        Log::info('User created successfully', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        // Create PropertyOwner record for the landlord
        $this->createPropertyOwnerRecord($user, $data);

        SalesAgreementTemplate::ensureDefaultForLandlord($user->id);
        LeaseTemplate::ensureDefaultForLandlord($user->id);

        // Assign Landlord role and permissions
        $this->assignLandlordRole($user);

        $this->grantStarterPackage($user);

        // Initialize profile completion tracking for landlords.
        $user->initializeProfileCompletion();

        AdminRegistrationNotifier::notify($user);

        Log::info('Landlord registration completed successfully', [
            'user_id' => $user->id,
        ]);

        return $user;
    }

    /**
     * Create PropertyOwner record for the landlord
     */
    private function createPropertyOwnerRecord(User $user, array $data): PropertyOwner
    {
        $propertyOwnerData = [
            'user_id' => $user->id,
            'type' => $data['owner_type'] ?? 'individual',
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        // Handle name fields based on owner type
        if (($data['owner_type'] ?? 'individual') === 'individual') {
            // Split the name for individual owners
            $nameParts = explode(' ', $user->name, 2);
            $propertyOwnerData['first_name'] = $nameParts[0];
            $propertyOwnerData['last_name'] = $nameParts[1] ?? '';
        } else {
            // For company/trust/government, use company name
            $propertyOwnerData['company_name'] = $data['company_name'] ?? $user->name;
        }

        // Add optional fields if provided
        if (!empty($data['tax_id'])) {
            $propertyOwnerData['tax_id'] = $data['tax_id'];
        }

        if (!empty($data['address'])) {
            $propertyOwnerData['address'] = $data['address'];
        }

        if (!empty($data['city'])) {
            $propertyOwnerData['city'] = $data['city'];
        }

        if (!empty($data['description'])) {
            $propertyOwnerData['notes'] = $data['description'];
        }

        $propertyOwner = PropertyOwner::create($propertyOwnerData);

        Log::info('PropertyOwner record created', [
            'property_owner_id' => $propertyOwner->id,
            'user_id' => $user->id,
            'type' => $propertyOwner->type,
        ]);

        return $propertyOwner;
    }

    /**
     * Assign Landlord role with appropriate permissions
     */
    private function assignLandlordRole(User $user): void
    {
        try {
            // Get the Landlord role
            $role = Role::where('name', 'landlord')
                ->where('guard_name', 'web')
                ->first();
            
            if ($role) {
                // Assign the role to the user
                $user->assignRole($role);
                Log::info("Assigned Landlord role to user: {$user->email}");
            } else {
                Log::error("Landlord role not found. Please run LandlordRoleSeeder.");
            }
            
        } catch (Exception $e) {
            Log::error("Failed to assign Landlord role to user {$user->email}: " . $e->getMessage());
            // Don't fail registration if role assignment fails
        }
    }

    private function grantStarterPackage(User $user): void
    {
        $starterPackage = ListingPackage::where('slug', 'starter')->where('is_active', true)->first();

        if (!$starterPackage) {
            Log::warning('Starter package not found for landlord registration.', [
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
