<?php

namespace App\Filament\Agency\Resources\AgentResource\Pages;

use Filament\Facades\Filament;
use Exception;
use App\Filament\Agency\Resources\AgentResource;
use App\Models\Agent;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class CreateAgent extends CreateRecord
{
    protected static string $resource = AgentResource::class;

    /**
     * Handle the creation of a new agent record following Filament tenancy pattern
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Get the current tenant (agency) from Filament
            $agency = Filament::getTenant();
            
            if (!$agency) {
                throw new Exception('Cannot create agent: No agency context found.');
            }

            // Validate required user data
            if (!isset($data['name']) || !$data['name']) {
                throw new Exception('Agent name is required.');
            }
            
            if (!isset($data['email']) || !$data['email']) {
                throw new Exception('Agent email is required.');
            }

            // Check if user with this email already exists
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                throw new Exception('A user with this email already exists.');
            }

            // Create the user first
            $user = $this->createUserFromData($data);
            
            // Check if user is already an agent in this agency (just in case)
            $existingAgent = Agent::where('user_id', $user->id)
                                ->where('agency_id', $agency->id)
                                ->first();
            
            if ($existingAgent) {
                throw new Exception('User is already an agent in this agency.');
            }

            // Ensure user profile exists with comprehensive agent details
            $this->createOrUpdateUserProfile($user, $agency, $data);

            // Prepare agent data following the tenancy pattern
            $agentData = $this->prepareAgentData($data, $user, $agency);

            // Create the agent record using Filament tenancy pattern
            $record = new ($this->getModel())($agentData);

            // Handle Filament tenancy association
            if (
                static::getResource()::isScopedToTenant() &&
                ($tenant = Filament::getTenant())
            ) {
                $agent = $this->associateRecordWithTenant($record, $tenant);
            } else {
                $record->save();
                $agent = $record;
            }

            // Create many-to-many relationship between user and agency
            $this->createAgencyUserRelationship($user, $agency);

            // Update agency agent count
            $agency->increment('total_agents');

            // Send success notification
            Notification::make()
                ->title('Agent Created Successfully')
                ->body("Agent {$user->name} has been added to {$agency->name} and can now access the agency panel.")
                ->success()
                ->send();

            return $agent;
        });
    }

    /**
     * Customize the form data before creation
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure email is lowercase for consistency
        if (isset($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }
        
        // Clean up phone number format
        if (isset($data['phone'])) {
            $data['phone'] = preg_replace('/[^0-9+\-\(\)\s]/', '', $data['phone']);
        }
        
        // Clean up alternate phone number format
        if (isset($data['alternate_phone'])) {
            $data['alternate_phone'] = preg_replace('/[^0-9+\-\(\)\s]/', '', $data['alternate_phone']);
        }
        
        // Clean up emergency contact phone
        if (isset($data['emergency_contact_phone'])) {
            $data['emergency_contact_phone'] = preg_replace('/[^0-9+\-\(\)\s]/', '', $data['emergency_contact_phone']);
        }
        
        // Ensure name is properly formatted
        if (isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }
        
        // Clean up first and last names
        if (isset($data['first_name'])) {
            $data['first_name'] = trim($data['first_name']);
        }
        
        if (isset($data['last_name'])) {
            $data['last_name'] = trim($data['last_name']);
        }
        
        // Clean up emergency contact name
        if (isset($data['emergency_contact_name'])) {
            $data['emergency_contact_name'] = trim($data['emergency_contact_name']);
        }
        
        // Set default password if not provided
        if (empty($data['password'])) {
            $data['password'] = 'password123!';
        }
        
        // Validate and clean social media URLs
        $socialFields = ['linkedin_url', 'facebook_url', 'twitter_url', 'website_url'];
        foreach ($socialFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $url = trim($data[$field]);
                // Add https:// if no protocol is specified
                if (!preg_match('/^https?:\/\//', $url)) {
                    $data[$field] = 'https://' . $url;
                }
            }
        }
        
        // Ensure years_experience is not negative
        if (isset($data['years_experience']) && $data['years_experience'] < 0) {
            $data['years_experience'] = 0;
        }
        
        // Ensure commission_rate is within bounds
        if (isset($data['commission_rate'])) {
            $data['commission_rate'] = max(0, min(100, $data['commission_rate']));
        }
        
        // Clean up specializations text
        if (isset($data['specializations'])) {
            $data['specializations'] = trim($data['specializations']);
        }
        
        // Clean up bio text
        if (isset($data['bio'])) {
            $data['bio'] = trim($data['bio']);
        }
        
        // Clean up occupation text
        if (isset($data['occupation'])) {
            $data['occupation'] = trim($data['occupation']);
        }
        
        // Clean up license number
        if (isset($data['license_number'])) {
            $data['license_number'] = trim(strtoupper($data['license_number']));
        }
        
        // Clean up ID number (but don't expose it in logs)
        if (isset($data['id_number'])) {
            $data['id_number'] = trim($data['id_number']);
        }
        
        return $data;
    }

    /**
     * Handle actions after successful record creation
     */
    protected function afterCreate(): void
    {
        $agent = $this->record;
        $user = $agent->user;
        $agency = $agent->agency;

        // Send comprehensive welcome notification
        Notification::make()
            ->title('Welcome to the Team!')
            ->body("Agent {$user->name} has been successfully added to {$agency->name}. They can now access the agency panel and start managing properties.")
            ->success()
            ->duration(8000)
            ->send();

        // Additional setup tasks could include:
        // - Sending welcome email with login instructions
        // - Creating default agent settings
        // - Setting up agent dashboard preferences
        // - Logging the creation activity

        // Example: Log the creation activity (if you have an activity log system)
        // activity()
        //     ->causedBy(auth()->user())
        //     ->performedOn($agent)
        //     ->log("Agent {$user->name} was added to agency {$agency->name} with {$agent->years_experience} years of experience");
    }

    /**
     * Get the success notification title
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Agent created successfully!';
    }

    /**
     * Get the redirect URL after creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Create a new user from the form data
     */
    private function createUserFromData(array $data): User
    {
        // Extract user data from the form
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password'] ?? 'password123!'),
            'user_type' => 'agent',
            'email_verified_at' => now(), // Auto-verify agency-created users
        ];

        $user = User::create($userData);
        
        // Ensure agent role exists for this agency before assigning it
        $agency = Filament::getTenant();
        if ($agency) {
            $this->ensureAgentRoleExists($agency);
            
            // Set the team context and assign role
            setPermissionsTeamId($agency->id);
            $user->assignRole('agent');
        }
        
        return $user;
    }

    /**
     * Ensure the 'agent' role exists for the current agency
     * Creates the role with appropriate permissions if it doesn't exist
     */
    private function ensureAgentRoleExists($agency): void
    {
        // Set the team context for Spatie permissions
        setPermissionsTeamId($agency->id);
        
        // Check if the 'agent' role exists for this agency using agency_id
        $agentRole = Role::where('name', 'agent')
            ->where('guard_name', 'web')
            ->where('agency_id', $agency->id)
            ->first();

        if (!$agentRole) {
            try {
                // Get the basic agent permissions that should exist globally
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

                // Find existing global permissions (these should exist from Shield generation)
                $permissions = collect($agentPermissions)->map(function ($permissionName) {
                    return Permission::where('name', $permissionName)
                        ->where('guard_name', 'web')
                        ->first();
                })->filter(); // Remove any null permissions

                // Create the role using our agency_id column
                $agentRole = Role::create([
                    'name' => 'agent',
                    'guard_name' => 'web',
                    'agency_id' => $agency->id, // Use agency_id as we migrated the database
                ]);

                // Assign permissions to the role
                if ($permissions->isNotEmpty()) {
                    $agentRole->syncPermissions($permissions);
                }

                Log::info("Created 'agent' role for agency: {$agency->name} (ID: {$agency->id}) with " . $permissions->count() . " permissions");
                
            } catch (Exception $e) {
                Log::error("Failed to create agent role for agency {$agency->name}: " . $e->getMessage());
                
                // Try to find any existing agent role for this agency as fallback
                $agentRole = Role::where('name', 'agent')
                    ->where('agency_id', $agency->id)
                    ->first();
                    
                if (!$agentRole) {
                    // Last resort: try to find a global agent role
                    $agentRole = Role::where('name', 'agent')
                        ->where('guard_name', 'web')
                        ->whereNull('agency_id')
                        ->first();
                }
                
                if (!$agentRole) {
                    throw new Exception("Could not create or find agent role for agency: {$agency->name}. Error: " . $e->getMessage());
                }
                
                Log::info("Using fallback agent role for agency: {$agency->name}");
            }
        } else {
            Log::info("Found existing 'agent' role for agency: {$agency->name} (ID: {$agency->id})");
        }
    }

    /**
     * Estimate agent income based on years of experience
     * This provides a reasonable estimate for profile completion
     */
    private function estimateAgentIncome(int $yearsExperience): ?int
    {
        if ($yearsExperience <= 0) {
            return 50000; // Entry level agent
        } elseif ($yearsExperience <= 2) {
            return 65000; // Junior agent
        } elseif ($yearsExperience <= 5) {
            return 85000; // Mid-level agent
        } elseif ($yearsExperience <= 10) {
            return 120000; // Senior agent
        } else {
            return 150000; // Veteran agent
        }
    }

    /**
     * Create or update user profile with comprehensive agent details
     */
    private function createOrUpdateUserProfile(User $user, $agency, array $data): void
    {
        if (!$user->profile) {
            // Split the user's name into first and last name if not provided in form
            $firstName = $data['first_name'] ?? null;
            $lastName = $data['last_name'] ?? null;
            
            if (!$firstName || !$lastName) {
                $nameParts = explode(' ', $user->name, 2);
                $firstName = $firstName ?? ($nameParts[0] ?? '');
                $lastName = $lastName ?? ($nameParts[1] ?? '');
            }
            
            // Create comprehensive user profile for the agent
            $user->profile()->create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'bio' => $data['bio'] ?? 'Professional Real Estate Agent specializing in helping clients find their perfect property.',
                'occupation' => $data['occupation'] ?? 'Real Estate Agent',
                
                // Location information from agency
                'state_id' => $agency->state_id,
                'city_id' => $agency->city_id,
                'area_id' => $agency->area_id,
                'address' => is_array($agency->address) ? ($agency->address['street'] ?? null) : $agency->address,
                'postal_code' => is_array($agency->address) ? ($agency->address['postal_code'] ?? null) : null,
                
                // Contact information
                'alternate_phone' => $data['alternate_phone'] ?? $user->phone,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                
                // Professional details
                'annual_income' => $data['annual_income'] ?? $this->estimateAgentIncome($data['years_experience'] ?? 0),
                
                // Property preferences (as a professional agent)
                'preferred_property_types' => json_encode(['Residential', 'Commercial', 'Luxury']),
                'preferred_locations' => json_encode([$agency->state->name ?? 'Multiple Areas']),
                'preferred_features' => json_encode(['Modern Amenities', 'Good Location', 'Investment Potential']),
                
                // ID Verification
                'id_type' => $data['id_type'] ?? null,
                'id_number' => $data['id_number'] ?? null,
                'is_id_verified' => $data['is_id_verified'] ?? false,
                'id_verified_at' => ($data['is_id_verified'] ?? false) ? now() : null,
                
                // Social media
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'twitter_url' => $data['twitter_url'] ?? null,
                'facebook_url' => $data['facebook_url'] ?? null,
                'website_url' => $data['website_url'] ?? $agency->website ?? null,
                
                // Profile completion status
                'is_complete' => false, // Will be updated as agent fills more details
            ]);
        } else {
            // Update existing profile with comprehensive agent-specific information
            $profileUpdates = [];
            
            // Personal information
            if (isset($data['first_name']) && $data['first_name']) {
                $profileUpdates['first_name'] = $data['first_name'];
            }
            if (isset($data['last_name']) && $data['last_name']) {
                $profileUpdates['last_name'] = $data['last_name'];
            }
            if (isset($data['date_of_birth']) && $data['date_of_birth']) {
                $profileUpdates['date_of_birth'] = $data['date_of_birth'];
            }
            if (isset($data['gender']) && $data['gender']) {
                $profileUpdates['gender'] = $data['gender'];
            }
            
            // Basic professional information
            if (!$user->profile->occupation || isset($data['occupation'])) {
                $profileUpdates['occupation'] = $data['occupation'] ?? 'Real Estate Agent';
            }
            
            if (!$user->profile->bio || isset($data['bio'])) {
                $profileUpdates['bio'] = $data['bio'] ?? 'Professional Real Estate Agent specializing in helping clients find their perfect property.';
            }
            
            // Contact information
            if (isset($data['alternate_phone']) && $data['alternate_phone']) {
                $profileUpdates['alternate_phone'] = $data['alternate_phone'];
            } elseif (!$user->profile->alternate_phone && $user->phone) {
                $profileUpdates['alternate_phone'] = $user->phone;
            }
            
            if (isset($data['emergency_contact_name']) && $data['emergency_contact_name']) {
                $profileUpdates['emergency_contact_name'] = $data['emergency_contact_name'];
            }
            if (isset($data['emergency_contact_phone']) && $data['emergency_contact_phone']) {
                $profileUpdates['emergency_contact_phone'] = $data['emergency_contact_phone'];
            }
            
            // Location information if missing and agency has it
            if (!$user->profile->state_id && $agency->state_id) {
                $profileUpdates['state_id'] = $agency->state_id;
            }
            
            if (!$user->profile->city_id && $agency->city_id) {
                $profileUpdates['city_id'] = $agency->city_id;
            }
            
            if (!$user->profile->area_id && $agency->area_id) {
                $profileUpdates['area_id'] = $agency->area_id;
            }
            
            if (!$user->profile->address && $agency->address) {
                $profileUpdates['address'] = is_array($agency->address) ? ($agency->address['street'] ?? null) : $agency->address;
            }
            
            if (!$user->profile->postal_code && is_array($agency->address) && isset($agency->address['postal_code'])) {
                $profileUpdates['postal_code'] = $agency->address['postal_code'];
            }
            
            // Professional details
            if (isset($data['annual_income']) && $data['annual_income']) {
                $profileUpdates['annual_income'] = $data['annual_income'];
            } elseif (!$user->profile->annual_income && isset($data['years_experience'])) {
                $profileUpdates['annual_income'] = $this->estimateAgentIncome($data['years_experience']);
            }
            
            // ID Verification
            if (isset($data['id_type']) && $data['id_type']) {
                $profileUpdates['id_type'] = $data['id_type'];
            }
            if (isset($data['id_number']) && $data['id_number']) {
                $profileUpdates['id_number'] = $data['id_number'];
            }
            if (isset($data['is_id_verified'])) {
                $profileUpdates['is_id_verified'] = $data['is_id_verified'];
                if ($data['is_id_verified']) {
                    $profileUpdates['id_verified_at'] = now();
                }
            }
            
            // Social media URLs
            if (isset($data['linkedin_url']) && $data['linkedin_url']) {
                $profileUpdates['linkedin_url'] = $data['linkedin_url'];
            }
            if (isset($data['twitter_url']) && $data['twitter_url']) {
                $profileUpdates['twitter_url'] = $data['twitter_url'];
            }
            if (isset($data['facebook_url']) && $data['facebook_url']) {
                $profileUpdates['facebook_url'] = $data['facebook_url'];
            }
            if (isset($data['website_url']) && $data['website_url']) {
                $profileUpdates['website_url'] = $data['website_url'];
            } elseif (!$user->profile->website_url && $agency->website) {
                $profileUpdates['website_url'] = $agency->website;
            }
            
            // Property preferences for professional context
            if (!$user->profile->preferred_property_types) {
                $profileUpdates['preferred_property_types'] = json_encode(['Residential', 'Commercial', 'Luxury']);
            }
            
            if (!$user->profile->preferred_locations) {
                $profileUpdates['preferred_locations'] = json_encode([$agency->state->name ?? 'Multiple Areas']);
            }
            
            if (!$user->profile->preferred_features) {
                $profileUpdates['preferred_features'] = json_encode(['Modern Amenities', 'Good Location', 'Investment Potential']);
            }
            
            if (!empty($profileUpdates)) {
                $user->profile->update($profileUpdates);
            }
        }
    }

    /**
     * Prepare agent data for creation following Filament tenancy pattern
     */
    private function prepareAgentData(array $data, User $user, $agency): array
    {
        // Remove user and user profile related fields from form data and prepare agent data
        $agentData = collect($data)->except([
            // User fields
            'name', 'email', 'phone', 'password',
            
            // User profile fields
            'first_name', 'last_name', 'date_of_birth', 'gender', 'bio', 'occupation',
            'alternate_phone', 'emergency_contact_name', 'emergency_contact_phone',
            'annual_income', 'id_type', 'id_number', 'is_id_verified',
            'linkedin_url', 'twitter_url', 'facebook_url', 'website_url'
        ])->toArray();
        
        // Set required relationships
        $agentData['user_id'] = $user->id;
        $agentData['agency_id'] = $agency->id;
        
        // Set default values for new agents and enhance with calculated fields
        $agentData = array_merge([
            'is_available' => true,
            'is_verified' => false,
            'is_featured' => false,
            'accepts_new_clients' => true,
            'last_active_at' => now(),
            'total_properties' => 0,
            'active_listings' => 0,
            'properties_sold' => 0,
            'properties_rented' => 0,
            'total_reviews' => 0,
            'rating' => 0.0,
            
            // Professional details with smart defaults
            'years_experience' => $data['years_experience'] ?? 0,
            'commission_rate' => $data['commission_rate'] ?? 3.0, // Default 3% commission
            'languages' => $data['languages'] ?? ['English'], // Default to English
            'specializations' => $data['specializations'] ?? 'Residential Properties, First-time Buyers',
            
            // Additional professional fields
            'phone' => $user->phone,
            'email' => $user->email,
            'social_media' => json_encode([
                'linkedin' => $data['linkedin_url'] ?? null,
                'facebook' => $data['facebook_url'] ?? null,
                'twitter' => $data['twitter_url'] ?? null,
                'instagram' => null,
                'website' => $data['website_url'] ?? null,
            ]),
        ], $agentData);

        return $agentData;
    }

    /**
     * Create agency-user relationship in pivot table
     */
    private function createAgencyUserRelationship(User $user, $agency): void
    {
        // Check if relationship already exists to avoid duplicates
        if (!$user->agencies()->where('agency_id', $agency->id)->exists()) {
            $user->agencies()->attach($agency->id, [
                'role' => 'agent',
                'is_active' => true,
                'permissions' => json_encode([
                    'manage_properties', 
                    'manage_clients', 
                    'view_reports', 
                    'schedule_viewings',
                    'manage_leads'
                ]),
                'joined_at' => now(),
            ]);
        }
    }
}
