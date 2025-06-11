<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Agency;
use App\Models\Agent;
use App\Models\Property;
use App\Models\State;
use App\Models\City;
use App\Models\Area;

class Phase9MultiPanelUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Phase 9: Multi-Panel User Seeding...');

        // Create Super Admin
        $this->createSuperAdmin();

        // Create Agency Owners with their agencies
        $this->createAgencyOwners();

        // Create Agents with their profiles
        $this->createAgents();

        // Create Property Owners with their properties
        $this->createPropertyOwners();

        // Create additional Tenants
        $this->createTenants();

        $this->command->info('✅ Phase 9 Multi-Panel User Seeding completed successfully!');
        $this->displaySummary();
    }

    /**
     * Create Super Admin user
     */
    private function createSuperAdmin(): void
    {
        $this->command->info('👑 Creating Super Admin...');

        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@homebaze.com'],
            [
                'name' => 'HomeBaze Super Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'user_type' => 'super_admin',
                'phone' => '08012345678',
                'phone_verified_at' => now(),
                'is_verified' => true,
                'is_active' => true,
                'avatar' => 'https://ui-avatars.com/api/?name=Super+Admin&background=dc2626&color=fff',
                'last_login_at' => now()->subDays(rand(1, 7)),
                'preferences' => [
                    'notifications' => true,
                    'dark_mode' => false,
                    'language' => 'en',
                    'timezone' => 'Africa/Lagos',
                ],
            ]
        );

        // Create profile for super admin
        $this->createUserProfile($superAdmin, [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'occupation' => 'System Administrator',
            'bio' => 'System administrator with full access to all HomeBaze features and data.',
            'is_complete' => true,
        ]);

        $this->command->info('✅ Super Admin created: ' . $superAdmin->email);
    }

    /**
     * Create Agency Owners with their agencies
     */
    private function createAgencyOwners(): void
    {
        $this->command->info('🏢 Creating Agency Owners...');

        $agencyOwnerData = [
            [
                'name' => 'Adebayo Okonkwo',
                'email' => 'adebayo@premiumproperties.ng',
                'agency_name' => 'Premium Properties Nigeria',
                'phone' => '08023456789',
            ],
            [
                'name' => 'Fatima Al-Hassan',
                'email' => 'fatima@northernrealty.ng',
                'agency_name' => 'Northern Realty Group',
                'phone' => '08034567890',
            ],
            [
                'name' => 'Chinedu Okoro',
                'email' => 'chinedu@lagosproperties.ng',
                'agency_name' => 'Lagos Properties Limited',
                'phone' => '08045678901',
            ],
            [
                'name' => 'Aisha Bello',
                'email' => 'aisha@modernhomes.ng',
                'agency_name' => 'Modern Homes & Estates',
                'phone' => '08056789012',
            ],
            [
                'name' => 'Emeka Nwankwo',
                'email' => 'emeka@royaltyproperties.ng',
                'agency_name' => 'Royalty Properties',
                'phone' => '08067890123',
            ],
        ];

        foreach ($agencyOwnerData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('agency123'),
                    'user_type' => 'agency_owner',
                    'phone' => $data['phone'],
                    'phone_verified_at' => now(),
                    'is_verified' => true,
                    'is_active' => true,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($data['name']) . '&background=' . ['dc2626', '16a34a', '2563eb', 'ca8a04', '9333ea'][$index] . '&color=fff',
                    'last_login_at' => now()->subDays(rand(1, 14)),
                    'preferences' => [
                        'notifications' => true,
                        'dark_mode' => rand(0, 1) === 1,
                        'language' => 'en',
                        'timezone' => 'Africa/Lagos',
                    ],
                ]
            );

            // Create user profile
            $names = explode(' ', $data['name']);
            $this->createUserProfile($user, [
                'first_name' => $names[0],
                'last_name' => $names[1] ?? '',
                'occupation' => 'Real Estate Agency Owner',
                'bio' => 'Experienced real estate professional managing ' . $data['agency_name'] . ' with a focus on quality service and customer satisfaction.',
                'annual_income' => rand(5000000, 15000000),
                'is_complete' => true,
            ]);

            // Get random location data - ensure we have cities
            $statesWithCities = State::whereHas('cities')->get();
            $randomState = $statesWithCities->isNotEmpty() ? $statesWithCities->random() : State::first();
            $cities = City::where('state_id', $randomState->id)->get();
            $randomCity = $cities->isNotEmpty() ? $cities->random() : $cities->first();
            $areas = $randomCity ? Area::where('city_id', $randomCity->id)->get() : collect();
            $randomArea = $areas->isNotEmpty() ? $areas->random() : null;

            // Create agency for this owner
            $agency = Agency::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['agency_name'],
                    'slug' => Str::slug($data['agency_name']),
                    'phone' => $data['phone'],
                    'owner_id' => $user->id,
                    'license_number' => 'REA-' . strtoupper(substr(md5($data['agency_name']), 0, 8)),
                    'description' => 'A leading real estate agency specializing in residential and commercial properties across Nigeria.',
                    'is_verified' => true,
                    'is_active' => true,
                    'verified_at' => now(),
                    'years_in_business' => rand(5, 15),
                    'rating' => rand(35, 50) / 10,
                    'total_reviews' => rand(20, 100),
                    'total_properties' => rand(50, 500),
                    'total_agents' => rand(3, 15),
                    'website' => 'https://' . strtolower(str_replace(' ', '', $data['agency_name'])) . '.ng',
                    'social_media' => [
                        'facebook' => 'https://facebook.com/' . strtolower(str_replace(' ', '', $data['agency_name'])),
                        'instagram' => 'https://instagram.com/' . strtolower(str_replace(' ', '', $data['agency_name'])),
                        'twitter' => 'https://twitter.com/' . strtolower(str_replace(' ', '', $data['agency_name'])),
                    ],
                    'specializations' => 'residential,commercial,luxury_properties',
                    'address' => [
                        'street' => fake()->streetAddress(),
                        'city' => $randomCity?->name ?? 'Lagos',
                        'state' => $randomState->name,
                        'country' => 'Nigeria',
                        'postal_code' => rand(100000, 999999),
                    ],
                    'state_id' => $randomState->id,
                    'city_id' => $randomCity->id ?? $cities->first()->id,
                    'area_id' => $randomArea?->id,
                    'latitude' => fake()->latitude(6.0, 7.0), // Nigeria latitude range
                    'longitude' => fake()->longitude(3.0, 4.5), // Nigeria longitude range
                ]
            );

            // Create many-to-many relationship between user and agency
            $user->agencies()->syncWithoutDetaching([$agency->id => [
                'role' => 'owner',
                'is_active' => true,
                'permissions' => json_encode(['all']),
                'joined_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]]);

            $this->command->info('✅ Agency Owner created: ' . $user->email . ' (Agency: ' . $agency->name . ')');
        }
    }

    /**
     * Create Agents with their profiles
     */
    private function createAgents(): void
    {
        $this->command->info('👨‍💼 Creating Agents...');

        $agencies = Agency::all();
        $agentData = [
            ['name' => 'Kemi Adebayo', 'email' => 'kemi.adebayo@agent.homebaze.com', 'phone' => '08078901234'],
            ['name' => 'Ibrahim Musa', 'email' => 'ibrahim.musa@agent.homebaze.com', 'phone' => '08089012345'],
            ['name' => 'Grace Okafor', 'email' => 'grace.okafor@agent.homebaze.com', 'phone' => '08090123456'],
            ['name' => 'Yusuf Abdullahi', 'email' => 'yusuf.abdullahi@agent.homebaze.com', 'phone' => '08001234567'],
            ['name' => 'Blessing Eze', 'email' => 'blessing.eze@agent.homebaze.com', 'phone' => '08012345678'],
            ['name' => 'Tunde Adeyemi', 'email' => 'tunde.adeyemi@agent.homebaze.com', 'phone' => '08023456789'],
            ['name' => 'Amina Usman', 'email' => 'amina.usman@agent.homebaze.com', 'phone' => '08034567890'],
            ['name' => 'Chioma Nwosu', 'email' => 'chioma.nwosu@agent.homebaze.com', 'phone' => '08045678901'],
            ['name' => 'Sani Garba', 'email' => 'sani.garba@agent.homebaze.com', 'phone' => '08056789012'],
            ['name' => 'Funmi Ogundimu', 'email' => 'funmi.ogundimu@agent.homebaze.com', 'phone' => '08067890123'],
        ];

        foreach ($agentData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('agent123'),
                    'user_type' => 'agent',
                    'phone' => $data['phone'],
                    'phone_verified_at' => now(),
                    'is_verified' => true,
                    'is_active' => true,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($data['name']) . '&background=' . ['059669', 'dc2626', '2563eb', 'ca8a04', '9333ea'][$index % 5] . '&color=fff',
                    'last_login_at' => now()->subDays(rand(1, 7)),
                    'preferences' => [
                        'notifications' => true,
                        'dark_mode' => rand(0, 1) === 1,
                        'language' => 'en',
                        'timezone' => 'Africa/Lagos',
                    ],
                ]
            );

            // Create user profile
            $names = explode(' ', $data['name']);
            $this->createUserProfile($user, [
                'first_name' => $names[0],
                'last_name' => $names[1] ?? '',
                'occupation' => 'Real Estate Agent',
                'bio' => 'Professional real estate agent with expertise in property sales and rentals.',
                'annual_income' => rand(2000000, 8000000),
                'is_complete' => true,
            ]);

            // Create agent profile
            $agency = $agencies->random();
            Agent::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'agency_id' => $agency->id,
                    'license_number' => 'AGT-' . strtoupper(substr(md5($data['name']), 0, 8)),
                    'specializations' => collect(['residential', 'commercial', 'luxury', 'land', 'investment'])->random(),
                    'years_experience' => rand(2, 15),
                    'bio' => 'Dedicated real estate professional with ' . rand(2, 15) . ' years of experience in the Nigerian property market.',
                    'is_verified' => true,
                    'is_available' => true,
                    'rating' => rand(35, 50) / 10,
                    'total_reviews' => rand(10, 50),
                    'commission_rate' => rand(25, 75) / 10,
                    'languages' => json_encode(['English', 'Hausa', 'Yoruba', 'Igbo'][rand(0, 3)]),
                    'total_properties' => rand(30, 150),
                    'properties_sold' => rand(10, 100),
                    'properties_rented' => rand(20, 200),
                    'is_featured' => rand(0, 1) === 1,
                    'accepts_new_clients' => true,
                    'verified_at' => now(),
                    'last_active_at' => now()->subDays(rand(0, 7)),
                ]
            );

            // Create many-to-many relationship between user and agency  
            $user->agencies()->syncWithoutDetaching([$agency->id => [
                'role' => 'agent',
                'is_active' => true,
                'permissions' => json_encode(['manage_properties', 'manage_clients', 'view_reports']),
                'joined_at' => now()->subMonths(rand(1, 12)),
                'created_at' => now(),
                'updated_at' => now(),
            ]]);

            $this->command->info('✅ Agent created: ' . $user->email . ' (Agency: ' . $agency->name . ')');
        }
    }

    /**
     * Create Property Owners with their properties
     */
    private function createPropertyOwners(): void
    {
        $this->command->info('🏠 Creating Property Owners...');

        $propertyOwnerData = [
            ['name' => 'Alhaji Musa Dantata', 'email' => 'musa@propertyowner.ng', 'phone' => '08098765432'],
            ['name' => 'Mrs. Adunni Williams', 'email' => 'adunni@propertyowner.ng', 'phone' => '08087654321'],
            ['name' => 'Chief Emeka Okafor', 'email' => 'emeka@propertyowner.ng', 'phone' => '08076543210'],
            ['name' => 'Dr. Fatima Babangida', 'email' => 'fatima@propertyowner.ng', 'phone' => '08065432109'],
            ['name' => 'Engr. Tayo Adeyemi', 'email' => 'tayo@propertyowner.ng', 'phone' => '08054321098'],
            ['name' => 'Hajiya Amina Kano', 'email' => 'amina@propertyowner.ng', 'phone' => '08043210987'],
            ['name' => 'Mr. Chidi Okonkwo', 'email' => 'chidi@propertyowner.ng', 'phone' => '08032109876'],
            ['name' => 'Barr. Folake Adeola', 'email' => 'folake@propertyowner.ng', 'phone' => '08021098765'],
        ];

        foreach ($propertyOwnerData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('owner123'),
                    'user_type' => 'property_owner',
                    'phone' => $data['phone'],
                    'phone_verified_at' => now(),
                    'is_verified' => true,
                    'is_active' => true,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($data['name']) . '&background=' . ['7c3aed', 'dc2626', '059669', 'ca8a04', '2563eb'][$index % 5] . '&color=fff',
                    'last_login_at' => now()->subDays(rand(1, 10)),
                    'preferences' => [
                        'notifications' => true,
                        'dark_mode' => rand(0, 1) === 1,
                        'language' => 'en',
                        'timezone' => 'Africa/Lagos',
                    ],
                ]
            );

            // Create user profile
            $names = explode(' ', $data['name']);
            $firstName = end($names);
            $lastName = count($names) > 1 ? $names[count($names) - 2] : '';
            
            $this->createUserProfile($user, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'occupation' => ['Business Owner', 'Doctor', 'Engineer', 'Lawyer', 'Entrepreneur'][rand(0, 4)],
                'bio' => 'Property investor and owner with multiple real estate investments across Nigeria.',
                'annual_income' => rand(10000000, 50000000),
                'is_complete' => true,
            ]);

            $this->command->info('✅ Property Owner created: ' . $user->email);
        }
    }

    /**
     * Create additional Tenants
     */
    private function createTenants(): void
    {
        $this->command->info('🏠 Creating additional Tenants...');

        $tenantData = [
            ['name' => 'Adaora Okwu', 'email' => 'adaora@tenant.ng', 'phone' => '08111111111'],
            ['name' => 'Babatunde Olatunji', 'email' => 'babatunde@tenant.ng', 'phone' => '08122222222'],
            ['name' => 'Chinwe Agu', 'email' => 'chinwe@tenant.ng', 'phone' => '08133333333'],
            ['name' => 'Damilola Fashola', 'email' => 'damilola@tenant.ng', 'phone' => '08144444444'],
            ['name' => 'Esther Adamu', 'email' => 'esther@tenant.ng', 'phone' => '08155555555'],
            ['name' => 'Femi Ogundipe', 'email' => 'femi@tenant.ng', 'phone' => '08166666666'],
            ['name' => 'Gbemisola Adebayo', 'email' => 'gbemisola@tenant.ng', 'phone' => '08177777777'],
            ['name' => 'Hassan Umar', 'email' => 'hassan@tenant.ng', 'phone' => '08188888888'],
            ['name' => 'Ifeanyi Chukwu', 'email' => 'ifeanyi@tenant.ng', 'phone' => '08199999999'],
            ['name' => 'Joy Alozie', 'email' => 'joy@tenant.ng', 'phone' => '08200000000'],
        ];

        foreach ($tenantData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('tenant123'),
                    'user_type' => 'tenant',
                    'phone' => $data['phone'],
                    'phone_verified_at' => now(),
                    'is_verified' => true,
                    'is_active' => true,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($data['name']) . '&background=' . ['10b981', 'f59e0b', 'ef4444', '8b5cf6', '06b6d4'][$index % 5] . '&color=fff',
                    'last_login_at' => now()->subDays(rand(1, 5)),
                    'preferences' => [
                        'notifications' => true,
                        'dark_mode' => rand(0, 1) === 1,
                        'language' => 'en',
                        'timezone' => 'Africa/Lagos',
                    ],
                ]
            );

            // Create user profile
            $names = explode(' ', $data['name']);
            $this->createUserProfile($user, [
                'first_name' => $names[0],
                'last_name' => $names[1] ?? '',
                'occupation' => ['Software Developer', 'Teacher', 'Banker', 'Marketing Manager', 'Nurse', 'Accountant'][rand(0, 5)],
                'bio' => 'Looking for quality accommodation in safe and accessible neighborhoods.',
                'annual_income' => rand(1500000, 6000000),
                'budget_min' => rand(200000, 500000),
                'budget_max' => rand(800000, 2000000),
                'preferred_property_types' => ['apartment', 'duplex', 'bungalow'],
                'preferred_bedrooms_min' => rand(1, 2),
                'preferred_bedrooms_max' => rand(3, 4),
                'preferred_features' => ['parking', 'security', 'generator', 'water_storage'],
                'is_complete' => true,
            ]);

            $this->command->info('✅ Tenant created: ' . $user->email);
        }
    }

    /**
     * Create user profile
     */
    private function createUserProfile(User $user, array $additionalData = []): void
    {
        $statesWithCities = State::whereHas('cities')->get();
        $randomState = $statesWithCities->isNotEmpty() ? $statesWithCities->random() : State::first();
        $cities = City::where('state_id', $randomState->id)->get();
        $randomCity = $cities->isNotEmpty() ? $cities->random() : $cities->first();
        $areas = $randomCity ? Area::where('city_id', $randomCity->id)->get() : collect();
        $randomArea = $areas->isNotEmpty() ? $areas->random() : null;

        $baseData = [
            'user_id' => $user->id,
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'gender' => ['male', 'female'][rand(0, 1)],
            'state_id' => $randomState->id,
            'city_id' => $randomCity->id ?? $cities->first()->id,
            'area_id' => $randomArea?->id,
            'address' => fake()->address(),
            'postal_code' => rand(100000, 999999),
            'alternate_phone' => '0' . rand(700, 999) . rand(1000000, 9999999),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => '0' . rand(700, 999) . rand(1000000, 9999999),
            'id_type' => ['nin', 'bvn', 'passport', 'drivers_license'][rand(0, 3)],
            'id_number' => strtoupper(substr(md5($user->email), 0, 11)),
            'is_id_verified' => true,
            'id_verified_at' => now(),
            'preferred_locations' => [$randomCity?->name, $randomArea?->name],
        ];

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($baseData, $additionalData)
        );
    }

    /**
     * Display seeding summary
     */
    private function displaySummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 PHASE 9 MULTI-PANEL USER SEEDING SUMMARY');
        $this->command->info('════════════════════════════════════════════');
        
        $userCounts = User::select('user_type')
            ->selectRaw('count(*) as count')
            ->groupBy('user_type')
            ->get()
            ->pluck('count', 'user_type');

        foreach ($userCounts as $type => $count) {
            $icon = match($type) {
                'super_admin' => '👑',
                'agency_owner' => '🏢',
                'agent' => '👨‍💼',
                'property_owner' => '🏠',
                'tenant' => '🏠',
                default => '👤',
            };
            
            $this->command->info($icon . ' ' . ucwords(str_replace('_', ' ', $type)) . ': ' . $count);
        }

        $this->command->info('');
        $this->command->info('🔗 Related Data:');
        $this->command->info('🏢 Agencies: ' . Agency::count());
        $this->command->info('👨‍💼 Agent Profiles: ' . Agent::count());
        $this->command->info('📋 User Profiles: ' . UserProfile::count());
        $this->command->info('');
        $this->command->info('🔐 Default Passwords:');
        $this->command->info('• Super Admin: admin123');
        $this->command->info('• Agency Owners: agency123');
        $this->command->info('• Agents: agent123');
        $this->command->info('• Property Owners: owner123');
        $this->command->info('• Tenants: tenant123');
        $this->command->info('');
        $this->command->info('✅ All users are verified and active for multi-panel access!');
    }
}
