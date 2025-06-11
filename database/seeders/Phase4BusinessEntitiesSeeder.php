<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Agency;
use App\Models\Agent;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Phase4BusinessEntitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Phase 4: Business Entities (Agencies & Agents)...');

        // Verify prerequisites exist
        $this->validatePrerequisites();

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing records in reverse dependency order
        $this->command->info('Clearing existing Phase 4 records...');
        Agent::truncate();
        Agency::truncate();
        
        // Delete agency owners and agents (users with these types)
        User::whereIn('user_type', ['agency_owner', 'agent'])->delete();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create records in dependency order
        $this->seedAgencyOwners();
        $this->seedAgencies();
        $this->seedIndependentAgents();
        $this->seedAgencyAgents();

        $this->command->info('Phase 4 seeding completed successfully!');
    }

    /**
     * Validate that prerequisite data exists before seeding
     */
    private function validatePrerequisites(): void
    {
        $this->command->info('Validating prerequisites...');

        // Check if states exist
        $stateCount = State::count();
        if ($stateCount === 0) {
            throw new \Exception('No states found. Please run Phase1FoundationSeeder first.');
        }

        // Check if cities exist
        $cityCount = City::count();
        if ($cityCount === 0) {
            throw new \Exception('No cities found. Please run Phase2LocationSeeder first.');
        }

        // Check if areas exist
        $areaCount = Area::count();
        if ($areaCount === 0) {
            throw new \Exception('No areas found. Please run Phase2LocationSeeder first.');
        }

        $this->command->info("Prerequisites validated: {$stateCount} states, {$cityCount} cities, {$areaCount} areas");
    }

    private function seedAgencyOwners(): void
    {
        $this->command->info('Creating agency owners...');

        $agencyOwners = [
            [
                'name' => 'Adebayo Ogundimu',
                'email' => 'adebayo@primerealty.ng',
                'phone' => '+2348012345001',
                'password' => bcrypt('password'),
                'user_type' => 'agency_owner',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Funmi Adeyemi',
                'email' => 'funmi@goldenhomes.ng',
                'phone' => '+2348012345002',
                'password' => bcrypt('password'),
                'user_type' => 'agency_owner',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Chidi Okwu',
                'email' => 'chidi@royalproperties.ng',
                'phone' => '+2348012345003',
                'password' => bcrypt('password'),
                'user_type' => 'agency_owner',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Amina Hassan',
                'email' => 'amina@zenithrealty.ng',
                'phone' => '+2348012345004',
                'password' => bcrypt('password'),
                'user_type' => 'agency_owner',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Emmanuel Kalu',
                'email' => 'emmanuel@prestigehomes.ng',
                'phone' => '+2348012345005',
                'password' => bcrypt('password'),
                'user_type' => 'agency_owner',
                'is_verified' => true,
                'is_active' => true,
            ],
        ];

        foreach ($agencyOwners as $owner) {
            User::create($owner);
        }
    }

    private function seedAgencies(): void
    {
        $this->command->info('Creating agencies...');

        // Get states with error handling
        $lagos = State::where('name', 'Lagos')->first();
        $abuja = State::where('name', 'Abuja Federal Capital Territory')->first();
        $rivers = State::where('name', 'Rivers')->first();
        $ogun = State::where('name', 'Ogun')->first();

        // Validate that required states exist
        if (!$lagos) {
            throw new \Exception('Lagos state not found. Please ensure Phase1FoundationSeeder has been run.');
        }
        if (!$abuja) {
            throw new \Exception('Abuja Federal Capital Territory not found. Please ensure Phase1FoundationSeeder has been run.');
        }
        if (!$rivers) {
            throw new \Exception('Rivers state not found. Please ensure Phase1FoundationSeeder has been run.');
        }
        if (!$ogun) {
            throw new \Exception('Ogun state not found. Please ensure Phase1FoundationSeeder has been run.');
        }

        // Get cities with validation
        $lagosCity = City::where('name', 'Lagos Island')->first();
        $ikejaCity = City::where('name', 'Ikeja')->first();
        $abujaCity = City::where('state_id', $abuja->id)->first();
        $phCity = City::where('name', 'Port Harcourt')->first();
        $abeokutaCity = City::where('name', 'Abeokuta North')->first();

        $agencies = [
            [
                'name' => 'Prime Realty Nigeria',
                'slug' => 'prime-realty-nigeria',
                'description' => 'Leading real estate agency specializing in luxury properties across Lagos and Abuja. With over 15 years of experience, we provide comprehensive real estate solutions.',
                'license_number' => 'REL-LG-2020-001',
                'license_expiry_date' => Carbon::now()->addYears(2)->toDateString(),
                'email' => 'info@primerealty.ng',
                'phone' => '+2341234567890',
                'website' => 'https://primerealty.ng',
                'address' => [
                    'street' => '45 Ademola Adetokunbo Crescent',
                    'city' => 'Lagos',
                    'state' => 'Lagos',
                    'country' => 'Nigeria',
                    'postal_code' => '101241'
                ],
                'latitude' => 6.4488,
                'longitude' => 3.4123,
                'social_media' => [
                    'facebook' => 'primerealtynigeria',
                    'twitter' => 'primerealty_ng',
                    'instagram' => 'primerealty.ng',
                    'linkedin' => 'prime-realty-nigeria'
                ],
                'specializations' => 'Luxury Homes,Commercial Properties,Investment Properties',
                'years_in_business' => 15,
                'rating' => 4.8,
                'total_reviews' => 234,
                'total_properties' => 450,
                'total_agents' => 12,
                'is_verified' => true,
                'is_featured' => true,
                'verified_at' => Carbon::now()->subMonths(6),
                'owner_id' => User::where('email', 'adebayo@primerealty.ng')->first()->id,
                'state_id' => $lagos->id,
                'city_id' => $lagosCity->id,
                'area_id' => Area::where('name', 'Victoria Island')->first()?->id,
            ],
            [
                'name' => 'Golden Homes Realty',
                'slug' => 'golden-homes-realty',
                'description' => 'Your trusted partner for affordable and luxury housing solutions. We specialize in residential properties and offer excellent customer service.',
                'license_number' => 'REL-LG-2021-015',
                'license_expiry_date' => Carbon::now()->addYears(1)->toDateString(),
                'email' => 'contact@goldenhomes.ng',
                'phone' => '+2341234567891',
                'website' => 'https://goldenhomes.ng',
                'address' => [
                    'street' => '78 Allen Avenue',
                    'city' => 'Lagos',
                    'state' => 'Lagos',
                    'country' => 'Nigeria',
                    'postal_code' => '101233'
                ],
                'latitude' => 6.5244,
                'longitude' => 3.3792,
                'social_media' => [
                    'facebook' => 'goldenhomesrealty',
                    'instagram' => 'goldenhomes_ng'
                ],
                'specializations' => 'Residential Properties,First-Time Buyers,Property Management',
                'years_in_business' => 8,
                'rating' => 4.5,
                'total_reviews' => 156,
                'total_properties' => 320,
                'total_agents' => 8,
                'is_verified' => true,
                'is_featured' => false,
                'verified_at' => Carbon::now()->subMonths(3),
                'owner_id' => User::where('email', 'funmi@goldenhomes.ng')->first()->id,
                'state_id' => $lagos->id,
                'city_id' => $ikejaCity->id,
                'area_id' => Area::where('name', 'Ikeja')->first()?->id,
            ],
            [
                'name' => 'Royal Properties Limited',
                'slug' => 'royal-properties-limited',
                'description' => 'Premium real estate services in Abuja and surrounding areas. We focus on high-end residential and commercial properties.',
                'license_number' => 'REL-AB-2019-008',
                'license_expiry_date' => Carbon::now()->addYears(3)->toDateString(),
                'email' => 'info@royalproperties.ng',
                'phone' => '+2349876543210',
                'website' => 'https://royalproperties.ng',
                'address' => [
                    'street' => '12 Shehu Shagari Way',
                    'city' => 'Abuja',
                    'state' => 'Federal Capital Territory',
                    'country' => 'Nigeria',
                    'postal_code' => '900001'
                ],
                'latitude' => 9.0765,
                'longitude' => 7.3986,
                'social_media' => [
                    'facebook' => 'royalpropertiesltd',
                    'twitter' => 'royalprops_ng',
                    'linkedin' => 'royal-properties-limited'
                ],
                'specializations' => 'Commercial Properties,Luxury Homes,Land Development',
                'years_in_business' => 12,
                'rating' => 4.7,
                'total_reviews' => 189,
                'total_properties' => 275,
                'total_agents' => 10,
                'is_verified' => true,
                'is_featured' => true,
                'verified_at' => Carbon::now()->subMonths(8),
                'owner_id' => User::where('email', 'chidi@royalproperties.ng')->first()->id,
                'state_id' => $abuja->id,
                'city_id' => $abujaCity?->id,
                'area_id' => Area::where('name', 'Maitama')->first()?->id,
            ],
            [
                'name' => 'Zenith Realty Services',
                'slug' => 'zenith-realty-services',
                'description' => 'Professional real estate services across multiple states. We offer comprehensive property solutions for all your needs.',
                'license_number' => 'REL-RS-2022-025',
                'license_expiry_date' => Carbon::now()->addMonths(18)->toDateString(),
                'email' => 'hello@zenithrealty.ng',
                'phone' => '+2348123456789',
                'website' => 'https://zenithrealty.ng',
                'address' => [
                    'street' => '25 Trans Amadi Industrial Layout',
                    'city' => 'Port Harcourt',
                    'state' => 'Rivers',
                    'country' => 'Nigeria',
                    'postal_code' => '500001'
                ],
                'latitude' => 4.8156,
                'longitude' => 7.0498,
                'social_media' => [
                    'facebook' => 'zenithrealtyservices',
                    'instagram' => 'zenithrealty_ng'
                ],
                'specializations' => 'Industrial Properties,Residential,Property Valuation',
                'years_in_business' => 6,
                'rating' => 4.3,
                'total_reviews' => 87,
                'total_properties' => 180,
                'total_agents' => 6,
                'is_verified' => true,
                'is_featured' => false,
                'verified_at' => Carbon::now()->subMonths(2),
                'owner_id' => User::where('email', 'amina@zenithrealty.ng')->first()->id,
                'state_id' => $rivers->id,
                'city_id' => $phCity->id,
                'area_id' => Area::where('name', 'GRA Phase 2')->first()?->id,
            ],
            [
                'name' => 'Prestige Homes Network',
                'slug' => 'prestige-homes-network',
                'description' => 'Exclusive network of premium properties. We cater to discerning clients seeking exceptional real estate opportunities.',
                'license_number' => null, // New agency, license pending
                'license_expiry_date' => null,
                'email' => 'contact@prestigehomes.ng',
                'phone' => '+2347123456789',
                'website' => 'https://prestigehomes.ng',
                'address' => [
                    'street' => '67 MKO Abiola Way',
                    'city' => 'Abeokuta',
                    'state' => 'Ogun',
                    'country' => 'Nigeria',
                    'postal_code' => '110001'
                ],
                'latitude' => 7.1475,
                'longitude' => 3.3619,
                'social_media' => [
                    'instagram' => 'prestigehomes_ng'
                ],
                'specializations' => 'Luxury Homes,Exclusive Properties,Investment Advisory',
                'years_in_business' => 2,
                'rating' => 4.0,
                'total_reviews' => 23,
                'total_properties' => 45,
                'total_agents' => 3,
                'is_verified' => false,
                'is_featured' => false,
                'verified_at' => null,
                'owner_id' => User::where('email', 'emmanuel@prestigehomes.ng')->first()->id,
                'state_id' => $ogun->id,
                'city_id' => $abeokutaCity->id,
                'area_id' => null,
            ],
        ];

        foreach ($agencies as $agencyData) {
            Agency::create($agencyData);
        }
    }

    private function seedIndependentAgents(): void
    {
        $this->command->info('Creating independent agents...');

        $independentAgentUsers = [
            [
                'name' => 'Tunde Bakare',
                'email' => 'tunde@freelanceagent.ng',
                'phone' => '+2348098765001',
                'password' => bcrypt('password'),
                'user_type' => 'agent',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sandra Okafor',
                'email' => 'sandra@propertyexpert.ng',
                'phone' => '+2348098765002',
                'password' => bcrypt('password'),
                'user_type' => 'agent',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Ibrahim Mohammed',
                'email' => 'ibrahim@realtyspecialist.ng',
                'phone' => '+2348098765003',
                'password' => bcrypt('password'),
                'user_type' => 'agent',
                'is_verified' => false,
                'is_active' => true,
            ],
        ];

        foreach ($independentAgentUsers as $userData) {
            $user = User::create($userData);

            $agentData = [
                'license_number' => $user->is_verified ? 'AGT-IND-' . rand(1000, 9999) : null,
                'license_expiry_date' => $user->is_verified ? Carbon::now()->addYears(2)->toDateString() : null,
                'bio' => $this->generateAgentBio($user->name),
                'specializations' => $this->getRandomSpecializations(),
                'years_experience' => rand(2, 15),
                'commission_rate' => rand(200, 500) / 100,
                'languages' => ['English', 'Yoruba'],
                'service_areas' => $this->getRandomServiceAreas(),
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(5, 50),
                'total_properties' => rand(10, 100),
                'active_listings' => rand(5, 25),
                'properties_sold' => rand(5, 80),
                'properties_rented' => rand(10, 50),
                'is_available' => true,
                'is_verified' => $user->is_verified,
                'is_featured' => false,
                'accepts_new_clients' => true,
                'verified_at' => $user->is_verified ? Carbon::now()->subMonths(rand(1, 12)) : null,
                'last_active_at' => Carbon::now()->subDays(rand(0, 7)),
                'user_id' => $user->id,
                'agency_id' => null,
            ];

            Agent::create($agentData);
        }
    }

    private function seedAgencyAgents(): void
    {
        $this->command->info('Creating agency agents...');

        $agencies = Agency::all();

        $agencyAgentUsers = [
            // Prime Realty agents
            [
                'name' => 'Kemi Adeleke',
                'email' => 'kemi@primerealty.ng',
                'phone' => '+2348012340001',
                'agency_name' => 'Prime Realty Nigeria',
            ],
            [
                'name' => 'Segun Ogundimu',
                'email' => 'segun@primerealty.ng',
                'phone' => '+2348012340002',
                'agency_name' => 'Prime Realty Nigeria',
            ],
            [
                'name' => 'Blessing Nwankwo',
                'email' => 'blessing@primerealty.ng',
                'phone' => '+2348012340003',
                'agency_name' => 'Prime Realty Nigeria',
            ],
            
            // Golden Homes agents
            [
                'name' => 'David Oyebade',
                'email' => 'david@goldenhomes.ng',
                'phone' => '+2348012340004',
                'agency_name' => 'Golden Homes Realty',
            ],
            [
                'name' => 'Grace Adebisi',
                'email' => 'grace@goldenhomes.ng',
                'phone' => '+2348012340005',
                'agency_name' => 'Golden Homes Realty',
            ],
            
            // Royal Properties agents
            [
                'name' => 'Michael Obi',
                'email' => 'michael@royalproperties.ng',
                'phone' => '+2348012340006',
                'agency_name' => 'Royal Properties Limited',
            ],
            [
                'name' => 'Fatima Abdullahi',
                'email' => 'fatima@royalproperties.ng',
                'phone' => '+2348012340007',
                'agency_name' => 'Royal Properties Limited',
            ],
            
            // Zenith Realty agents
            [
                'name' => 'John Eze',
                'email' => 'john@zenithrealty.ng',
                'phone' => '+2348012340008',
                'agency_name' => 'Zenith Realty Services',
            ],
            
            // Prestige Homes agents
            [
                'name' => 'Taiwo Ogundipe',
                'email' => 'taiwo@prestigehomes.ng',
                'phone' => '+2348012340009',
                'agency_name' => 'Prestige Homes Network',
            ],
        ];

        foreach ($agencyAgentUsers as $userData) {
            $agency = $agencies->where('name', $userData['agency_name'])->first();
            
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => bcrypt('password'),
                'user_type' => 'agent',
                'is_verified' => $agency->is_verified,
                'is_active' => true,
            ]);

            $agentData = [
                'license_number' => $agency->is_verified ? 'AGT-' . strtoupper(substr($agency->slug, 0, 3)) . '-' . rand(1000, 9999) : null,
                'license_expiry_date' => $agency->is_verified ? Carbon::now()->addYears(2)->toDateString() : null,
                'bio' => $this->generateAgentBio($user->name),
                'specializations' => $this->getRandomSpecializations(),
                'years_experience' => rand(1, 12),
                'commission_rate' => rand(150, 400) / 100,
                'languages' => $this->getRandomLanguages(),
                'service_areas' => $this->getRandomServiceAreas(),
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(5, 80),
                'total_properties' => rand(15, 150),
                'active_listings' => rand(5, 30),
                'properties_sold' => rand(10, 120),
                'properties_rented' => rand(15, 80),
                'is_available' => rand(0, 1) ? true : false,
                'is_verified' => $agency->is_verified,
                'is_featured' => rand(0, 1) ? true : false,
                'accepts_new_clients' => true,
                'verified_at' => $agency->is_verified ? Carbon::now()->subMonths(rand(1, 18)) : null,
                'last_active_at' => Carbon::now()->subDays(rand(0, 14)),
                'user_id' => $user->id,
                'agency_id' => $agency->id,
            ];

            Agent::create($agentData);
        }
    }

    private function generateAgentBio(string $name): string
    {
        $bios = [
            "Experienced real estate professional with a passion for helping clients find their dream homes.",
            "Dedicated agent specializing in luxury properties and investment opportunities.",
            "Customer-focused realtor committed to providing exceptional service and market expertise.",
            "Results-driven agent with extensive knowledge of the local real estate market.",
            "Professional real estate consultant with a track record of successful transactions.",
        ];

        return $bios[array_rand($bios)] . " Contact $name for all your property needs.";
    }

    private function getRandomSpecializations(): string
    {
        $specializations = [
            'Residential Properties',
            'Commercial Properties',
            'Luxury Homes',
            'Investment Properties',
            'First-Time Buyers',
            'Property Management',
            'Land Development',
            'Industrial Properties',
            'Rental Properties',
            'Property Valuation'
        ];

        $selected = array_rand($specializations, rand(2, 4));
        $result = [];
        foreach ((array)$selected as $index) {
            $result[] = $specializations[$index];
        }

        return implode(',', $result);
    }

    private function getRandomLanguages(): array
    {
        $languages = [
            ['English'],
            ['English', 'Yoruba'],
            ['English', 'Hausa'],
            ['English', 'Igbo'],
            ['English', 'Yoruba', 'Hausa'],
            ['English', 'French'],
        ];

        return $languages[array_rand($languages)];
    }

    private function getRandomServiceAreas(): array
    {
        $areas = Area::inRandomOrder()->limit(rand(3, 8))->pluck('id')->toArray();
        return $areas;
    }
}

