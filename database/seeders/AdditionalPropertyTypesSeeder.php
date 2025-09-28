<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyType;

class AdditionalPropertyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $additionalPropertyTypes = [
            [
                'name' => 'Shop',
                'description' => 'Retail shops and stores for commercial business',
                'icon' => 'heroicon-o-building-storefront',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Event Center',
                'description' => 'Event venues, halls, and conference centers',
                'icon' => 'heroicon-o-calendar-days',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Restaurant',
                'description' => 'Restaurant spaces and food service establishments',
                'icon' => 'heroicon-o-building-storefront',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Hotel',
                'description' => 'Hotels, lodges, and hospitality properties',
                'icon' => 'heroicon-o-building-office',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'School',
                'description' => 'Educational facilities and institutions',
                'icon' => 'heroicon-o-academic-cap',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Hospital/Clinic',
                'description' => 'Medical facilities and healthcare centers',
                'icon' => 'heroicon-o-heart',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Gas Station',
                'description' => 'Fuel stations and automotive service centers',
                'icon' => 'heroicon-o-truck',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Shopping Mall',
                'description' => 'Large retail complexes and shopping centers',
                'icon' => 'heroicon-o-building-office-2',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'Church/Mosque',
                'description' => 'Religious buildings and worship centers',
                'icon' => 'heroicon-o-home-modern',
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'name' => 'Factory',
                'description' => 'Manufacturing facilities and industrial plants',
                'icon' => 'heroicon-o-cog-8-tooth',
                'is_active' => true,
                'sort_order' => 16,
            ],
            [
                'name' => 'Market Stall',
                'description' => 'Individual stalls in markets and bazaars',
                'icon' => 'heroicon-o-shopping-bag',
                'is_active' => true,
                'sort_order' => 17,
            ],
            [
                'name' => 'Recreational Center',
                'description' => 'Sports centers, gyms, and recreational facilities',
                'icon' => 'heroicon-o-trophy',
                'is_active' => true,
                'sort_order' => 18,
            ],
            [
                'name' => 'Car Wash',
                'description' => 'Vehicle cleaning and maintenance facilities',
                'icon' => 'heroicon-o-truck',
                'is_active' => true,
                'sort_order' => 19,
            ],
            [
                'name' => 'Parking Space',
                'description' => 'Dedicated parking lots and spaces',
                'icon' => 'heroicon-o-square-3-stack-3d',
                'is_active' => true,
                'sort_order' => 20,
            ],
        ];

        foreach ($additionalPropertyTypes as $typeData) {
            PropertyType::firstOrCreate(
                ['name' => $typeData['name']],
                $typeData
            );
        }

        $this->command->info('Additional property types created successfully!');
    }
}