<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Models\Agent;
use App\Models\Lease;
use App\Models\State;
use App\Models\Agency;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\RentPayment;
use Illuminate\Support\Str;
use App\Models\PropertyType;
use App\Models\PropertyInquiry;
use App\Models\PropertySubtype;
use App\Models\PropertyViewing;
use App\Models\PropertyOwner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComprehensiveTestingSeeder extends Seeder
{
    /**
     * Seed the database with comprehensive test data for all panels and features.
     * This seeder creates realistic scenarios for:
     * - Agency owners with agents creating property listings
     * - Independent agents creating their own listings
     * - Landlords creating direct listings
     * - Tenants renting/buying properties
     * - Complete lease and payment cycles
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive Testing Seeder...');
        $this->command->info('This will create realistic test data for all system features');
        $this->command->line('');

        // Verify prerequisites
        $this->validatePrerequisites();

        // Clear existing test data
        $this->clearExistingTestData();

        // Create comprehensive test scenarios
        $this->createAgencyScenarios();
        $this->createIndependentAgentScenarios();
        $this->createLandlordScenarios();
        $this->createTenantInteractions();
        $this->createLeaseAndPaymentCycles();
        $this->createPropertyInquiriesAndViewings();

        $this->command->line('');
        $this->command->info('✅ Comprehensive Testing Seeder completed successfully!');
        $this->showTestingCredentials();
    }

    private function validatePrerequisites(): void
    {
        $this->command->info('🔍 Validating prerequisites...');

        $requirements = [
            'states' => State::count(),
            'cities' => City::count(),
            'areas' => Area::count(),
            'property_types' => PropertyType::count(),
            'property_subtypes' => PropertySubtype::count(),
        ];

        foreach ($requirements as $entity => $count) {
            if ($count === 0) {
                throw new \Exception("Missing {$entity}. Please run foundation seeders first.");
            }
        }

        $this->command->info('✓ All prerequisites validated');
    }

    /**
     * Clear existing test data to avoid duplicates
     */
    private function clearExistingTestData(): void
    {
        $this->command->info('🧹 Clearing existing test data...');

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Clear in dependency order
            RentPayment::truncate();
            Lease::truncate();
            PropertyInquiry::truncate();
            PropertyViewing::truncate();
            DB::table('property_feature_property')->truncate();
            Property::truncate();
            PropertyOwner::truncate();
            Agent::truncate();
            DB::table('agency_user')->truncate();
            Agency::truncate();
            Tenant::truncate();
            
            // Clear test users (keep system users)
            User::whereIn('user_type', [
                'agency_owner', 'agent', 'property_owner', 'tenant'
            ])->delete();

            $this->command->info('✓ Existing test data cleared');
        } finally {
            // Re-enable foreign key constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Create Agency-based scenarios with multiple agents and properties
     */
    private function createAgencyScenarios(): void
    {
        $this->command->info('🏢 Creating Agency Scenarios...');

        // Get location data
        $lagos = State::where('name', 'Lagos')->first();
        $abuja = State::where('name', 'Federal Capital Territory')->first();
        $rivers = State::where('name', 'Rivers')->first();

        // Check if states exist
        if (!$lagos) {
            $this->command->warn('Lagos state not found, skipping Lagos agency');
        }
        if (!$abuja) {
            $this->command->warn('FCT state not found, skipping Abuja agency');
        }
        if (!$rivers) {
            $this->command->warn('Rivers state not found, skipping Rivers agency');
        }

        // Create 3 comprehensive agency scenarios
        $agencies = [];
        
        if ($lagos) {
            $agencies[] = [
                'name' => 'Prime Realty Lagos',
                'owner_name' => 'Adebayo Ogundimu',
                'email' => 'adebayo@primerealty.ng',
                'state' => $lagos,
                'agent_count' => 4,
                'property_count' => 25,
                'specialization' => ['residential', 'commercial'],
            ];
        }
        
        if ($abuja) {
            $agencies[] = [
                'name' => 'Golden Homes Abuja',
                'owner_name' => 'Funmi Adeyemi',
                'email' => 'funmi@goldenhomes.ng',
                'state' => $abuja,
                'agent_count' => 3,
                'property_count' => 18,
                'specialization' => ['luxury', 'residential'],
            ];
        }
        
        if ($rivers) {
            $agencies[] = [
                'name' => 'Port Harcourt Properties',
                'owner_name' => 'Emeka Okafor',
                'email' => 'emeka@phproperties.ng',
                'state' => $rivers,
                'agent_count' => 2,
                'property_count' => 12,
                'specialization' => ['industrial', 'residential'],
            ];
        }

        foreach ($agencies as $agencyData) {
            $this->createAgencyWithAgentsAndProperties($agencyData);
        }

        $this->command->info('✓ Agency scenarios created');
    }

    private function createAgencyWithAgentsAndProperties(array $agencyData): void
    {
        // Create agency owner
        $owner = User::create([
            'name' => $agencyData['owner_name'],
            'email' => $agencyData['email'],
            'phone' => '+234' . rand(8000000000, 8999999999),
            'password' => bcrypt('password'),
            'user_type' => 'agency_owner',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create agency - get city and area with proper fallbacks
        $city = $agencyData['state']->cities()->whereHas('areas')->inRandomOrder()->first();
        if (!$city) {
            $this->command->warn("No cities with areas found for state: {$agencyData['state']->name}");
            return;
        }

        $area = $city->areas()->inRandomOrder()->first();

        $agency = Agency::create([
            'name' => $agencyData['name'],
            'slug' => Str::slug($agencyData['name']),
            'description' => "Leading real estate agency specializing in " . implode(', ', $agencyData['specialization']),
            'license_number' => 'REA/' . strtoupper(substr($agencyData['state']->name, 0, 3)) . '/' . rand(1000, 9999),
            'license_expiry_date' => now()->addYears(2),
            'email' => $agencyData['email'],
            'phone' => '+234' . rand(9000000000, 9999999999),
            'website' => 'https://' . Str::slug($agencyData['name']) . '.com',
            'address' => [
                'street' => fake()->streetAddress(),
                'area' => $area->name,
                'city' => $city->name,
                'state' => $agencyData['state']->name,
            ],
            'latitude' => fake()->latitude(6.0, 7.0),
            'longitude' => fake()->longitude(3.0, 4.0),
            'specializations' => implode(',', $agencyData['specialization']), // Convert array to comma-separated string
            'years_in_business' => rand(5, 20),
            'rating' => rand(35, 50) / 10, // 3.5 to 5.0
            'total_reviews' => rand(50, 200),
            'is_verified' => true,
            'is_featured' => true,
            'is_active' => true,
            'verified_at' => now(),
            'owner_id' => $owner->id,
            'state_id' => $agencyData['state']->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
        ]);

        // Associate owner with agency
        $owner->agencies()->attach($agency->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Create agents for this agency
        $agents = [];
        for ($i = 1; $i <= $agencyData['agent_count']; $i++) {
            $agentUser = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => '+234' . rand(8000000000, 8999999999),
                'password' => bcrypt('password'),
                'user_type' => 'agent',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Assign agent role with agency context
            $this->assignAgentRoleToUser($agentUser, $agency);

            $agent = Agent::create([
                'license_number' => 'AGENT/' . strtoupper(substr($agencyData['state']->name, 0, 3)) . '/' . rand(1000, 9999),
                'license_expiry_date' => now()->addYears(3),
                'bio' => "Experienced real estate agent with " . rand(2, 10) . " years in the industry.",
                'specializations' => implode(',', fake()->randomElements(['residential', 'commercial', 'luxury', 'affordable'], rand(1, 2))),
                'years_experience' => rand(2, 10),
                'commission_rate' => rand(3, 8),
                'languages' => ['English', 'Yoruba', 'Hausa', 'Igbo'],
                'service_areas' => [$city->name],
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(10, 50),
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => rand(0, 1) === 1,
                'accepts_new_clients' => true,
                'verified_at' => now(),
                'user_id' => $agentUser->id,
                'agency_id' => $agency->id,
            ]);

            // Associate agent with agency
            $agentUser->agencies()->attach($agency->id, [
                'role' => 'agent',
                'is_active' => true,
                'joined_at' => now(),
            ]);

            $agents[] = $agent;
        }

        // Create properties for this agency
        $this->createPropertiesForAgency($agency, $agents, $agencyData['property_count']);
    }

    private function createPropertiesForAgency(Agency $agency, array $agents, int $propertyCount): void
    {
        $propertyTypes = PropertyType::with('propertySubtypes')->get();
        $cities = $agency->state->cities()->whereHas('areas')->with('areas')->get();

        for ($i = 1; $i <= $propertyCount; $i++) {
            // Skip if no cities with areas available
            if ($cities->isEmpty()) {
                $this->command->warn("Skipping agency property creation - no cities with areas for state: {$agency->state->name}");
                break;
            }
            
            $propertyType = $propertyTypes->random();
            
            // Skip if no subtypes available for this property type
            if ($propertyType->propertySubtypes->isEmpty()) {
                $this->command->warn("Skipping property creation - no subtypes for property type: {$propertyType->name}");
                continue;
            }
            
            $subtype = $propertyType->propertySubtypes->random();
            $city = $cities->random();
            
            // Get a random area (we know it exists because we filtered for cities with areas)
            $area = $city->areas->random();
            
            $agent = collect($agents)->random();

            // Create a property owner (either individual or company)
            $ownerType = fake()->randomElement([PropertyOwner::TYPE_INDIVIDUAL, PropertyOwner::TYPE_COMPANY]);
            
            if ($ownerType === PropertyOwner::TYPE_INDIVIDUAL) {
                $propertyOwner = PropertyOwner::create([
                    'type' => $ownerType,
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'email' => fake()->safeEmail(),
                    'phone' => '+234' . rand(8000000000, 8999999999),
                    'address' => fake()->address(),
                    'city' => $city->name,
                    'state' => $agency->state->name,
                    'country' => 'Nigeria',
                    'agency_id' => $agency->id,
                    'agent_id' => null,
                    'notes' => 'Property owner managed by ' . $agency->name,
                    'is_active' => true,
                ]);
            } else {
                $propertyOwner = PropertyOwner::create([
                    'type' => $ownerType,
                    'company_name' => fake()->company(),
                    'email' => fake()->safeEmail(),
                    'phone' => '+234' . rand(8000000000, 8999999999),
                    'address' => fake()->address(),
                    'city' => $city->name,
                    'state' => $agency->state->name,
                    'country' => 'Nigeria',
                    'tax_id' => 'TIN-' . rand(100000000, 999999999),
                    'agency_id' => $agency->id,
                    'agent_id' => null,
                    'notes' => 'Corporate property owner managed by ' . $agency->name,
                    'is_active' => true,
                ]);
            }

            $listingType = fake()->randomElement(['sale', 'rent']);
            $title = $this->generatePropertyTitle($propertyType->name, $subtype->name, $area->name);

            Property::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                'description' => $this->generatePropertyDescription($propertyType->name, $subtype->name),
                'listing_type' => $listingType,
                'status' => fake()->randomElement(['available', 'rented', 'sold', 'off_market']),
                'price' => $this->generatePrice($listingType, $propertyType->name),
                'price_period' => $listingType === 'rent' ? 'per_year' : null,
                'service_charge' => $listingType === 'rent' ? rand(50000, 200000) : null,
                'legal_fee' => rand(100000, 300000),
                'agency_fee' => rand(100000, 500000),
                'caution_deposit' => $listingType === 'rent' ? rand(500000, 2000000) : null,
                'bedrooms' => rand(1, 5),
                'bathrooms' => rand(1, 4),
                'toilets' => rand(1, 5),
                'size_sqm' => rand(80, 500),
                'parking_spaces' => rand(1, 4),
                'year_built' => rand(2010, 2024),
                'furnishing_status' => fake()->randomElement(['furnished', 'semi_furnished', 'unfurnished']),
                'address' => fake()->streetAddress(),
                'landmark' => fake()->randomElement(['Near Shoprite', 'Close to University', 'Main Road', 'Quiet Estate']),
                'latitude' => fake()->latitude(6.0, 7.0),
                'longitude' => fake()->longitude(3.0, 4.0),
                'property_type_id' => $propertyType->id,
                'property_subtype_id' => $subtype->id,
                'state_id' => $agency->state_id,
                'city_id' => $city->id,
                'area_id' => $area->id,
                'owner_id' => $propertyOwner->id,
                'agent_id' => $agent->id,
                'agency_id' => $agency->id,
                'is_featured' => rand(0, 1) === 1,
                'is_verified' => true,
                'is_published' => true,
                'verified_at' => now(),
                'published_at' => now(),
            ]);
        }
    }

    /**
     * Create Independent Agent scenarios
     */
    private function createIndependentAgentScenarios(): void
    {
        $this->command->info('👤 Creating Independent Agent Scenarios...');

        $states = State::with(['cities' => function($query) {
            $query->whereHas('areas');
        }, 'cities.areas'])->take(3)->get();

        // Create 6 independent agents across different states
        for ($i = 1; $i <= 6; $i++) {
            // Only use states that have cities with areas
            $statesWithAreas = $states->filter(function($state) {
                return $state->cities->isNotEmpty();
            });
            
            if ($statesWithAreas->isEmpty()) {
                $this->command->warn("No states with cities that have areas found, skipping remaining independent agents");
                break;
            }
            
            $state = $statesWithAreas->random();
            $city = $state->cities->random();
            $area = $city->areas->random();

            // Create agent user
            $agentUser = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => '+234' . rand(8000000000, 8999999999),
                'password' => bcrypt('password'),
                'user_type' => 'agent',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Assign independent_agent role
            $this->assignRoleToUser($agentUser, 'independent_agent');

            // Create independent agent profile
            $agent = Agent::create([
                'license_number' => 'IND/' . strtoupper(substr($state->name, 0, 3)) . '/' . rand(1000, 9999),
                'license_expiry_date' => now()->addYears(3),
                'bio' => "Independent real estate agent specializing in " . fake()->randomElement(['residential', 'commercial', 'luxury']) . " properties.",
                'specializations' => implode(',', fake()->randomElements(['residential', 'commercial', 'luxury', 'affordable'], rand(1, 2))),
                'years_experience' => rand(3, 15),
                'commission_rate' => rand(5, 10),
                'languages' => ['English', fake()->randomElement(['Yoruba', 'Hausa', 'Igbo'])],
                'service_areas' => [$city->name, fake()->randomElement($state->cities->pluck('name')->toArray())],
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(5, 30),
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => rand(0, 1) === 1,
                'accepts_new_clients' => true,
                'verified_at' => now(),
                'user_id' => $agentUser->id,
                'agency_id' => null, // Independent agent
            ]);

            // Create properties for independent agent
            $this->createPropertiesForIndependentAgent($agent, rand(8, 15));
        }

        $this->command->info('✓ Independent agent scenarios created');
    }

    private function createPropertiesForIndependentAgent(Agent $agent, int $propertyCount): void
    {
        $propertyTypes = PropertyType::with('propertySubtypes')->get();
        $cities = City::whereHas('areas')->with('areas')->get();

        for ($i = 1; $i <= $propertyCount; $i++) {
            if ($cities->isEmpty()) {
                $this->command->warn("Skipping independent agent property creation - no cities with areas");
                break;
            }
            
            $propertyType = $propertyTypes->random();
            
            // Skip if no subtypes available for this property type
            if ($propertyType->propertySubtypes->isEmpty()) {
                $this->command->warn("Skipping property creation - no subtypes for property type: {$propertyType->name}");
                continue;
            }
            
            $subtype = $propertyType->propertySubtypes->random();
            $city = $cities->random();
            
            $area = $city->areas->random();

            // Create a property owner managed by the independent agent
            $ownerType = fake()->randomElement([PropertyOwner::TYPE_INDIVIDUAL, PropertyOwner::TYPE_COMPANY]);
            
            if ($ownerType === PropertyOwner::TYPE_INDIVIDUAL) {
                $propertyOwner = PropertyOwner::create([
                    'type' => $ownerType,
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'email' => fake()->safeEmail(),
                    'phone' => '+234' . rand(8000000000, 8999999999),
                    'address' => fake()->address(),
                    'city' => $city->name,
                    'state' => $city->state->name,
                    'country' => 'Nigeria',
                    'agency_id' => null,
                    'agent_id' => $agent->id,
                    'notes' => 'Property owner managed by independent agent ' . $agent->user->name,
                    'is_active' => true,
                ]);
            } else {
                $propertyOwner = PropertyOwner::create([
                    'type' => $ownerType,
                    'company_name' => fake()->company(),
                    'email' => fake()->safeEmail(),
                    'phone' => '+234' . rand(8000000000, 8999999999),
                    'address' => fake()->address(),
                    'city' => $city->name,
                    'state' => $city->state->name,
                    'country' => 'Nigeria',
                    'tax_id' => 'TIN-' . rand(100000000, 999999999),
                    'agency_id' => null,
                    'agent_id' => $agent->id,
                    'notes' => 'Corporate property owner managed by independent agent ' . $agent->user->name,
                    'is_active' => true,
                ]);
            }

            $listingType = fake()->randomElement(['sale', 'rent']);
            $title = $this->generatePropertyTitle($propertyType->name, $subtype->name, $area->name);

            Property::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                'description' => $this->generatePropertyDescription($propertyType->name, $subtype->name),
                'listing_type' => $listingType,
                'status' => fake()->randomElement(['available', 'rented', 'sold', 'off_market']),
                'price' => $this->generatePrice($listingType, $propertyType->name),
                'price_period' => $listingType === 'rent' ? 'per_year' : null,
                'service_charge' => $listingType === 'rent' ? rand(50000, 200000) : null,
                'legal_fee' => rand(100000, 300000),
                'agency_fee' => rand(100000, 500000),
                'caution_deposit' => $listingType === 'rent' ? rand(500000, 2000000) : null,
                'bedrooms' => rand(1, 5),
                'bathrooms' => rand(1, 4),
                'toilets' => rand(1, 5),
                'size_sqm' => rand(80, 500),
                'parking_spaces' => rand(1, 4),
                'year_built' => rand(2010, 2024),
                'furnishing_status' => fake()->randomElement(['furnished', 'semi_furnished', 'unfurnished']),
                'address' => fake()->streetAddress(),
                'landmark' => fake()->randomElement(['Near Market', 'Main Street', 'Close to School', 'Residential Area']),
                'latitude' => fake()->latitude(6.0, 7.0),
                'longitude' => fake()->longitude(3.0, 4.0),
                'property_type_id' => $propertyType->id,
                'property_subtype_id' => $subtype->id,
                'state_id' => $city->state_id,
                'city_id' => $city->id,
                'area_id' => $area->id,
                'owner_id' => $propertyOwner->id,
                'agent_id' => $agent->id,
                'agency_id' => null, // Independent agent property
                'is_featured' => rand(0, 1) === 1,
                'is_verified' => true,
                'is_published' => true,
                'verified_at' => now(),
                'published_at' => now(),
            ]);
        }
    }

    /**
     * Create Landlord scenarios
     */
    private function createLandlordScenarios(): void
    {
        $this->command->info('🏠 Creating Landlord Scenarios...');

        $states = State::with(['cities' => function($query) {
            $query->whereHas('areas');
        }, 'cities.areas'])->take(3)->get();

        // Create 8 landlords with varying property portfolios
        for ($i = 1; $i <= 8; $i++) {
            $state = $states->random();
            $city = $state->cities->random();

            // Create landlord user
            $landlord = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => '+234' . rand(8000000000, 8999999999),
                'password' => bcrypt('password'),
                'user_type' => 'property_owner',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Assign landlord role
            $this->assignRoleToUser($landlord, 'landlord');

            // Create properties for landlord (direct listings)
            $propertyCount = rand(3, 12);
            $this->createPropertiesForLandlord($landlord, $propertyCount);
        }

        $this->command->info('✓ Landlord scenarios created');
    }

    private function createPropertiesForLandlord(User $landlord, int $propertyCount): void
    {
        // Create a PropertyOwner record for this landlord (similar to how Landlord panel registration works)
        $propertyOwner = PropertyOwner::create([
            'type' => PropertyOwner::TYPE_INDIVIDUAL,
            'first_name' => explode(' ', $landlord->name)[0],
            'last_name' => explode(' ', $landlord->name, 2)[1] ?? '',
            'email' => $landlord->email,
            'phone' => $landlord->phone,
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'Nigeria',
            'user_id' => $landlord->id,
            'agency_id' => null,
            'agent_id' => null,
            'notes' => 'Direct landlord property owner',
            'is_active' => true,
        ]);

        $propertyTypes = PropertyType::with('propertySubtypes')->get();
        $cities = City::whereHas('areas')->with('areas')->get();

        for ($i = 1; $i <= $propertyCount; $i++) {
            if ($cities->isEmpty()) {
                $this->command->warn("Skipping landlord property creation - no cities with areas");
                break;
            }
            
            $propertyType = $propertyTypes->random();
            
            // Skip if no subtypes available for this property type
            if ($propertyType->propertySubtypes->isEmpty()) {
                $this->command->warn("Skipping property creation - no subtypes for property type: {$propertyType->name}");
                continue;
            }
            
            $subtype = $propertyType->propertySubtypes->random();
            $city = $cities->random();
            
            $area = $city->areas->random();

            $listingType = 'rent'; // Landlords primarily rent properties
            $title = $this->generatePropertyTitle($propertyType->name, $subtype->name, $area->name);

            Property::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                'description' => $this->generatePropertyDescription($propertyType->name, $subtype->name),
                'listing_type' => $listingType,
                'status' => fake()->randomElement(['available', 'rented']),
                'price' => $this->generatePrice($listingType, $propertyType->name),
                'price_period' => 'per_year',
                'service_charge' => rand(50000, 200000),
                'legal_fee' => rand(100000, 300000),
                'agency_fee' => 0, // Direct listing, no agency fee
                'caution_deposit' => rand(500000, 2000000),
                'bedrooms' => rand(1, 5),
                'bathrooms' => rand(1, 4),
                'toilets' => rand(1, 5),
                'size_sqm' => rand(80, 500),
                'parking_spaces' => rand(1, 4),
                'year_built' => rand(2010, 2024),
                'furnishing_status' => fake()->randomElement(['furnished', 'semi_furnished', 'unfurnished']),
                'address' => fake()->streetAddress(),
                'landmark' => fake()->randomElement(['Corner Plot', 'Serene Environment', 'Gated Estate', 'Main Road']),
                'latitude' => fake()->latitude(6.0, 7.0),
                'longitude' => fake()->longitude(3.0, 4.0),
                'property_type_id' => $propertyType->id,
                'property_subtype_id' => $subtype->id,
                'state_id' => $city->state_id,
                'city_id' => $city->id,
                'area_id' => $area->id,
                'owner_id' => $propertyOwner->id, // Reference PropertyOwner, not User
                'agent_id' => null, // Direct listing
                'agency_id' => null, // Direct listing
                'is_featured' => rand(0, 1) === 1,
                'is_verified' => true,
                'is_published' => true,
                'verified_at' => now(),
                'published_at' => now(),
            ]);
        }
    }

    /**
     * Create Tenant scenarios and interactions
     */
    private function createTenantInteractions(): void
    {
        $this->command->info('👥 Creating Tenant Scenarios...');

        // Create 25 tenants with different profiles
        $tenants = [];
        for ($i = 1; $i <= 25; $i++) {
            // Create tenant user
            $tenantUser = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => '+234' . rand(8000000000, 8999999999),
                'password' => bcrypt('password'),
                'user_type' => 'tenant',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Assign tenant role
            $this->assignRoleToUser($tenantUser, 'tenant');

            // Create tenant profile
            $tenant = Tenant::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => $tenantUser->email,
                'phone' => $tenantUser->phone,
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_phone' => '+234' . rand(8000000000, 8999999999),
                'employment_status' => fake()->randomElement(['employed', 'self_employed', 'student', 'unemployed']),
                'employer_name' => fake()->company(),
                'monthly_income' => rand(150000, 1000000),
                'identification_type' => fake()->randomElement(['national_id', 'passport', 'drivers_license']),
                'identification_number' => fake()->regexify('[A-Z0-9]{10}'),
                'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years'),
                'nationality' => 'Nigerian',
                'occupation' => fake()->jobTitle(),
                'guarantor_name' => fake()->name(),
                'guarantor_phone' => '+234' . rand(8000000000, 8999999999),
                'guarantor_email' => fake()->safeEmail(),
                'is_active' => true,
                'user_id' => $tenantUser->id,
            ]);

            $tenants[] = $tenant;
        }

        $this->command->info('✓ Tenant scenarios created');
    }

    /**
     * Create comprehensive lease and payment cycles
     */
    private function createLeaseAndPaymentCycles(): void
    {
        $this->command->info('📋 Creating Lease and Payment Cycles...');

        // Get available rental properties
        $rentalProperties = Property::where('listing_type', 'rent')
            ->where('status', 'available')
            ->take(30)
            ->get();

        $tenants = Tenant::all();

        if ($rentalProperties->isEmpty() || $tenants->isEmpty()) {
            $this->command->warn('⚠️ Insufficient rental properties or tenants for lease creation');
            return;
        }

        foreach ($rentalProperties as $property) {
            // 70% chance of having an active lease
            if (rand(1, 100) <= 70) {
                $this->createLeaseForProperty($property, $tenants);
            }
        }

        $this->command->info('✓ Lease and payment cycles created');
    }

    private function createLeaseForProperty(Property $property, $tenants): void
    {
        $tenant = $tenants->random();
        
        // Determine landlord (property owner's linked user or agent's agency owner)
        $landlord = null;
        if ($property->owner_id) {
            // Get the PropertyOwner record and its linked user
            $propertyOwner = PropertyOwner::find($property->owner_id);
            if ($propertyOwner && $propertyOwner->user_id) {
                $landlord = User::find($propertyOwner->user_id);
            }
        } elseif ($property->agency_id) {
            $landlord = $property->agency->owner;
        }

        if (!$landlord) {
            return;
        }

        // Create lease
        $startDate = Carbon::parse(fake()->dateTimeBetween('-1 year', '+1 month'));
        $endDate = $startDate->copy()->addYear();

        $lease = Lease::create([
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'landlord_id' => $landlord->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'monthly_rent' => $property->price / 12, // Convert yearly to monthly
            'security_deposit' => $property->caution_deposit ?? ($property->price * 0.1),
            'service_charge' => $property->service_charge ?? 0,
            'legal_fee' => $property->legal_fee ?? 0,
            'agency_fee' => $property->agency_fee ?? 0,
            'caution_deposit' => $property->caution_deposit ?? 0,
            'lease_type' => Lease::TYPE_FIXED_TERM,
            'payment_frequency' => Lease::FREQUENCY_MONTHLY,
            'payment_method' => fake()->randomElement(['bank_transfer', 'cash', 'cheque']),
            'late_fee_amount' => 50000,
            'grace_period_days' => 7,
            'renewal_option' => true,
            'early_termination_fee' => $property->price * 0.2,
            'terms_and_conditions' => 'Standard lease agreement terms and conditions apply.',
            'status' => Lease::STATUS_ACTIVE,
            'signed_date' => $startDate,
            'move_in_date' => $startDate,
            'notes' => 'Property leased through ' . ($property->agency ? $property->agency->name : 'direct listing'),
        ]);

        // Update property status
        $property->update(['status' => 'rented']);

        // Create payment history
        $this->createPaymentHistoryForLease($lease);

        // Update tenant's landlord reference
        $tenant->update(['landlord_id' => $landlord->id]);
    }

    private function createPaymentHistoryForLease(Lease $lease): void
    {
        $monthlyRent = $lease->monthly_rent;
        $startDate = Carbon::parse($lease->start_date);
        $currentDate = Carbon::now();

        // Create payment records from lease start to current date
        $paymentDate = $startDate->copy();
        $paymentNumber = 1;

        while ($paymentDate->lte($currentDate)) {
            $dueDate = $paymentDate->copy();
            $actualPaymentDate = $paymentDate->copy();

            // 85% chance of on-time payment, 10% late, 5% pending
            $paymentStatus = fake()->randomElement([
                'paid', 'paid', 'paid', 'paid', 'paid', 'paid', 'paid', 'paid', // 80%
                'paid', // Additional 5% for 85% total
                'paid', 'overdue', // 10% late/overdue
                'pending' // 5% pending
            ]);

            // Adjust payment date based on status
            if ($paymentStatus === 'overdue') {
                $actualPaymentDate->addDays(rand(8, 20)); // Late payment
            } elseif ($paymentStatus === 'pending') {
                $actualPaymentDate = null; // No payment yet
            }

            $lateFee = ($paymentStatus === 'overdue') ? $lease->late_fee_amount : 0;

            RentPayment::create([
                'lease_id' => $lease->id,
                'tenant_id' => $lease->tenant_id,
                'landlord_id' => $lease->landlord_id,
                'property_id' => $lease->property_id,
                'amount' => $monthlyRent,
                'payment_date' => $actualPaymentDate,
                'due_date' => $dueDate,
                'payment_method' => $lease->payment_method,
                'payment_reference' => 'REF-' . strtoupper(fake()->bothify('??###')),
                'late_fee' => $lateFee,
                'discount' => 0,
                'net_amount' => $monthlyRent + $lateFee,
                'status' => $paymentStatus,
                'payment_for_period' => $paymentDate->format('F Y'),
                'notes' => $paymentStatus === 'overdue' ? 'Late payment with penalty' : null,
                'receipt_number' => 'RCP-' . str_pad($paymentNumber, 6, '0', STR_PAD_LEFT),
            ]);

            $paymentDate->addMonth();
            $paymentNumber++;
        }
    }

    /**
     * Create property inquiries and viewings
     */
    private function createPropertyInquiriesAndViewings(): void
    {
        $this->command->info('💬 Creating Property Inquiries and Viewings...');

        $properties = Property::where('is_published', true)->take(50)->get();
        $tenants = User::where('user_type', 'tenant')->get();

        if ($properties->isEmpty() || $tenants->isEmpty()) {
            $this->command->warn('⚠️ Insufficient properties or tenants for inquiries');
            return;
        }

        foreach ($properties as $property) {
            // 60% chance of having inquiries
            if (rand(1, 100) <= 60) {
                $inquiryCount = rand(1, 5);
                for ($i = 0; $i < $inquiryCount; $i++) {
                    $this->createPropertyInquiry($property, $tenants);
                }
            }
        }

        $this->command->info('✓ Property inquiries and viewings created');
    }

    private function createPropertyInquiry(Property $property, $tenants): void
    {
        $inquirer = $tenants->random();
        $inquiryDate = Carbon::parse(fake()->dateTimeBetween('-30 days', 'now'));

        $messages = [
            'Hi, I\'m interested in viewing this property. When would be convenient?',
            'Is this property still available? I would like to schedule a viewing.',
            'Could you provide more details about the neighborhood and amenities?',
            'What are the lease terms and move-in requirements?',
            'I\'m looking to move in next month. Is that possible?',
            'Is the rent negotiable? I\'m a reliable tenant.',
            'What utilities are included in the rental price?',
            'Can you arrange a viewing this weekend?',
        ];

        $inquiry = PropertyInquiry::create([
            'property_id' => $property->id,
            'inquirer_id' => $inquirer->id,
            'inquirer_name' => $inquirer->name,
            'inquirer_email' => $inquirer->email,
            'inquirer_phone' => $inquirer->phone,
            'message' => fake()->randomElement($messages),
            'preferred_viewing_date' => fake()->dateTimeBetween('now', '+14 days'),
            'status' => fake()->randomElement(['new', 'contacted', 'scheduled', 'viewed', 'closed']),
            'created_at' => $inquiryDate,
        ]);

        // 50% chance of creating a viewing if inquiry status is appropriate
        if (in_array($inquiry->status, ['scheduled', 'viewed']) && rand(1, 100) <= 50) {
            $this->createPropertyViewing($property, $inquirer, $inquiry);
        }
    }

    private function createPropertyViewing(Property $property, User $inquirer, PropertyInquiry $inquiry): void
    {
        $viewingDate = Carbon::parse(fake()->dateTimeBetween($inquiry->created_at, '+14 days'));
        $status = $viewingDate->isPast() ? 
            fake()->randomElement(['completed', 'cancelled', 'no_show']) : 
            fake()->randomElement(['scheduled', 'confirmed']);

        // Get the agent's user ID if the property has an agent
        $agentUserId = null;
        if ($property->agent_id) {
            $agent = Agent::find($property->agent_id);
            if ($agent && $agent->user_id) {
                $agentUserId = $agent->user_id;
            }
        }

        PropertyViewing::create([
            'property_id' => $property->id,
            'inquirer_id' => $inquirer->id,
            'agent_id' => $agentUserId, // Use the agent's user ID, not the agent profile ID
            'scheduled_date' => $viewingDate->toDateString(),
            'scheduled_time' => $viewingDate->setTime(rand(9, 17), rand(0, 1) * 30),
            'status' => $status,
            'notes' => $status === 'completed' ? 
                'Client showed genuine interest in the property.' : 
                ($status === 'no_show' ? 'Client did not show up for viewing.' : null),
            'completed_at' => $status === 'completed' ? $viewingDate : null,
            'cancelled_at' => $status === 'cancelled' ? $viewingDate->subHours(2) : null,
            'cancellation_reason' => $status === 'cancelled' ? 'Schedule conflict' : null,
        ]);
    }

    /**
     * Helper methods for generating realistic data
     */
    private function generatePropertyTitle(string $propertyType, string $subtype, string $area): string
    {
        $descriptors = [
            'Luxury', 'Modern', 'Spacious', 'Beautiful', 'Executive', 'Tastefully', 'Well-furnished',
            'Newly Built', 'Renovated', 'Premium', 'Elegant', 'Contemporary', 'Serviced'
        ];

        $descriptor = fake()->randomElement($descriptors);
        return "{$descriptor} {$subtype} in {$area}";
    }

    private function generatePropertyDescription(string $propertyType, string $subtype): string
    {
        $features = [
            'fitted kitchen', 'spacious living room', 'master bedroom with en-suite',
            'backup generator', '24/7 security', 'swimming pool', 'gymnasium',
            'ample parking space', 'serene environment', 'good road network',
            'close to amenities', 'family-friendly neighborhood'
        ];

        $selectedFeatures = fake()->randomElements($features, rand(3, 6));
        
        return "This {$subtype} offers " . implode(', ', $selectedFeatures) . 
               ". Perfect for families and professionals seeking comfort and convenience.";
    }

    private function generatePrice(string $listingType, string $propertyType): int
    {
        $basePrice = match($propertyType) {
            'Residential' => rand(800000, 5000000),
            'Commercial' => rand(2000000, 15000000),
            'Industrial' => rand(3000000, 20000000),
            'Land' => rand(500000, 10000000),
            default => rand(1000000, 5000000),
        };

        // Sale prices are typically higher than rental prices
        return $listingType === 'sale' ? $basePrice * rand(10, 50) : $basePrice;
    }

    /**
     * Display testing credentials for easy access
     */
    private function showTestingCredentials(): void
    {
        $this->command->line('');
        $this->command->info('🔑 TESTING CREDENTIALS (Password: password)');
        $this->command->line('═══════════════════════════════════════════');
        
        // Show agency owner credentials with super_admin role
        $agencyOwner = User::where('user_type', 'agency_owner')->first();
        if ($agencyOwner) {
            $agency = $agencyOwner->agencies()->first();
            $this->command->info("Agency Owner (super_admin role): {$agencyOwner->email}");
            if ($agency) {
                $this->command->line("  Agency: {$agency->name}");
            }
        }
        $this->command->line('');
        
        // Show sample agent credentials with agent role
        $agent = Agent::with('user', 'agency')->whereNotNull('agency_id')->first();
        if ($agent && $agent->user) {
            $this->command->info("Sample Agent (agent role): {$agent->user->email}");
            if ($agent->agency) {
                $this->command->line("  Agency: {$agent->agency->name}");
            }
        }
        $this->command->line('');
        
        // Show independent agent credentials with independent_agent role
        $independentAgent = Agent::with('user')->whereNull('agency_id')->first();
        if ($independentAgent && $independentAgent->user) {
            $this->command->info("Independent Agent (independent_agent role): {$independentAgent->user->email}");
        }
        $this->command->line('');
        
        // Show landlord credentials with landlord role
        $landlord = User::where('user_type', 'property_owner')->first();
        if ($landlord) {
            $this->command->info("Sample Landlord (landlord role): {$landlord->email}");
        }
        $this->command->line('');
        
        // Show tenant credentials with tenant role
        $tenant = User::where('user_type', 'tenant')->first();
        if ($tenant) {
            $this->command->info("Sample Tenant (tenant role): {$tenant->email}");
        }
        $this->command->line('');

        $this->command->info('📱 PANEL ACCESS:');
        $this->command->line('• Agency Panel (/agency): Agency owners (super_admin) and agents (agent role)');
        $this->command->line('• Agent Panel (/agent): Independent agents (independent_agent role)');
        $this->command->line('• Landlord Panel (/landlord): Property owners (landlord role)');
        $this->command->line('• Tenant Panel (/tenant): Tenants (tenant role)');
        $this->command->line('');
        
        $this->command->info('🎯 Test Scenarios Created:');
        $this->command->line('  ✓ Agency listings with agent management');
        $this->command->line('  ✓ Independent agent listings');
        $this->command->line('  ✓ Direct landlord listings');
        $this->command->line('  ✓ Active leases with payment histories');
        $this->command->line('  ✓ Property inquiries and viewings');
        $this->command->line('  ✓ Multi-panel access scenarios');
        $this->command->line('');
        
        $this->command->info('📊 Summary Statistics:');
        $this->command->line('  • Agencies: ' . Agency::count());
        $this->command->line('  • Agents: ' . Agent::count());
        $this->command->line('  • Properties: ' . Property::count());
        $this->command->line('  • Tenants: ' . Tenant::count());
        $this->command->line('  • Active Leases: ' . Lease::where('status', 'active')->count());
        $this->command->line('  • Rent Payments: ' . RentPayment::count());
        $this->command->line('  • Property Inquiries: ' . PropertyInquiry::count());
        $this->command->line('  • Property Viewings: ' . PropertyViewing::count());
    }

    /**
     * Assign a role to a user
     */
    private function assignRoleToUser(User $user, string $roleName): void
    {
        try {
            // Ensure the role exists
            $role = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            if (!$role) {
                $this->command->warn("Role '{$roleName}' not found. Please run ShieldSeeder first.");
                return;
            }

            // Assign the role
            $user->assignRole($role);
            $this->command->info("✓ Assigned '{$roleName}' role to {$user->email}");

        } catch (\Exception $e) {
            $this->command->error("Failed to assign role '{$roleName}' to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Assign super_admin role to user with agency context
     */
    private function assignSuperAdminRoleToUser(User $user, Agency $agency): void
    {
        try {
            // First, ensure the agency-specific super_admin role exists
            $this->ensureSuperAdminRoleExists($agency);
            
            // Find the agency-specific super_admin role
            $role = Role::where('name', 'super_admin')
                ->where('guard_name', 'web')
                ->where('agency_id', $agency->id)
                ->first();

            if (!$role) {
                $this->command->error("Failed to find or create agency-specific super_admin role for agency '{$agency->name}'");
                return;
            }

            // Set team context for Spatie permissions
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($agency->id);

            // Assign the agency-specific role
            $user->assignRole($role);
            $this->command->info("✓ Assigned super_admin role to {$user->email} for agency '{$agency->name}'");

        } catch (\Exception $e) {
            $this->command->error("Failed to assign super_admin role to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Assign agent role to user with agency context
     */
    private function assignAgentRoleToUser(User $user, Agency $agency): void
    {
        try {
            // First, ensure the agency-specific agent role exists
            $this->ensureAgentRoleExists($agency);
            
            // Find the agency-specific agent role
            $role = Role::where('name', 'agent')
                ->where('guard_name', 'web')
                ->where('agency_id', $agency->id)
                ->first();

            if (!$role) {
                $this->command->error("Failed to find or create agency-specific agent role for agency '{$agency->name}'");
                return;
            }

            // Set team context for Spatie permissions
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($agency->id);

            // Assign the agency-specific role
            $user->assignRole($role);
            $this->command->info("✓ Assigned agent role to {$user->email} for agency '{$agency->name}'");

        } catch (\Exception $e) {
            $this->command->error("Failed to assign agent role to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Ensure the super_admin role exists for the given agency
     */
    private function ensureSuperAdminRoleExists(Agency $agency): void
    {
        // Check if the 'super_admin' role exists for this agency
        $superAdminRole = Role::where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->where('agency_id', $agency->id)
            ->first();

        if (!$superAdminRole) {
            // Set team context for Spatie permissions
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($agency->id);
            
            // Get the super_admin permissions that should exist globally
            $superAdminPermissions = [
                // Role permissions
                'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role',
                // Agent permissions
                'view_agent', 'view_any_agent', 'create_agent', 'update_agent', 'restore_agent', 'restore_any_agent',
                'replicate_agent', 'reorder_agent', 'delete_agent', 'delete_any_agent', 'force_delete_agent', 'force_delete_any_agent',
                // Property permissions
                'view_property', 'view_any_property', 'create_property', 'update_property', 'restore_property', 'restore_any_property',
                'replicate_property', 'reorder_property', 'delete_property', 'delete_any_property', 'force_delete_property', 'force_delete_any_property',
                // Property inquiry permissions
                'view_property::inquiry', 'view_any_property::inquiry', 'create_property::inquiry', 'update_property::inquiry',
                'restore_property::inquiry', 'restore_any_property::inquiry', 'replicate_property::inquiry', 'reorder_property::inquiry',
                'delete_property::inquiry', 'delete_any_property::inquiry', 'force_delete_property::inquiry', 'force_delete_any_property::inquiry',
                // Property viewing permissions
                'view_property::viewing', 'view_any_property::viewing', 'create_property::viewing', 'update_property::viewing',
                'restore_property::viewing', 'restore_any_property::viewing', 'replicate_property::viewing', 'reorder_property::viewing',
                'delete_property::viewing', 'delete_any_property::viewing', 'force_delete_property::viewing', 'force_delete_any_property::viewing',
                // Page and widget permissions
                'page_AgencyDashboard', 'widget_AgencyStatsWidget', 'widget_PropertiesChartWidget',
                // Tenant menu and profile permissions
                'view_tenant_menu', 'update_agency_profile'
            ];

            // Find or create global permissions first
            $permissions = collect($superAdminPermissions)->map(function ($permissionName) {
                return Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            });

            // Create the agency-specific role
            $superAdminRole = Role::create([
                'name' => 'super_admin',
                'guard_name' => 'web',
                'agency_id' => $agency->id,
            ]);

            // Assign permissions to the role
            if ($permissions->isNotEmpty()) {
                $superAdminRole->syncPermissions($permissions);
                $this->command->info("✓ Created agency-specific super_admin role for '{$agency->name}' with " . $permissions->count() . " permissions");
            }
        } else {
            $this->command->info("✓ Using existing agency-specific super_admin role for '{$agency->name}'");
        }
    }

    /**
     * Ensure the agent role exists for the given agency
     */
    private function ensureAgentRoleExists(Agency $agency): void
    {
        // Check if the 'agent' role exists for this agency
        $agentRole = Role::where('name', 'agent')
            ->where('guard_name', 'web')
            ->where('agency_id', $agency->id)
            ->first();

        if (!$agentRole) {
            // Set team context for Spatie permissions
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($agency->id);
            
            // Get the basic agent permissions that should exist globally
            $agentPermissions = [
                'view_property', 'view_any_property', 'create_property', 'update_property',
                'view_property::inquiry', 'view_any_property::inquiry', 'create_property::inquiry', 'update_property::inquiry',
                'view_property::viewing', 'view_any_property::viewing', 'create_property::viewing', 'update_property::viewing',
                'page_AgencyDashboard', 'widget_AgencyStatsWidget', 'widget_PropertiesChartWidget',
                'view_tenant_menu'
            ];

            // Find or create global permissions first
            $permissions = collect($agentPermissions)->map(function ($permissionName) {
                return Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            });

            // Create the agency-specific role
            $agentRole = Role::create([
                'name' => 'agent',
                'guard_name' => 'web',
                'agency_id' => $agency->id,
            ]);

            // Assign permissions to the role
            if ($permissions->isNotEmpty()) {
                $agentRole->syncPermissions($permissions);
                $this->command->info("✓ Created agency-specific agent role for '{$agency->name}' with " . $permissions->count() . " permissions");
            }
        } else {
            $this->command->info("✓ Using existing agency-specific agent role for '{$agency->name}'");
        }
    }
}
