<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MissingAreasSeeder extends Seeder
{
    /**
     * Create basic areas for cities that don't have any areas
     */
    public function run(): void
    {
        $this->command->info('🏘️ Creating areas for cities without areas...');

        // Get all cities that don't have any areas
        $citiesWithoutAreas = City::doesntHave('areas')->get();

        $this->command->info("Found {$citiesWithoutAreas->count()} cities without areas");

        foreach ($citiesWithoutAreas as $city) {
            $this->createBasicAreasForCity($city);
        }

        $this->command->info('✅ Missing areas created successfully!');
        $this->command->info('📊 Total areas now: ' . Area::count());
    }

    private function createBasicAreasForCity(City $city): void
    {
        // Create 3-8 basic areas for each city
        $areaCount = rand(3, 8);
        $areaTypes = [
            'Central', 'North', 'South', 'East', 'West',
            'Downtown', 'Uptown', 'New Town', 'Old Town',
            'Industrial', 'Residential', 'Commercial',
            'GRA', 'Layout', 'Estate', 'Phase 1', 'Phase 2'
        ];

        $createdAreas = [];
        
        for ($i = 0; $i < $areaCount; $i++) {
            $areaType = fake()->randomElement($areaTypes);
            $areaName = $areaType . ' ' . $city->name;
            
            // Avoid duplicates
            if (in_array($areaName, $createdAreas)) {
                $areaName = $areaType . ' Area ' . ($i + 1);
            }

            Area::create([
                'name' => $areaName,
                'slug' => Str::slug($areaName),
                'description' => "A {$areaType} area in {$city->name}, {$city->state->name}",
                'postal_code' => $this->generatePostalCode($city->state->name),
                'latitude' => $this->generateLatitude($city->state->name),
                'longitude' => $this->generateLongitude($city->state->name),
                'is_active' => true,
                'city_id' => $city->id,
                'state_id' => $city->state_id,
            ]);

            $createdAreas[] = $areaName;
        }

        $this->command->line("✓ Created {$areaCount} areas for {$city->name}");
    }

    private function generatePostalCode(string $stateName): string
    {
        // Generate realistic postal codes based on state
        $stateCode = match($stateName) {
            'Lagos' => '10',
            'Abuja', 'Federal Capital Territory' => '90',
            'Rivers' => '50',
            'Kano' => '70',
            'Oyo' => '20',
            'Delta' => '33',
            default => str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT)
        };

        return $stateCode . rand(1000, 9999);
    }

    private function generateLatitude(string $stateName): float
    {
        // Generate realistic coordinates for Nigerian states
        return match($stateName) {
            'Lagos' => fake()->latitude(6.4, 6.7),
            'Federal Capital Territory', 'Abuja' => fake()->latitude(8.8, 9.2),
            'Rivers' => fake()->latitude(4.7, 5.2),
            'Kano' => fake()->latitude(11.9, 12.1),
            'Oyo' => fake()->latitude(7.2, 8.2),
            'Delta' => fake()->latitude(5.0, 6.5),
            default => fake()->latitude(4.0, 14.0) // General Nigeria range
        };
    }

    private function generateLongitude(string $stateName): float
    {
        // Generate realistic coordinates for Nigerian states
        return match($stateName) {
            'Lagos' => fake()->longitude(3.1, 3.6),
            'Federal Capital Territory', 'Abuja' => fake()->longitude(7.3, 7.6),
            'Rivers' => fake()->longitude(6.7, 7.2),
            'Kano' => fake()->longitude(8.4, 8.6),
            'Oyo' => fake()->longitude(3.5, 4.5),
            'Delta' => fake()->longitude(5.5, 6.8),
            default => fake()->longitude(2.5, 14.5) // General Nigeria range
        };
    }
}
