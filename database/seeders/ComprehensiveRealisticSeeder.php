<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Agency;
use App\Models\Agent;
use App\Models\Property;
use App\Models\PropertyOwner;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Models\PropertyFeature;
use App\Models\Tenant;

class ComprehensiveRealisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏗️  Starting Comprehensive Realistic Seeding...');

        // Clean database first
        $this->cleanDatabase();

        // Seed foundation data first
        $this->seedFoundationData();

        // Seed realistic business data
        $this->seedAgenciesWithAgents();
        $this->seedIndependentAgents();
        $this->seedPropertyOwners();
        $this->seedTenantUsers();

        $this->command->info('✅ Comprehensive Realistic Seeding completed successfully!');
    }

    /**
     * Clean the database before seeding
     */
    private function cleanDatabase(): void
    {
        $this->command->info('🧹 Cleaning database...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clean in reverse dependency order - but keep foundation data
        DB::table('property_feature_property')->truncate();
        Property::truncate();
        Agent::truncate();
        DB::table('agency_user')->truncate();
        Agency::truncate();
        PropertyOwner::truncate();
        Tenant::truncate();

        // Clean users except super admin
        User::where('email', '!=', 'admin@homebaze.com')->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Database cleaned successfully!');
    }

    /**
     * Seed foundation data if not exists
     */
    private function seedFoundationData(): void
    {
        $this->command->info('📋 Ensuring foundation data exists...');

        // Check if we have basic data, if not seed it
        if (State::count() === 0) {
            $this->call(Phase1FoundationSeeder::class);
        }

        if (City::count() === 0) {
            $this->call(Phase2LocationSeeder::class);
        }

        if (PropertySubtype::count() === 0) {
            $this->call(Phase3PropertySubtypeSeeder::class);
        }

        $this->command->info('✅ Foundation data ready!');
    }

    /**
     * Create 2 agencies with 5 agents each, each agent with 2+ properties
     */
    private function seedAgenciesWithAgents(): void
    {
        $this->command->info('🏢 Creating agencies with agents...');

        $agencies = [
            [
                'name' => 'Northern Prime Properties',
                'description' => 'Leading real estate agency specializing in premium properties across Northern Nigeria',
                'email' => 'info@northernprime.ng',
                'phone' => '+234-803-123-4567',
                'website' => 'https://northernprime.ng',
                'license_number' => 'NPP-001-2024',
                'location' => ['state' => 'Kano', 'city' => 'Kano Metropolitan'],
                'specializations' => 'Residential,Commercial,Industrial',
            ],
            [
                'name' => 'Arewa Luxury Homes',
                'description' => 'Premium property consultancy serving the elite market in Northern Nigeria',
                'email' => 'contact@arewaluxury.com',
                'phone' => '+234-806-987-6543',
                'website' => 'https://arewaluxury.com',
                'license_number' => 'ALH-002-2024',
                'location' => ['state' => 'Kaduna', 'city' => 'Kaduna North'],
                'specializations' => 'Luxury Residential,Commercial,Investment',
            ]
        ];

        foreach ($agencies as $agencyData) {
            $agency = $this->createAgency($agencyData);
            $this->createAgentsForAgency($agency, 5);
        }

        $this->command->info('✅ Agencies and agents created successfully!');
    }

    /**
     * Create 5 independent agents with 5+ properties each
     */
    private function seedIndependentAgents(): void
    {
        $this->command->info('👤 Creating independent agents...');

        $independentAgents = [
            [
                'name' => 'Musa Ibrahim Ahmad',
                'email' => 'musa.ahmad@gmail.com',
                'phone' => '+234-812-345-6789',
                'location' => ['state' => 'Abuja Federal Capital Territory', 'city' => 'Maitama'],
                'specialization' => 'Luxury Properties',
            ],
            [
                'name' => 'Fatima Aliyu Bello',
                'email' => 'fatima.bello@yahoo.com',
                'phone' => '+234-815-567-8901',
                'location' => ['state' => 'Borno', 'city' => 'Maiduguri'],
                'specialization' => 'Residential & Commercial',
            ],
            [
                'name' => 'Abdullahi Musa Kano',
                'email' => 'abdullahi.kano@outlook.com',
                'phone' => '+234-818-234-5678',
                'location' => ['state' => 'Kano', 'city' => 'Kano Metropolitan'],
                'specialization' => 'Investment Properties',
            ],
            [
                'name' => 'Zainab Hassan Gombe',
                'email' => 'zainab.hassan@gmail.com',
                'phone' => '+234-817-876-5432',
                'location' => ['state' => 'Gombe', 'city' => 'Gombe'],
                'specialization' => 'Affordable Housing',
            ],
            [
                'name' => 'Umar Sani Bauchi',
                'email' => 'umar.bauchi@hotmail.com',
                'phone' => '+234-813-987-6543',
                'location' => ['state' => 'Bauchi', 'city' => 'Bauchi'],
                'specialization' => 'Land & Development',
            ]
        ];

        foreach ($independentAgents as $agentData) {
            $agent = $this->createIndependentAgent($agentData);
            $this->createPropertiesForAgent($agent, rand(5, 8));
        }

        $this->command->info('✅ Independent agents created successfully!');
    }

    /**
     * Create 5 property owners with multiple listings
     */
    private function seedPropertyOwners(): void
    {
        $this->command->info('🏡 Creating property owners...');

        $propertyOwners = [
            [
                'name' => 'Alhaji Muhammad Bello Yusuf',
                'email' => 'bello.yusuf@gmail.com',
                'phone' => '+234-803-456-7890',
                'location' => ['state' => 'Kaduna', 'city' => 'Kaduna North'],
                'type' => 'Individual Investor',
            ],
            [
                'name' => 'Dr. Amina Zakari',
                'email' => 'amina.zakari@yahoo.com',
                'phone' => '+234-806-234-5678',
                'location' => ['state' => 'Abuja Federal Capital Territory', 'city' => 'Wuse'],
                'type' => 'Professional',
            ],
            [
                'name' => 'Malam Sani Usman Katsina',
                'email' => 'sani.katsina@outlook.com',
                'phone' => '+234-814-567-8901',
                'location' => ['state' => 'Katsina', 'city' => 'Katsina'],
                'type' => 'Business Owner',
            ],
            [
                'name' => 'Hajiya Khadija Musa',
                'email' => 'khadija.musa@gmail.com',
                'phone' => '+234-809-876-5432',
                'location' => ['state' => 'Borno', 'city' => 'Maiduguri'],
                'type' => 'Landlord',
            ],
            [
                'name' => 'Engr. Ibrahim Gombe',
                'email' => 'ibrahim.gombe@engineer.com',
                'phone' => '+234-811-234-5678',
                'location' => ['state' => 'Gombe', 'city' => 'Gombe'],
                'type' => 'Engineer & Developer',
            ]
        ];

        foreach ($propertyOwners as $ownerData) {
            $owner = $this->createPropertyOwner($ownerData);
            $this->createPropertiesForOwner($owner, rand(3, 6));
        }

        $this->command->info('✅ Property owners created successfully!');
    }

    /**
     * Create tenant users
     */
    private function seedTenantUsers(): void
    {
        $this->command->info('👥 Creating tenant users...');

        $tenants = [
            [
                'name' => 'Ahmad Suleiman',
                'email' => 'ahmad.suleiman@gmail.com',
                'phone' => '+234-805-123-4567',
                'location' => ['state' => 'Kano', 'city' => 'Kano Metropolitan'],
            ],
            [
                'name' => 'Halima Yusuf',
                'email' => 'halima.yusuf@yahoo.com',
                'phone' => '+234-808-234-5678',
                'location' => ['state' => 'Kaduna', 'city' => 'Kaduna South'],
            ],
            [
                'name' => 'Bashir Abdullahi',
                'email' => 'bashir.abdullahi@outlook.com',
                'phone' => '+234-812-345-6789',
                'location' => ['state' => 'Abuja Federal Capital Territory', 'city' => 'Garki'],
            ],
            [
                'name' => 'Aisha Muhammad',
                'email' => 'aisha.muhammad@gmail.com',
                'phone' => '+234-816-456-7890',
                'location' => ['state' => 'Borno', 'city' => 'Maiduguri'],
            ],
            [
                'name' => 'Usman Aliyu',
                'email' => 'usman.aliyu@hotmail.com',
                'phone' => '+234-819-567-8901',
                'location' => ['state' => 'Bauchi', 'city' => 'Bauchi'],
            ]
        ];

        foreach ($tenants as $tenantData) {
            $this->createTenant($tenantData);
        }

        $this->command->info('✅ Tenant users created successfully!');
    }

    /**
     * Create an agency
     */
    private function createAgency(array $data): Agency
    {
        // Create agency owner user
        $owner = User::create([
            'name' => 'CEO ' . explode(' ', $data['name'])[0],
            'email' => str_replace('info@', 'ceo@', $data['email']),
            'phone' => str_replace('-123-', '-111-', $data['phone']),
            'password' => Hash::make('password123'),
            'user_type' => 'agency_owner',
            'is_verified' => true,
            'is_active' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Get location
        $state = State::where('name', 'like', '%' . $data['location']['state'] . '%')->first();
        $city = City::where('name', $data['location']['city'])->first();
        $area = $city ? Area::where('city_id', $city->id)->first() : null;

        // Create agency
        $agency = Agency::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'license_number' => $data['license_number'],
            'license_expiry_date' => now()->addYears(3),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'website' => $data['website'],
            'address' => [
                'street' => 'Plot ' . rand(1, 500) . ', ' . ($area->name ?? 'Business District'),
                'area' => $area->name ?? 'Central Area',
                'city' => $city->name ?? 'City Center',
                'state' => $state->name ?? 'State',
            ],
            'specializations' => $data['specializations'],
            'years_in_business' => rand(5, 15),
            'rating' => rand(40, 50) / 10, // 4.0 to 5.0
            'total_reviews' => rand(50, 200),
            'is_verified' => true,
            'is_featured' => rand(0, 1),
            'is_active' => true,
            'verified_at' => now(),
            'owner_id' => $owner->id,
            'state_id' => $state->id ?? null,
            'city_id' => $city->id ?? null,
            'area_id' => $area->id ?? null,
        ]);

        // Associate owner with agency
        $agency->users()->attach($owner->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return $agency;
    }

    /**
     * Create agents for an agency
     */
    private function createAgentsForAgency(Agency $agency, int $count): void
    {
        $northernNames = [
            'Muhammad Audu', 'Fatima Shehu', 'Ibrahim Garba', 'Amina Bello', 'Usman Yakubu',
            'Zainab Ahmad', 'Abdullahi Umar', 'Khadija Aliyu', 'Musa Ibrahim', 'Halima Sani',
            'Suleiman Yusuf', 'Hauwa Muhammad', 'Bashir Idris', 'Safiya Hassan', 'Ahmad Baba'
        ];

        for ($i = 0; $i < $count; $i++) {
            $name = $northernNames[array_rand($northernNames)];

            // Create agent user
            $user = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '.' . time() . rand(100, 999) . '@' . strtolower(str_replace(' ', '', $agency->name)) . '.com',
                'phone' => '+234-' . rand(800, 819) . '-' . rand(100, 999) . '-' . rand(1000, 9999),
                'password' => Hash::make('password123'),
                'user_type' => 'agent',
                'is_verified' => true,
                'is_active' => true,
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]);

            // Create agent profile
            $agent = Agent::create([
                'user_id' => $user->id,
                'agency_id' => $agency->id,
                'license_number' => 'AGT-' . $agency->id . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'specializations' => $this->getRandomSpecializations(),
                'years_experience' => rand(2, 12),
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(10, 100),
                'total_properties' => rand(10, 50),
                'active_listings' => rand(2, 10),
                'properties_sold' => rand(2, 25),
                'properties_rented' => rand(2, 25),
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => rand(0, 1),
                'accepts_new_clients' => true,
                'verified_at' => now(),
                'last_active_at' => now(),
            ]);

            // Associate with agency
            $agency->users()->attach($user->id, [
                'role' => 'agent',
                'is_active' => true,
                'joined_at' => now(),
            ]);

            // Create properties for this agent
            $this->createPropertiesForAgent($agent, rand(2, 4));
        }
    }

    /**
     * Create an independent agent
     */
    private function createIndependentAgent(array $data): Agent
    {
        // Create agent user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make('password123'),
            'user_type' => 'agent',
            'is_verified' => true,
            'is_active' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Get location
        $state = State::where('name', 'like', '%' . $data['location']['state'] . '%')->first();
        $city = City::where('name', $data['location']['city'])->first();

        // Create agent profile
        $agent = Agent::create([
            'user_id' => $user->id,
            'agency_id' => null, // Independent
            'license_number' => 'IND-' . strtoupper(substr($data['name'], 0, 3)) . '-' . date('Y'),
            'specializations' => $data['specialization'],
            'years_experience' => rand(3, 15),
            'rating' => rand(38, 50) / 10,
            'total_reviews' => rand(15, 80),
            'total_properties' => rand(15, 80),
            'active_listings' => rand(3, 15),
            'properties_sold' => rand(5, 50),
            'properties_rented' => rand(5, 50),
            'is_available' => true,
            'is_verified' => true,
            'is_featured' => rand(0, 1),
            'accepts_new_clients' => true,
            'verified_at' => now(),
            'last_active_at' => now(),
        ]);

        return $agent;
    }

    /**
     * Create a property owner
     */
    private function createPropertyOwner(array $data): PropertyOwner
    {
        // Create owner user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make('password123'),
            'user_type' => 'property_owner',
            'is_verified' => true,
            'is_active' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Get location
        $state = State::where('name', 'like', '%' . $data['location']['state'] . '%')->first();
        $city = City::where('name', $data['location']['city'])->first();

        // Create property owner profile
        $owner = PropertyOwner::create([
            'user_id' => $user->id,
            'type' => 'individual',
            'first_name' => explode(' ', $data['name'])[0] ?? 'Owner',
            'last_name' => explode(' ', $data['name'], 2)[1] ?? 'User',
            'company_name' => $data['name'] . ' Properties',
            'email' => $data['email'],
            'phone' => $data['phone'],
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        return $owner;
    }

    /**
     * Create a tenant
     */
    private function createTenant(array $data): Tenant
    {
        // Create tenant user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make('password123'),
            'user_type' => 'tenant',
            'is_verified' => true,
            'is_active' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Get location
        $state = State::where('name', 'like', '%' . $data['location']['state'] . '%')->first();
        $city = City::where('name', $data['location']['city'])->first();

        // Create tenant profile
        $tenant = Tenant::create([
            'user_id' => $user->id,
            'first_name' => explode(' ', $data['name'])[0] ?? 'Tenant',
            'last_name' => explode(' ', $data['name'], 2)[1] ?? 'User',
            'email' => $data['email'],
            'phone' => $data['phone'],
            'employment_status' => ['employed', 'self_employed', 'business_owner'][rand(0, 2)],
            'monthly_income' => rand(200000, 800000),
            'nationality' => 'Nigerian',
            'occupation' => ['Teacher', 'Engineer', 'Doctor', 'Lawyer', 'Business Owner', 'Civil Servant'][rand(0, 5)],
            'is_active' => true,
        ]);

        return $tenant;
    }

    /**
     * Create properties for an agent
     */
    private function createPropertiesForAgent(Agent $agent, int $count): void
    {
        // Create a property owner for this agent if they don't have one
        $propertyOwner = PropertyOwner::firstOrCreate([
            'user_id' => $agent->user_id,
        ], [
            'type' => 'individual',
            'first_name' => explode(' ', $agent->user->name)[0] ?? 'Agent',
            'last_name' => explode(' ', $agent->user->name)[1] ?? 'User',
            'company_name' => $agent->user->name . ' Properties',
            'email' => $agent->user->email,
            'phone' => $agent->user->phone,
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        for ($i = 0; $i < $count; $i++) {
            $this->createProperty([
                'agent_id' => $agent->id,
                'agency_id' => $agent->agency_id,
                'owner_id' => $propertyOwner->id,
            ]);
        }
    }

    /**
     * Create properties for an owner
     */
    private function createPropertiesForOwner(PropertyOwner $owner, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->createProperty([
                'owner_id' => $owner->id, // Use PropertyOwner id, not user_id
                'agent_id' => null,
                'agency_id' => null,
            ]);
        }
    }

    /**
     * Create a property with realistic data
     */
    private function createProperty(array $context = []): Property
    {
        $propertyTypes = PropertyType::all();
        $propertySubtypes = PropertySubtype::all();
        $propertyFeatures = PropertyFeature::all();

        // Northern Nigeria focused locations
        $northernStates = State::whereIn('region', ['north_central', 'north_east', 'north_west'])->get();
        $state = $northernStates->random();
        $cities = City::where('state_id', $state->id)->get();
        $city = $cities->random();
        $areas = Area::where('city_id', $city->id)->get();

        // If no areas for this city, find a city that has areas
        $attempts = 0;
        while ($areas->count() === 0 && $attempts < 10) {
            $city = $cities->random();
            $areas = Area::where('city_id', $city->id)->get();
            $attempts++;
        }

        // If still no areas, use any area from any northern state
        if ($areas->count() === 0) {
            $areas = Area::whereHas('city.state', function ($query) {
                $query->whereIn('region', ['north_central', 'north_east', 'north_west']);
            })->get();
        }

        $area = $areas->random();

        $propertyType = $propertyTypes->random();
        $propertySubtype = $propertySubtypes->where('property_type_id', $propertyType->id)->first()
                         ?? $propertySubtypes->random();

        $listingType = ['rent', 'sale'][rand(0, 1)];
        $bedrooms = rand(1, 6);
        $bathrooms = rand(1, $bedrooms);

        // Realistic pricing based on location and type
        $basePrice = $this->calculateBasePrice($state->name, $city->name, $propertyType->name, $bedrooms);

        if ($listingType === 'rent') {
            $price = $basePrice * rand(8, 15) / 100; // 8-15% of sale price as annual rent
        } else {
            $price = $basePrice;
        }

        $titles = $this->getPropertyTitles($propertyType->name, $bedrooms, $area->name, $city->name);
        $title = $titles[array_rand($titles)];

        $property = Property::create([
            'title' => $title,
            'slug' => Str::slug($title . '-' . $city->name . '-' . rand(1000, 9999)),
            'description' => $this->generatePropertyDescription($propertyType->name, $bedrooms, $area->name, $listingType),
            'listing_type' => $listingType,
            'status' => 'available',
            'price' => $price,
            'price_period' => $listingType === 'rent' ? 'per_year' : null,
            'service_charge' => $listingType === 'rent' ? rand(50000, 200000) : null,
            'legal_fee' => $listingType === 'sale' ? rand(100000, 500000) : null,
            'agency_fee' => isset($context['agency_id']) ? rand(100000, 300000) : null,
            'caution_deposit' => $listingType === 'rent' ? $price * rand(1, 2) : null,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'toilets' => $bathrooms + rand(0, 1),
            'size_sqm' => rand(80, 500),
            'parking_spaces' => rand(1, 4),
            'year_built' => rand(2010, 2024),
            'furnishing_status' => ['unfurnished', 'semi_furnished', 'furnished'][rand(0, 2)],
            'compound_type' => ['standalone', 'duplex', 'terrace', 'apartment'][rand(0, 3)],
            'address' => $this->generatePropertyAddress($area->name, $city->name),
            'landmark' => $this->generateLandmark($area->name, $city->name),
            'latitude' => $this->generateLatitude($state->name),
            'longitude' => $this->generateLongitude($state->name),
            'property_type_id' => $propertyType->id,
            'property_subtype_id' => $propertySubtype->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
            'owner_id' => $context['owner_id'] ?? 1, // Will be set properly by caller functions
            'agent_id' => $context['agent_id'] ?? null,
            'agency_id' => $context['agency_id'] ?? null,
            'meta_title' => $title . ' - ' . $city->name,
            'meta_description' => substr($this->generatePropertyDescription($propertyType->name, $bedrooms, $area->name, $listingType), 0, 160),
            'view_count' => rand(0, 100),
            'inquiry_count' => rand(0, 20),
            'favorite_count' => rand(0, 15),
            'is_featured' => rand(0, 10) > 7, // 30% chance of being featured
            'is_verified' => rand(0, 10) > 2, // 80% chance of being verified
            'is_published' => true,
            'featured_until' => rand(0, 10) > 7 ? now()->addDays(rand(7, 30)) : null,
            'verified_at' => rand(0, 10) > 2 ? now() : null,
            'published_at' => now(),
        ]);

        // Attach random features
        $randomFeatures = $propertyFeatures->random(rand(3, 8));
        $property->features()->attach($randomFeatures->pluck('id'));

        // Add realistic property images
        $this->addPropertyImages($property);

        return $property;
    }

    /**
     * Calculate base price based on location and property details
     */
    private function calculateBasePrice(string $state, string $city, string $propertyType, int $bedrooms): int
    {
        $baseMultiplier = 1;

        // State-based multipliers
        $stateMultipliers = [
            'Abuja Federal Capital Territory' => 3.5,
            'Lagos' => 3.0,
            'Kano' => 1.8,
            'Kaduna' => 1.6,
            'Rivers' => 2.2,
            'Oyo' => 1.4,
        ];

        $baseMultiplier *= $stateMultipliers[$state] ?? 1.0;

        // City-based adjustments
        if (in_array($city, ['Maitama', 'Asokoro', 'Wuse', 'Central Business District'])) {
            $baseMultiplier *= 2.0; // Premium areas
        } elseif (in_array($city, ['GRA Maiduguri', 'GRA Kano', 'GRA Kaduna'])) {
            $baseMultiplier *= 1.5; // GRA areas
        }

        // Property type multipliers
        $typeMultipliers = [
            'Apartment' => 1.0,
            'House' => 1.3,
            'Land' => 0.8,
            'Commercial' => 1.5,
        ];

        $baseMultiplier *= $typeMultipliers[$propertyType] ?? 1.0;

        // Bedroom-based pricing
        $bedroomBase = [
            1 => 2000000,
            2 => 3500000,
            3 => 5000000,
            4 => 7500000,
            5 => 10000000,
            6 => 15000000,
        ];

        $basePrice = $bedroomBase[$bedrooms] ?? 5000000;

        return (int)($basePrice * $baseMultiplier);
    }

    /**
     * Generate property titles
     */
    private function getPropertyTitles(string $propertyType, int $bedrooms, string $area, string $city): array
    {
        $bedroomText = $bedrooms . ' Bedroom';

        $titles = [
            "Executive {$bedroomText} {$propertyType} in {$area}",
            "Luxury {$bedroomText} {$propertyType} at {$area}",
            "Modern {$bedroomText} {$propertyType} in {$city}",
            "Premium {$bedroomText} {$propertyType} - {$area}",
            "Contemporary {$bedroomText} {$propertyType} in {$area}",
            "Spacious {$bedroomText} {$propertyType} at {$area}",
            "Beautiful {$bedroomText} {$propertyType} in {$city}",
            "Elegant {$bedroomText} {$propertyType} - {$area}",
        ];

        return $titles;
    }

    /**
     * Generate property description
     */
    private function generatePropertyDescription(string $propertyType, int $bedrooms, string $area, string $listingType): string
    {
        $action = $listingType === 'rent' ? 'rent' : 'sale';

        $descriptions = [
            "Exceptional {$bedrooms} bedroom {$propertyType} available for {$action} in the prestigious {$area} area. This property features modern amenities, spacious rooms, and excellent security. Perfect for families or professionals seeking comfort and convenience.",

            "Beautiful {$bedrooms} bedroom {$propertyType} for {$action} in {$area}. Well-designed with contemporary finishes, ample parking, and access to essential amenities. Located in a serene environment with 24/7 security.",

            "Luxurious {$bedrooms} bedroom {$propertyType} in the heart of {$area}. Features include fitted kitchen, en-suite bathrooms, air conditioning, generator backup, and excellent road network. Ideal for modern living.",

            "Spacious {$bedrooms} bedroom {$propertyType} available for {$action} in {$area}. Property boasts modern architecture, quality finishes, adequate parking space, and proximity to schools, hospitals, and shopping centers.",

            "Premium {$bedrooms} bedroom {$propertyType} for {$action} in {$area}. Comes with excellent infrastructure, reliable power supply, security features, and beautiful interior design. Perfect for discerning individuals."
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Generate property address
     */
    private function generatePropertyAddress(string $area, string $city): string
    {
        $plots = ['Plot ' . rand(1, 500), 'House ' . rand(1, 200), 'Block ' . rand(1, 50)];
        $streets = ['Ahmadu Bello Way', 'Ibrahim Babangida Street', 'Shehu Shagari Close', 'Murtala Mohammed Road', 'Tafawa Balewa Street'];

        return $plots[array_rand($plots)] . ', ' . $streets[array_rand($streets)] . ', ' . $area . ', ' . $city;
    }

    /**
     * Generate landmark
     */
    private function generateLandmark(string $area, string $city): string
    {
        $landmarks = [
            'Near Central Mosque',
            'Close to Government House',
            'Behind Central Market',
            'Opposite Police Station',
            'Near Federal University',
            'Close to General Hospital',
            'Behind Emir\'s Palace',
            'Near Main Market',
            'Close to Stadium',
            'Opposite Bank'
        ];

        return $landmarks[array_rand($landmarks)];
    }

    /**
     * Generate realistic latitude based on state
     */
    private function generateLatitude(string $state): float
    {
        $stateCoordinates = [
            'Kano' => ['lat' => 12.0, 'lng' => 8.5],
            'Kaduna' => ['lat' => 10.5, 'lng' => 7.4],
            'Abuja Federal Capital Territory' => ['lat' => 9.0, 'lng' => 7.5],
            'Borno' => ['lat' => 11.8, 'lng' => 13.2],
            'Bauchi' => ['lat' => 10.3, 'lng' => 9.8],
            'Gombe' => ['lat' => 10.3, 'lng' => 11.2],
        ];

        $coords = $stateCoordinates[$state] ?? ['lat' => 10.0, 'lng' => 8.0];
        return $coords['lat'] + (rand(-100, 100) / 1000); // Add small variation
    }

    /**
     * Generate realistic longitude based on state
     */
    private function generateLongitude(string $state): float
    {
        $stateCoordinates = [
            'Kano' => ['lat' => 12.0, 'lng' => 8.5],
            'Kaduna' => ['lat' => 10.5, 'lng' => 7.4],
            'Abuja Federal Capital Territory' => ['lat' => 9.0, 'lng' => 7.5],
            'Borno' => ['lat' => 11.8, 'lng' => 13.2],
            'Bauchi' => ['lat' => 10.3, 'lng' => 9.8],
            'Gombe' => ['lat' => 10.3, 'lng' => 11.2],
        ];

        $coords = $stateCoordinates[$state] ?? ['lat' => 10.0, 'lng' => 8.0];
        return $coords['lng'] + (rand(-100, 100) / 1000); // Add small variation
    }

    /**
     * Get random specializations for agents
     */
    private function getRandomSpecializations(): string
    {
        $specializations = [
            'Residential Properties',
            'Commercial Properties',
            'Luxury Homes',
            'Investment Properties',
            'Land Sales',
            'Property Management',
            'Industrial Properties',
            'Affordable Housing',
        ];

        $selected = array_rand($specializations, rand(1, 3));
        if (is_array($selected)) {
            return implode(',', array_map(fn($i) => $specializations[$i], $selected));
        }
        return $specializations[$selected];
    }

    /**
     * Add property images using Spatie Media Library
     */
    private function addPropertyImages(Property $property): void
    {
        try {
            // Real estate property images from Unsplash
            $imageUrls = [
                'https://images.unsplash.com/photo-1505843513577-22bb7d21e455?w=800&h=600&q=80', // Modern house
                'https://images.unsplash.com/photo-1522050212171-61b01dd24579?w=800&h=600&q=80', // Apartment building
                'https://images.unsplash.com/photo-1489171078254-c3365d6e359f?w=800&h=600&q=80', // Luxury home
                'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?w=800&h=600&q=80', // Interior
                'https://images.unsplash.com/photo-1502005229762-cf1b2da7c5d6?w=800&h=600&q=80', // Kitchen
                'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&q=80', // Living room
                'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&q=80', // Bedroom
                'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=800&h=600&q=80', // Kitchen view
                'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800&h=600&q=80', // Bathroom
                'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?w=800&h=600&q=80', // House exterior
            ];

            // Shuffle and select 3-6 images for this property
            shuffle($imageUrls);
            $selectedImages = array_slice($imageUrls, 0, rand(3, 6));

            // Add featured image (first image)
            $featuredUrl = $selectedImages[0];
            $featuredMedia = $property
                ->addMediaFromUrl($featuredUrl)
                ->toMediaCollection('featured');

            $this->command->info("📷 Added featured image for: {$property->title}");

            // Add gallery images (remaining images)
            for ($i = 1; $i < count($selectedImages); $i++) {
                $property
                    ->addMediaFromUrl($selectedImages[$i])
                    ->toMediaCollection('gallery');
            }

            $galleryCount = count($selectedImages) - 1;
            $this->command->info("🖼️  Added {$galleryCount} gallery images for: {$property->title}");

        } catch (\Exception $e) {
            // If image download fails, continue without breaking the seeder
            $this->command->warn("⚠️  Failed to add images for {$property->title}: " . $e->getMessage());
        }
    }
}