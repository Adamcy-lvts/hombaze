<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;
use Illuminate\Support\Str;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Seed the application's property types.
     */
    public function run(): void
    {
        $this->command->info('Seeding Property Types...');

        // Get basic property types from model
        $basicPropertyTypes = PropertyType::getDefaultTypes();

        // Additional property types for comprehensive coverage
        $additionalPropertyTypes = [
            ['name' => 'Shop', 'description' => 'Retail shops and stores for commercial business', 'icon' => 'heroicon-o-building-storefront'],
            ['name' => 'Event Center', 'description' => 'Event venues, halls, and conference centers', 'icon' => 'heroicon-o-calendar-days'],
            ['name' => 'Restaurant', 'description' => 'Restaurant spaces and food service establishments', 'icon' => 'heroicon-o-building-storefront'],
            ['name' => 'Hotel', 'description' => 'Hotels, lodges, and hospitality properties', 'icon' => 'heroicon-o-building-office'],
            ['name' => 'School', 'description' => 'Educational facilities and institutions', 'icon' => 'heroicon-o-academic-cap'],
            ['name' => 'Hospital/Clinic', 'description' => 'Medical facilities and healthcare centers', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Gas Station', 'description' => 'Fuel stations and automotive service centers', 'icon' => 'heroicon-o-truck'],
            ['name' => 'Shopping Mall', 'description' => 'Large retail complexes and shopping centers', 'icon' => 'heroicon-o-building-office-2'],
            ['name' => 'Church/Mosque', 'description' => 'Religious buildings and worship centers', 'icon' => 'heroicon-o-home-modern'],
            ['name' => 'Factory', 'description' => 'Manufacturing facilities and industrial plants', 'icon' => 'heroicon-o-cog-8-tooth'],
            ['name' => 'Market Stall', 'description' => 'Individual stalls in markets and bazaars', 'icon' => 'heroicon-o-shopping-bag'],
            ['name' => 'Recreational Center', 'description' => 'Sports centers, gyms, and recreational facilities', 'icon' => 'heroicon-o-trophy'],
            ['name' => 'Car Wash', 'description' => 'Vehicle cleaning and maintenance facilities', 'icon' => 'heroicon-o-truck'],
            ['name' => 'Parking Space', 'description' => 'Dedicated parking lots and spaces', 'icon' => 'heroicon-o-square-3-stack-3d'],
        ];

        // Combine all property types
        $allPropertyTypes = array_merge($basicPropertyTypes, $additionalPropertyTypes);

        foreach ($allPropertyTypes as $index => $type) {
            PropertyType::firstOrCreate(
                ['slug' => Str::slug($type['name'])],
                array_merge($type, [
                    'is_active' => true,
                    'sort_order' => ($index + 1) * 10,
                ])
            );
        }

        $this->command->info('Property Types seeded: ' . count($allPropertyTypes));
    }
}
