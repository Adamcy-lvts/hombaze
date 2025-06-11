<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Models\Agent;
use App\Models\State;
use App\Models\Agency;
use App\Models\Property;
use Illuminate\Support\Str;
use App\Models\PropertyType;
use App\Models\PropertyFeature;
use App\Models\PropertySubtype;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Phase5PropertySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Phase 5: Core Property System...');
        $this->validatePrerequisites();
        $this->clearExistingRecords();
        $this->seedProperties();
        $this->command->info('Phase 5 Core Property System seeded successfully!');
    }

    /**
     * Validate that all prerequisite data exists
     */
    private function validatePrerequisites(): void
    {
        $this->command->info('Validating prerequisites...');
        
        $counts = [
            'states' => State::count(),
            'cities' => City::count(),
            'areas' => Area::count(),
            'property_types' => PropertyType::count(),
            'property_subtypes' => PropertySubtype::count(),
            'property_features' => PropertyFeature::count(),
            'users' => User::count(),
            'agents' => Agent::count(),
            'agencies' => Agency::count(),
        ];

        foreach ($counts as $entity => $count) {
            if ($count === 0) {
                throw new \Exception("Cannot seed properties: No {$entity} found. Please run previous phases first.");
            }
        }

        $this->command->info("Prerequisites validated: " . implode(', ', array_map(
            fn($k, $v) => "{$v} {$k}",
            array_keys($counts),
            $counts
        )));
    }

    /**
     * Clear existing property records
     */
    private function clearExistingRecords(): void
    {
        $this->command->info('Clearing existing Phase 5 records...');
        
        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear in dependency order
        DB::table('property_feature_property')->truncate();
        DB::table('properties')->truncate();
        
        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Seed properties with sample data
     */
    private function seedProperties(): void
    {
        $this->command->info('Creating sample properties...');

        // Get reference data
        $propertyTypes = PropertyType::with('propertySubtypes')->get();
        $states = State::with(['cities.areas'])->get();
        $agents = Agent::with('agency')->get();
        $users = User::where('user_type', 'property_owner')->get();
        $features = PropertyFeature::all();

        // If no property owners exist, use any users
        if ($users->isEmpty()) {
            $this->command->info('No property owners found, using all users...');
            $users = User::all();
        }

        // Validate we have all required data
        $this->command->info(sprintf(
            'Data counts: users=%d, agents=%d, features=%d, states=%d, propertyTypes=%d',
            $users->count(),
            $agents->count(),
            $features->count(),
            $states->count(),
            $propertyTypes->count()
        ));

        if ($users->isEmpty()) {
            throw new \Exception('No users found to assign as property owners');
        }
        if ($agents->isEmpty()) {
            throw new \Exception('No agents found to assign to properties');
        }
        if ($features->isEmpty()) {
            throw new \Exception('No property features found');
        }
        if ($states->isEmpty()) {
            throw new \Exception('No states found');
        }

        $properties = [];
        $propertyFeatures = [];
        $currentId = 1;

        // Generate diverse property data
        $listingTypes = ['sale', 'rent', 'lease'];
        $statuses = ['available', 'sold', 'rented'];
        $furnishingStatuses = ['furnished', 'semi_furnished', 'unfurnished'];

        foreach ($propertyTypes as $propertyType) {
            $subtypes = $propertyType->propertySubtypes;
            
            // Skip if no subtypes exist for this property type
            if (!$subtypes || $subtypes->isEmpty()) {
                $this->command->info("Skipping {$propertyType->name} - no subtypes found");
                continue;
            }
            
            foreach ($subtypes as $subtype) {
                // Create 3-5 properties per subtype
                $propertyCount = rand(3, 5);
                
                for ($i = 0; $i < $propertyCount; $i++) {
                    // Safely get random items with validation
                    if ($states->isEmpty()) {
                        throw new \Exception('No states available');
                    }
                    $state = $states->random();
                    
                    if ($state->cities->isEmpty()) {
                        $this->command->info("Skipping {$state->name} - no cities found");
                        continue;
                    }
                    $city = $state->cities->random();
                    
                    if ($city->areas->isEmpty()) {
                        $this->command->info("Skipping {$city->name} - no areas found");
                        continue;
                    }
                    $area = $city->areas->random();
                    
                    if ($agents->isEmpty()) {
                        throw new \Exception('No agents available');
                    }
                    $agent = $agents->random();
                    
                    if ($users->isEmpty()) {
                        throw new \Exception('No users available');
                    }
                    $owner = $users->random();
                    
                    $listingType = $listingTypes[array_rand($listingTypes)];
                    $status = $statuses[array_rand($statuses)];
                    
                    // Generate realistic pricing based on property type and listing type
                    $basePrice = $this->generateBasePrice($propertyType->name, $listingType);
                    $price = $basePrice + rand(-$basePrice * 0.3, $basePrice * 0.5);
                    
                    $title = $this->generatePropertyTitle($subtype->name, $area->name, $city->name);
                    $slug = Str::slug($title . '-' . $currentId);
                    
                    $bedrooms = $this->generateBedrooms($subtype->name);
                    $bathrooms = rand(1, max(1, $bedrooms));
                    $toilets = rand($bathrooms, $bathrooms + 2);
                    
                    $property = [
                        'id' => $currentId,
                        'title' => $title,
                        'slug' => $slug,
                        'description' => $this->generateDescription($subtype->name, $area->name, $city->name),
                        'listing_type' => $listingType,
                        'status' => $status,
                        'price' => $price,
                        'price_period' => $listingType === 'rent' ? 'per_year' : null,
                        'service_charge' => $listingType === 'rent' ? rand(50000, 200000) : null,
                        'legal_fee' => $listingType === 'sale' ? rand(100000, 500000) : null,
                        'agency_fee' => rand(50000, 300000),
                        'caution_deposit' => $listingType === 'rent' ? $price : null,
                        'bedrooms' => $bedrooms,
                        'bathrooms' => $bathrooms,
                        'toilets' => $toilets,
                        'size_sqm' => rand(50, 500),
                        'parking_spaces' => rand(0, 4),
                        'year_built' => rand(1990, 2024),
                        'furnishing_status' => $furnishingStatuses[array_rand($furnishingStatuses)],
                        'address' => $this->generateAddress($area->name, $city->name, $state->name),
                        'landmark' => $this->generateLandmark($area->name),
                        'latitude' => $this->generateLatitude($state->name),
                        'longitude' => $this->generateLongitude($state->name),
                        'property_type_id' => $propertyType->id,
                        'property_subtype_id' => $subtype->id,
                        'state_id' => $state->id,
                        'city_id' => $city->id,
                        'area_id' => $area->id,
                        'owner_id' => $owner->id,
                        'agent_id' => $agent->id,
                        'agency_id' => $agent->agency_id,
                        'meta_title' => $title . ' - ' . $city->name . ' Property',
                        'meta_description' => substr($this->generateDescription($subtype->name, $area->name, $city->name), 0, 160),
                        'is_published' => rand(0, 10) > 2, // 80% published
                        'is_featured' => rand(0, 10) > 7, // 30% featured
                        'featured_until' => rand(0, 10) > 7 ? now()->addDays(rand(7, 30)) : null,
                        'view_count' => rand(0, 1000),
                        'inquiry_count' => rand(0, 50),
                        'created_at' => now()->subDays(rand(1, 365)),
                        'updated_at' => now()->subDays(rand(0, 30)),
                    ];

                    $properties[] = $property;

                    // Assign random features to property
                    if (!$features->isEmpty()) {
                        $featureCount = min(rand(3, 8), $features->count());
                        $selectedFeatures = $features->random($featureCount);
                        foreach ($selectedFeatures as $feature) {
                            $propertyFeatures[] = [
                                'property_id' => $currentId,
                                'property_feature_id' => $feature->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    $currentId++;
                }
            }
        }

        // Insert properties in batches
        $batches = array_chunk($properties, 100);
        foreach ($batches as $batch) {
            DB::table('properties')->insert($batch);
        }

        // Insert property features in batches
        $featureBatches = array_chunk($propertyFeatures, 500);
        foreach ($featureBatches as $batch) {
            DB::table('property_feature_property')->insert($batch);
        }

        $this->command->info(sprintf(
            'Properties created: %d with %d feature associations',
            count($properties),
            count($propertyFeatures)
        ));
    }

    /**
     * Generate base price based on property type and listing type
     */
    private function generateBasePrice(string $propertyType, string $listingType): int
    {
        $basePrices = [
            'sale' => [
                'Residential' => 25000000, // 25M
                'Commercial' => 50000000,  // 50M
                'Industrial' => 75000000,  // 75M
                'Agricultural' => 15000000, // 15M
                'Mixed Use' => 35000000,   // 35M
                'Special Purpose' => 40000000, // 40M
            ],
            'rent' => [
                'Residential' => 1200000,  // 1.2M annually
                'Commercial' => 3000000,   // 3M annually
                'Industrial' => 5000000,   // 5M annually
                'Agricultural' => 800000,  // 800K annually
                'Mixed Use' => 2500000,    // 2.5M annually
                'Special Purpose' => 2000000, // 2M annually
            ],
            'lease' => [
                'Residential' => 800000,   // 800K annually
                'Commercial' => 2000000,   // 2M annually
                'Industrial' => 3500000,   // 3.5M annually
                'Agricultural' => 500000,  // 500K annually
                'Mixed Use' => 1800000,    // 1.8M annually
                'Special Purpose' => 1500000, // 1.5M annually
            ],
        ];

        return $basePrices[$listingType][$propertyType] ?? 10000000;
    }

    /**
     * Generate property title
     */
    private function generatePropertyTitle(string $subtype, string $area, string $city): string
    {
        $descriptors = ['Modern', 'Luxury', 'Executive', 'Premium', 'Spacious', 'Beautiful', 'Elegant', 'Contemporary'];
        $descriptor = $descriptors[array_rand($descriptors)];
        
        return "{$descriptor} {$subtype} in {$area}, {$city}";
    }

    /**
     * Generate property description
     */
    private function generateDescription(string $subtype, string $area, string $city): string
    {
        $templates = [
            "A {subtype} located in the heart of {area}, {city}. This property offers modern amenities and excellent connectivity to major landmarks.",
            "Discover this exceptional {subtype} in {area}, {city}. Perfect for those seeking comfort, convenience, and quality living.",
            "Prime {subtype} situated in the prestigious {area} area of {city}. Features contemporary design and premium finishes.",
            "Well-appointed {subtype} in {area}, {city}. Ideal investment opportunity with great potential for appreciation.",
        ];
        
        $template = $templates[array_rand($templates)];
        return str_replace(['{subtype}', '{area}', '{city}'], [strtolower($subtype), $area, $city], $template);
    }

    /**
     * Generate bedrooms based on property subtype
     */
    private function generateBedrooms(string $subtype): int
    {
        $bedroomMap = [
            'Studio Apartment' => 0,
            'One Bedroom Apartment' => 1,
            'Two Bedroom Apartment' => 2,
            'Three Bedroom Apartment' => 3,
            'Penthouse' => rand(3, 5),
            'Detached House' => rand(3, 6),
            'Semi-Detached House' => rand(2, 4),
            'Terraced House' => rand(2, 4),
            'Townhouse' => rand(2, 4),
            'Bungalow' => rand(2, 4),
            'Mansion' => rand(5, 8),
            'Villa' => rand(3, 6),
        ];

        return $bedroomMap[$subtype] ?? rand(1, 4);
    }

    /**
     * Generate address
     */
    private function generateAddress(string $area, string $city, string $state): string
    {
        $streetNumbers = [rand(1, 200), rand(1, 50) . chr(rand(65, 90))];
        $streetNames = ['Main Street', 'High Street', 'Victoria Avenue', 'Independence Way', 'Freedom Road', 'Unity Close'];
        
        $number = $streetNumbers[array_rand($streetNumbers)];
        $street = $streetNames[array_rand($streetNames)];
        
        return "{$number} {$street}, {$area}, {$city}, {$state}";
    }

    /**
     * Generate landmark
     */
    private function generateLandmark(string $area): string
    {
        $landmarks = [
            'Shopping Mall', 'Central Park', 'Government House', 'University Campus',
            'Sports Complex', 'Hospital', 'Police Station', 'Market Square',
            'Business District', 'Embassy Row'
        ];
        
        $landmark = $landmarks[array_rand($landmarks)];
        return "Near {$area} {$landmark}";
    }

    /**
     * Generate latitude for Nigerian states
     */
    private function generateLatitude(string $state): float
    {
        // Approximate latitude ranges for major Nigerian regions
        $ranges = [
            'Lagos' => [6.4, 6.6],
            'Abuja' => [9.0, 9.1],
            'Kano' => [11.9, 12.1],
            'Rivers' => [4.7, 4.9],
            'Oyo' => [7.3, 7.5],
        ];
        
        $range = $ranges[$state] ?? [6.0, 12.0]; // Default Nigeria range
        return round(rand($range[0] * 1000000, $range[1] * 1000000) / 1000000, 6);
    }

    /**
     * Generate longitude for Nigerian states
     */
    private function generateLongitude(string $state): float
    {
        // Approximate longitude ranges for major Nigerian regions
        $ranges = [
            'Lagos' => [3.3, 3.5],
            'Abuja' => [7.4, 7.6],
            'Kano' => [8.4, 8.6],
            'Rivers' => [6.9, 7.1],
            'Oyo' => [3.8, 4.0],
        ];
        
        $range = $ranges[$state] ?? [3.0, 15.0]; // Default Nigeria range
        return round(rand($range[0] * 1000000, $range[1] * 1000000) / 1000000, 6);
    }
}
