<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\State;
use App\Models\PropertyType;
use App\Models\PropertyFeature;

class Phase1FoundationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Phase 1: Foundation Data...');
        
        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing foundation data
        $this->command->info('Clearing existing Phase 1 records...');
        PropertyFeature::truncate();
        PropertyType::truncate();
        State::truncate();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
    $this->seedStates();
    // Extracted property type seeding to dedicated seeder
    $this->call(\Database\Seeders\PropertyTypeSeeder::class);
    $this->seedPropertyFeatures();
        
        $this->command->info('Phase 1 Foundation Data seeded successfully!');
    }

    /**
     * Seed Nigerian states
     */
    private function seedStates(): void
    {
        $this->command->info('Seeding States...');
        
        $states = [
            // North Central
            ['name' => 'Abuja Federal Capital Territory', 'code' => 'FCT', 'region' => 'north_central'],
            ['name' => 'Benue', 'code' => 'BE', 'region' => 'north_central'],
            ['name' => 'Kogi', 'code' => 'KG', 'region' => 'north_central'],
            ['name' => 'Kwara', 'code' => 'KW', 'region' => 'north_central'],
            ['name' => 'Nasarawa', 'code' => 'NA', 'region' => 'north_central'],
            ['name' => 'Niger', 'code' => 'NI', 'region' => 'north_central'],
            ['name' => 'Plateau', 'code' => 'PL', 'region' => 'north_central'],

            // North East
            ['name' => 'Adamawa', 'code' => 'AD', 'region' => 'north_east'],
            ['name' => 'Bauchi', 'code' => 'BA', 'region' => 'north_east'],
            ['name' => 'Borno', 'code' => 'BO', 'region' => 'north_east'],
            ['name' => 'Gombe', 'code' => 'GO', 'region' => 'north_east'],
            ['name' => 'Taraba', 'code' => 'TA', 'region' => 'north_east'],
            ['name' => 'Yobe', 'code' => 'YO', 'region' => 'north_east'],

            // North West
            ['name' => 'Jigawa', 'code' => 'JI', 'region' => 'north_west'],
            ['name' => 'Kaduna', 'code' => 'KD', 'region' => 'north_west'],
            ['name' => 'Kano', 'code' => 'KN', 'region' => 'north_west'],
            ['name' => 'Katsina', 'code' => 'KT', 'region' => 'north_west'],
            ['name' => 'Kebbi', 'code' => 'KB', 'region' => 'north_west'],
            ['name' => 'Sokoto', 'code' => 'SO', 'region' => 'north_west'],
            ['name' => 'Zamfara', 'code' => 'ZA', 'region' => 'north_west'],

            // South East
            ['name' => 'Abia', 'code' => 'AB', 'region' => 'south_east'],
            ['name' => 'Anambra', 'code' => 'AN', 'region' => 'south_east'],
            ['name' => 'Ebonyi', 'code' => 'EB', 'region' => 'south_east'],
            ['name' => 'Enugu', 'code' => 'EN', 'region' => 'south_east'],
            ['name' => 'Imo', 'code' => 'IM', 'region' => 'south_east'],

            // South South
            ['name' => 'Akwa Ibom', 'code' => 'AK', 'region' => 'south_south'],
            ['name' => 'Bayelsa', 'code' => 'BY', 'region' => 'south_south'],
            ['name' => 'Cross River', 'code' => 'CR', 'region' => 'south_south'],
            ['name' => 'Delta', 'code' => 'DE', 'region' => 'south_south'],
            ['name' => 'Edo', 'code' => 'ED', 'region' => 'south_south'],
            ['name' => 'Rivers', 'code' => 'RI', 'region' => 'south_south'],

            // South West
            ['name' => 'Ekiti', 'code' => 'EK', 'region' => 'south_west'],
            ['name' => 'Lagos', 'code' => 'LA', 'region' => 'south_west'],
            ['name' => 'Ogun', 'code' => 'OG', 'region' => 'south_west'],
            ['name' => 'Ondo', 'code' => 'ON', 'region' => 'south_west'],
            ['name' => 'Osun', 'code' => 'OS', 'region' => 'south_west'],
            ['name' => 'Oyo', 'code' => 'OY', 'region' => 'south_west'],
        ];

        foreach ($states as $state) {
            State::firstOrCreate(
                ['code' => $state['code']],
                array_merge($state, ['status' => 'active'])
            );
        }

        $this->command->info('States seeded: ' . count($states));
    }

    // Property type seeding has been extracted to PropertyTypeSeeder

    /**
     * Seed property features
     */
    private function seedPropertyFeatures(): void
    {
        $this->command->info('Seeding Property Features...');
        
        $propertyFeatures = PropertyFeature::getDefaultFeatures();
        
        foreach ($propertyFeatures as $index => $feature) {
            PropertyFeature::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($feature['name'])],
                array_merge($feature, [
                    'is_active' => true,
                    'sort_order' => ($index + 1) * 10
                ])
            );
        }

        $this->command->info('Property Features seeded: ' . count($propertyFeatures));
    }
}
