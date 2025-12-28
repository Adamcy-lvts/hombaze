<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Agent;
use App\Models\Area;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyOwner;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedNigerianPropertyListings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'seed:nigeria-properties
                            {--maiduguri=40 : Properties for Maiduguri, Borno}
                            {--abuja=30 : Properties for Abuja (FCT)}
                            {--kano=30 : Properties for Kano}
                            {--kaduna=30 : Properties for Kaduna}
                            {--images=1 : Attach images from the internet (1/0)}';

    /**
     * The console command description.
     */
    protected $description = 'Seed Nigerian properties with agencies, agents, owners, and images for Maiduguri, Abuja, Kano, and Kaduna';

    public function handle(): int
    {
        $propertyTypes = PropertyType::with('propertySubtypes')->get();
        if ($propertyTypes->isEmpty()) {
            $this->error('No property types found. Run the core data seeders first.');
            return self::FAILURE;
        }

        $withImages = filter_var($this->option('images'), FILTER_VALIDATE_BOOLEAN);
        $locations = $this->locationConfig();

        foreach ($locations as $key => $location) {
            $count = (int) $this->option($key);
            if ($count <= 0) {
                continue;
            }

            $this->seedLocation($location, $count, $propertyTypes, $withImages);
        }

        $this->info('✅ Nigerian property seeding completed.');
        return self::SUCCESS;
    }

    private function seedLocation(array $location, int $count, Collection $propertyTypes, bool $withImages): void
    {
        $this->info("📍 Seeding {$count} properties for {$location['label']}...");

        $state = $this->findState($location['state_code'], $location['state_name']);
        if (!$state) {
            $this->error("State not found for {$location['label']}.");
            return;
        }

        $city = $this->findOrCreateCity($state, $location['city_candidates']);
        if (!$city) {
            $this->error("City not found for {$location['label']}.");
            return;
        }

        $areas = $this->ensureAreas($city, $location['areas']);
        if ($areas->isEmpty()) {
            $this->error("No areas available for {$location['label']}.");
            return;
        }

        $agency = $this->createAgency($location, $state, $city, $areas);
        $agencyAgents = $this->createAgencyAgents($location, $agency);
        $independentAgents = $this->createIndependentAgents($location);

        $agencyPropertyCount = (int) round($count * 0.7);
        $independentPropertyCount = $count - $agencyPropertyCount;

        $agencyOwners = $this->createOwnerPool(
            max(5, (int) ceil($agencyPropertyCount / 3)),
            $location,
            $state,
            $city,
            $areas,
            $agency,
            null
        );

        $independentOwners = $this->createOwnerPool(
            max(3, (int) ceil($independentPropertyCount / 3)),
            $location,
            $state,
            $city,
            $areas,
            null,
            $independentAgents
        );

        for ($i = 0; $i < $agencyPropertyCount; $i++) {
            $agent = $agencyAgents->random();
            $owner = $agencyOwners->random();
            $area = $areas[$i % $areas->count()];

            $property = $this->createProperty(
                $location,
                $state,
                $city,
                $area,
                $propertyTypes,
                $owner,
                $agent,
                $agency
            );

            $this->attachImages($property, $withImages);
        }

        for ($i = 0; $i < $independentPropertyCount; $i++) {
            $agent = $independentAgents->random();
            $owner = $independentOwners->random();
            $area = $areas[($i + $agencyPropertyCount) % $areas->count()];

            $property = $this->createProperty(
                $location,
                $state,
                $city,
                $area,
                $propertyTypes,
                $owner,
                $agent,
                null
            );

            $this->attachImages($property, $withImages);
        }

        $this->info("✅ {$location['label']} seeded successfully.");
    }

    private function locationConfig(): array
    {
        return [
            'maiduguri' => [
                'label' => 'Maiduguri, Borno',
                'state_code' => 'BO',
                'state_name' => 'Borno',
                'city_candidates' => ['Maiduguri'],
                'areas' => [
                    ['name' => 'GRA (Government Reserved Area)', 'type' => 'residential'],
                    ['name' => 'Bulumkutu', 'type' => 'residential'],
                    ['name' => 'Gwange', 'type' => 'residential'],
                    ['name' => 'Pompomari', 'type' => 'residential'],
                    ['name' => 'Mairi', 'type' => 'residential'],
                    ['name' => 'Shehuri North', 'type' => 'residential'],
                    ['name' => 'Shehuri South', 'type' => 'residential'],
                    ['name' => 'Baga Road', 'type' => 'mixed'],
                    ['name' => 'Gamboru Market', 'type' => 'commercial'],
                    ['name' => 'Post Office Area', 'type' => 'commercial'],
                ],
                'agency' => [
                    'name' => 'Sahel Homes & Realty',
                    'email_domain' => 'sahelhomes.ng',
                    'phone_prefix' => '+234806',
                ],
                'agent_names' => [
                    'Amina Shehu', 'Usman Ibrahim', 'Zainab Sani', 'Ibrahim Yerima',
                    'Halima Bukar', 'Kassim Audu', 'Hadiza Lawan', 'Mustapha Garba',
                ],
                'independent_agent_names' => [
                    'Yusuf Bintu', 'Zara Ali', 'Babagana Musa', 'Maryam Gana',
                ],
                'pricing' => [
                    'sale_min' => 5000000,
                    'sale_max' => 90000000,
                    'rent_min' => 250000,
                    'rent_max' => 1500000,
                    'shortlet_min' => 25000,
                    'shortlet_max' => 85000,
                ],
            ],
            'abuja' => [
                'label' => 'Abuja, FCT',
                'state_code' => 'FC',
                'state_name' => 'FCT - Abuja',
                'city_candidates' => ['Abuja', 'Abuja Municipal', 'Garki', 'Wuse'],
                'areas' => [
                    ['name' => 'Maitama', 'type' => 'residential'],
                    ['name' => 'Asokoro', 'type' => 'residential'],
                    ['name' => 'Wuse 2', 'type' => 'commercial'],
                    ['name' => 'Garki 2', 'type' => 'commercial'],
                    ['name' => 'Gwarinpa', 'type' => 'residential'],
                    ['name' => 'Jabi', 'type' => 'mixed'],
                    ['name' => 'Utako', 'type' => 'mixed'],
                    ['name' => 'Jahi', 'type' => 'residential'],
                    ['name' => 'Apo', 'type' => 'mixed'],
                    ['name' => 'Lugbe', 'type' => 'residential'],
                ],
                'agency' => [
                    'name' => 'Capital Crest Realty',
                    'email_domain' => 'capitalcrest.ng',
                    'phone_prefix' => '+234807',
                ],
                'agent_names' => [
                    'Chinedu Okeke', 'Blessing Yusuf', 'Temitope Adekunle', 'Abdulrahman Musa',
                    'Ifunanya Eze', 'Segun Adebayo', 'Maryam Sule', 'Paulina Obi',
                ],
                'independent_agent_names' => [
                    'Moses Dikko', 'Hauwa Abubakar', 'Ruth Daniel', 'Victor Okoro',
                ],
                'pricing' => [
                    'sale_min' => 30000000,
                    'sale_max' => 300000000,
                    'rent_min' => 500000,
                    'rent_max' => 8000000,
                    'shortlet_min' => 50000,
                    'shortlet_max' => 180000,
                ],
            ],
            'kano' => [
                'label' => 'Kano, Kano State',
                'state_code' => 'KN',
                'state_name' => 'Kano',
                'city_candidates' => ['Kano', 'Kano Municipal'],
                'areas' => [
                    ['name' => 'Nassarawa GRA', 'type' => 'residential'],
                    ['name' => 'Tarauni', 'type' => 'residential'],
                    ['name' => 'Fagge', 'type' => 'mixed'],
                    ['name' => 'Gwale', 'type' => 'mixed'],
                    ['name' => 'Dala', 'type' => 'mixed'],
                    ['name' => 'Bompai', 'type' => 'commercial'],
                    ['name' => 'Sharada', 'type' => 'industrial'],
                    ['name' => 'Kurna', 'type' => 'residential'],
                    ['name' => 'Hotoro', 'type' => 'residential'],
                    ['name' => 'Sabon Gari', 'type' => 'commercial'],
                ],
                'agency' => [
                    'name' => 'Arewa Heritage Properties',
                    'email_domain' => 'arewaheritage.ng',
                    'phone_prefix' => '+234808',
                ],
                'agent_names' => [
                    'Sadiq Abdullahi', 'Nafisa Umar', 'Aisha Bello', 'Garba Idris',
                    'Nura Sani', 'Zainab Kabiru', 'Ismaila Bala', 'Maryam Danjuma',
                ],
                'independent_agent_names' => [
                    'Abba Nasir', 'Rahma Suleiman', 'Bashir Usman', 'Hajara Idris',
                ],
                'pricing' => [
                    'sale_min' => 5000000,
                    'sale_max' => 90000000,
                    'rent_min' => 250000,
                    'rent_max' => 1500000,
                    'shortlet_min' => 25000,
                    'shortlet_max' => 80000,
                ],
            ],
            'kaduna' => [
                'label' => 'Kaduna, Kaduna State',
                'state_code' => 'KD',
                'state_name' => 'Kaduna',
                'city_candidates' => ['Kaduna', 'Kaduna North', 'Kaduna South'],
                'areas' => [
                    ['name' => 'Barnawa', 'type' => 'residential'],
                    ['name' => 'Kakuri', 'type' => 'mixed'],
                    ['name' => 'Malali', 'type' => 'residential'],
                    ['name' => 'Kawo', 'type' => 'mixed'],
                    ['name' => 'Ungwan Rimi', 'type' => 'residential'],
                    ['name' => 'Sabon Tasha', 'type' => 'residential'],
                    ['name' => 'Tudun Wada', 'type' => 'mixed'],
                    ['name' => 'GRA', 'type' => 'residential'],
                    ['name' => 'Rigasa', 'type' => 'mixed'],
                    ['name' => 'Narayi', 'type' => 'residential'],
                ],
                'agency' => [
                    'name' => 'Northern Crest Estates',
                    'email_domain' => 'northerncrest.ng',
                    'phone_prefix' => '+234809',
                ],
                'agent_names' => [
                    'Fatima Yakubu', 'Ibrahim Hassan', 'Maryam Sani', 'Suleiman Garba',
                    'Hannah Luka', 'Efe Bako', 'Abdulraman Saleh', 'Hajara Musa',
                ],
                'independent_agent_names' => [
                    'Ishaq Ladan', 'Joy Udo', 'Usman Yahaya', 'Grace Umar',
                ],
                'pricing' => [
                    'sale_min' => 5000000,
                    'sale_max' => 90000000,
                    'rent_min' => 250000,
                    'rent_max' => 1500000,
                    'shortlet_min' => 25000,
                    'shortlet_max' => 80000,
                ],
            ],
        ];
    }

    private function findState(string $code, string $fallbackName): ?State
    {
        return State::where('code', $code)->first()
            ?? State::where('name', 'like', '%' . $fallbackName . '%')->first();
    }

    private function findOrCreateCity(State $state, array $candidates): ?City
    {
        foreach ($candidates as $candidate) {
            $city = City::where('state_id', $state->id)
                ->where('name', $candidate)
                ->first();
            if ($city) {
                return $city;
            }
        }

        $likeCity = City::where('state_id', $state->id)
            ->where(function ($query) use ($candidates) {
                foreach ($candidates as $candidate) {
                    $query->orWhere('name', 'like', '%' . $candidate . '%');
                }
            })
            ->first();

        if ($likeCity) {
            return $likeCity;
        }

        $name = $candidates[0] ?? null;
        if (!$name) {
            return null;
        }

        return City::create([
            'name' => $name,
            'state_id' => $state->id,
            'type' => 'city',
            'is_active' => true,
        ]);
    }

    private function ensureAreas(City $city, array $areas): Collection
    {
        $areaModels = collect();

        foreach ($areas as $area) {
            $areaModels->push(
                Area::firstOrCreate(
                    ['name' => $area['name'], 'city_id' => $city->id],
                    [
                        'type' => $area['type'] ?? 'residential',
                        'description' => "Popular area in {$city->name}.",
                        'is_active' => true,
                    ]
                )
            );
        }

        return $areaModels;
    }

    private function createAgency(array $location, State $state, City $city, Collection $areas): Agency
    {
        $ownerUser = $this->createUser(
            $this->randomName($location['agent_names']),
            $location['agency']['email_domain'],
            $location['agency']['phone_prefix'],
            'agency_owner'
        );

        $agency = Agency::create([
            'name' => $location['agency']['name'],
            'slug' => Str::slug($location['agency']['name']),
            'description' => "Trusted real estate agency serving {$city->name} and surrounding communities.",
            'license_number' => strtoupper(Str::random(3)) . '-' . rand(1000, 9999),
            'license_expiry_date' => now()->addYears(3),
            'email' => 'info@' . $location['agency']['email_domain'],
            'phone' => $location['agency']['phone_prefix'] . rand(1000000, 9999999),
            'website' => 'https://' . $location['agency']['email_domain'],
            'address' => [
                'street' => 'Plot ' . rand(10, 250) . ', ' . $areas->first()->name,
                'city' => $city->name,
                'state' => $state->name,
                'country' => 'Nigeria',
            ],
            'specializations' => 'Residential,Commercial,Land',
            'years_in_business' => rand(4, 15),
            'rating' => rand(38, 50) / 10,
            'total_reviews' => rand(20, 120),
            'is_verified' => true,
            'is_featured' => true,
            'is_active' => true,
            'verified_at' => now(),
            'owner_id' => $ownerUser->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'area_id' => $areas->first()->id,
        ]);

        $agency->users()->attach($ownerUser->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return $agency;
    }

    private function createAgencyAgents(array $location, Agency $agency): Collection
    {
        $agents = collect();
        $agentCount = 3;

        for ($i = 0; $i < $agentCount; $i++) {
            $user = $this->createUser(
                $this->randomName($location['agent_names']),
                $location['agency']['email_domain'],
                $location['agency']['phone_prefix'],
                'agent'
            );

            $agent = Agent::create([
                'user_id' => $user->id,
                'agency_id' => $agency->id,
                'license_number' => 'AGT-' . $agency->id . '-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'specializations' => 'Residential,Land,Shortlet',
                'years_experience' => rand(2, 12),
                'commission_rate' => rand(3, 8),
                'languages' => ['English', 'Hausa'],
                'service_areas' => [],
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(5, 80),
                'total_properties' => rand(10, 60),
                'active_listings' => rand(2, 15),
                'properties_sold' => rand(2, 20),
                'properties_rented' => rand(2, 20),
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => rand(0, 1) === 1,
                'accepts_new_clients' => true,
                'verified_at' => now(),
                'last_active_at' => now(),
            ]);

            $agency->users()->attach($user->id, [
                'role' => 'agent',
                'is_active' => true,
                'joined_at' => now(),
            ]);

            $agents->push($agent);
        }

        return $agents;
    }

    private function createIndependentAgents(array $location): Collection
    {
        $agents = collect();
        $agentCount = 2;

        for ($i = 0; $i < $agentCount; $i++) {
            $user = $this->createUser(
                $this->randomName($location['independent_agent_names']),
                $location['agency']['email_domain'],
                $location['agency']['phone_prefix'],
                'agent'
            );

            $agent = Agent::create([
                'user_id' => $user->id,
                'agency_id' => null,
                'license_number' => 'IND-' . strtoupper(Str::random(3)) . '-' . date('Y'),
                'specializations' => 'Residential,Land,Commercial',
                'years_experience' => rand(3, 14),
                'commission_rate' => rand(3, 10),
                'languages' => ['English', 'Hausa'],
                'service_areas' => [],
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(5, 70),
                'total_properties' => rand(8, 40),
                'active_listings' => rand(2, 12),
                'properties_sold' => rand(2, 18),
                'properties_rented' => rand(2, 18),
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => rand(0, 1) === 1,
                'accepts_new_clients' => true,
                'verified_at' => now(),
                'last_active_at' => now(),
            ]);

            $agents->push($agent);
        }

        return $agents;
    }

    private function createOwnerPool(
        int $count,
        array $location,
        State $state,
        City $city,
        Collection $areas,
        ?Agency $agency,
        ?Collection $independentAgents
    ): Collection {
        $owners = collect();

        for ($i = 0; $i < $count; $i++) {
            $name = $this->randomName(array_merge($location['agent_names'], $location['independent_agent_names']));
            $user = $this->createUser(
                $name,
                $location['agency']['email_domain'],
                $location['agency']['phone_prefix'],
                'property_owner'
            );

            $area = $areas[$i % $areas->count()];
            $agent = $independentAgents ? $independentAgents->random() : null;

            $owners->push(PropertyOwner::create([
                'type' => 'individual',
                'first_name' => Str::of($name)->before(' ')->toString(),
                'last_name' => Str::of($name)->after(' ')->toString(),
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => 'Plot ' . rand(1, 200) . ', ' . $area->name . ', ' . $city->name,
                'city' => $city->name,
                'state' => $state->name,
                'country' => 'Nigeria',
                'user_id' => $user->id,
                'agency_id' => $agency?->id,
                'agent_id' => $agent?->id,
                'notes' => "Owner based in {$area->name}, {$city->name}.",
                'is_active' => true,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'area_id' => $area->id,
                'is_verified' => true,
                'verified_at' => now(),
            ]));
        }

        return $owners;
    }

    private function createProperty(
        array $location,
        State $state,
        City $city,
        Area $area,
        Collection $propertyTypes,
        PropertyOwner $owner,
        Agent $agent,
        ?Agency $agency
    ): Property {
        $listingType = $this->weightedChoice([
            'sale' => 45,
            'rent' => 35,
            'lease' => 15,
            'shortlet' => 5,
        ]);

        $status = $this->weightedChoice([
            'available' => 80,
            'rented' => 8,
            'sold' => 6,
            'off_market' => 4,
            'under_review' => 2,
        ]);

        $propertyType = $this->pickPropertyType($propertyTypes);
        $propertySubtype = $propertyType->propertySubtypes->isNotEmpty()
            ? $propertyType->propertySubtypes->random()
            : null;
        $propertyLabel = $propertySubtype?->name ?? $propertyType->name;

        $bedrooms = $this->bedroomCount($propertyType->name);
        $bathrooms = max(1, min(5, $bedrooms === 0 ? 1 : rand(1, $bedrooms)));

        $price = $this->generatePrice($location, $listingType);
        $pricePeriod = $this->pricePeriodForListing($listingType);

        $title = $this->buildTitle($propertyLabel, $area->name, $city->name);

        return Property::create([
            'title' => $title,
            'description' => $this->buildDescription($propertyLabel, $area->name, $city->name, $state->name),
            'listing_type' => $listingType,
            'status' => $status,
            'price' => $price,
            'price_period' => $pricePeriod,
            'service_charge' => rand(0, 1) ? rand(150000, 500000) : null,
            'legal_fee' => rand(0, 1) ? rand(120000, 350000) : null,
            'agency_fee' => rand(0, 1) ? rand(120000, 450000) : null,
            'caution_deposit' => $listingType === 'rent' ? rand(200000, 800000) : null,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'toilets' => $bedrooms > 0 ? $bathrooms + rand(0, 2) : 1,
            'size_sqm' => rand(120, 850),
            'parking_spaces' => rand(0, 4),
            'year_built' => rand(2000, 2024),
            'furnishing_status' => $this->weightedChoice([
                'furnished' => 35,
                'semi_furnished' => 40,
                'unfurnished' => 25,
            ]),
            'address' => 'Plot ' . rand(10, 350) . ', ' . $area->name . ', ' . $city->name,
            'landmark' => $this->randomLandmark($city->name),
            'latitude' => fake()->latitude(4.5, 13.5),
            'longitude' => fake()->longitude(3.0, 13.5),
            'property_type_id' => $propertyType->id,
            'property_subtype_id' => $propertySubtype?->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
            'owner_id' => $owner->id,
            'agent_id' => $agent->id,
            'agency_id' => $agency?->id,
            'meta_title' => $title,
            'meta_description' => "Modern {$propertyLabel} in {$area->name}, {$city->name}.",
            'view_count' => rand(0, 120),
            'inquiry_count' => rand(0, 15),
            'favorite_count' => rand(0, 20),
            'last_viewed_at' => now()->subDays(rand(1, 30)),
            'is_featured' => rand(0, 1) === 1,
            'is_verified' => rand(0, 1) === 1,
            'is_published' => true,
            'published_at' => now()->subDays(rand(1, 10)),
            'price_negotiable' => rand(0, 1) === 1,
            'contact_phone' => $agent->user?->phone,
            'contact_email' => $agent->user?->email,
            'viewing_instructions' => 'Contact agent at least 24 hours ahead to schedule viewing.',
            'is_active' => true,
        ]);
    }

    private function attachImages(Property $property, bool $withImages): void
    {
        if (!$withImages) {
            return;
        }

        if ($property->getMedia('featured')->count() > 0) {
            return;
        }

        $imageUrls = [
            'https://images.unsplash.com/photo-1505843513577-22bb7d21e455?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1522050212171-61b01dd24579?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1489171078254-c3365d6e359f?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1502005229762-cf1b2da7c5d6?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1556909114-f7c31d2b281b?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1604709177225-055f99402ea3?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1571055107559-3e67626fa8be?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1628744876497-eb30460be9f6?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1497366216548-37526070297c?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1501045661006-fcebe0257c3f?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1501183638710-841dd1904471?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1448630360428-65456885c650?w=900&h=650&q=80',
            'https://images.unsplash.com/photo-1479839672679-a46483c0e7c8?w=900&h=650&q=80',
        ];

        $startIndex = $property->id % count($imageUrls);
        $rotated = array_merge(
            array_slice($imageUrls, $startIndex),
            array_slice($imageUrls, 0, $startIndex)
        );
        $selected = array_slice($rotated, 0, rand(3, 5));

        try {
            $property
                ->addMediaFromUrl($selected[0])
                ->usingName("Featured image for {$property->title}")
                ->usingFileName('featured_' . $property->id . '_' . time() . '.jpg')
                ->toMediaCollection('featured');

            for ($i = 1; $i < count($selected); $i++) {
                $property
                    ->addMediaFromUrl($selected[$i])
                    ->usingName("Gallery image {$i} for {$property->title}")
                    ->usingFileName('gallery_' . $property->id . '_' . $i . '_' . time() . '.jpg')
                    ->toMediaCollection('gallery');
            }
        } catch (\Exception $e) {
            $this->warn("Image attach failed for {$property->id}: {$e->getMessage()}");
        }
    }

    private function createUser(string $name, string $domain, string $phonePrefix, string $userType): User
    {
        $slug = Str::slug($name);
        $email = $slug . '.' . Str::lower(Str::random(5)) . '@' . $domain;

        return User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phonePrefix . rand(1000000, 9999999),
            'password' => Hash::make('password123'),
            'user_type' => $userType,
            'is_verified' => true,
            'is_active' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);
    }

    private function randomName(array $names): string
    {
        return $names[array_rand($names)];
    }

    private function pickPropertyType(Collection $propertyTypes): PropertyType
    {
        $preferred = $propertyTypes->filter(function ($type) {
            return in_array(strtolower($type->name), ['apartment', 'house', 'land', 'commercial', 'office space', 'warehouse']);
        });

        return ($preferred->isNotEmpty() ? $preferred : $propertyTypes)->random();
    }

    private function bedroomCount(string $propertyTypeName): int
    {
        $lower = strtolower($propertyTypeName);
        if (str_contains($lower, 'land')) {
            return 0;
        }
        if (str_contains($lower, 'commercial') || str_contains($lower, 'warehouse')) {
            return rand(0, 2);
        }
        return rand(1, 6);
    }

    private function generatePrice(array $location, string $listingType): int
    {
        $pricing = $location['pricing'];

        $range = match ($listingType) {
            'sale' => [$pricing['sale_min'], $pricing['sale_max']],
            'shortlet' => [$pricing['shortlet_min'], $pricing['shortlet_max']],
            default => [$pricing['rent_min'], $pricing['rent_max']],
        };

        return rand($range[0], $range[1]);
    }

    private function pricePeriodForListing(string $listingType): ?string
    {
        return match ($listingType) {
            'sale' => 'total',
            'lease' => 'per_year',
            'rent' => rand(0, 1) ? 'per_year' : 'per_month',
            'shortlet' => 'per_night',
            default => null,
        };
    }

    private function buildTitle(string $propertyLabel, string $area, string $city): string
    {
        $templates = [
            "Modern {$propertyLabel} in {$area}, {$city}",
            "{$propertyLabel} with Spacious Compound - {$area}",
            "Well-Located {$propertyLabel} at {$area}, {$city}",
            "Newly Built {$propertyLabel} in {$area}",
        ];

        return $templates[array_rand($templates)];
    }

    private function buildDescription(string $propertyLabel, string $area, string $city, string $state): string
    {
        return "A well-finished {$propertyLabel} situated in {$area}, {$city}, {$state}. "
            . "This listing offers modern finishes, reliable access roads, and proximity to key amenities. "
            . "Ideal for families and professionals seeking convenience in {$city}.";
    }

    private function randomLandmark(string $city): string
    {
        $landmarks = [
            "near {$city} Central Market",
            "close to the main express road",
            "opposite a neighborhood park",
            "close to a leading secondary school",
            "minutes from the city ring road",
        ];

        return $landmarks[array_rand($landmarks)];
    }

    private function weightedChoice(array $weights): string
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);

        foreach ($weights as $item => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $item;
            }
        }

        return array_key_first($weights);
    }
}
