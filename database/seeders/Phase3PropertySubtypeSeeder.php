<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PropertyType;
use App\Models\PropertySubtype;

class Phase3PropertySubtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Phase 3: Property Subtypes...');

        // Validate prerequisites
        $this->validatePrerequisites();

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing property subtypes
        $this->command->info('Clearing existing Phase 3 records...');
        PropertySubtype::truncate();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedPropertySubtypes();

        $this->command->info('Phase 3 Property Subtypes seeded successfully!');
    }

    /**
     * Validate that prerequisite data exists before seeding
     */
    private function validatePrerequisites(): void
    {
        $this->command->info('Validating prerequisites...');

        // Check if property types exist
        $propertyTypeCount = PropertyType::count();
        if ($propertyTypeCount === 0) {
            throw new \Exception('No property types found. Please run Phase1FoundationSeeder first.');
        }

        $this->command->info("Prerequisites validated: {$propertyTypeCount} property types found");
    }

    private function seedPropertySubtypes(): void
    {
        $subtypes = [
            // Residential subtypes
            'apartment' => [
                ['name' => 'Studio Apartment', 'description' => 'Single room with kitchenette and bathroom'],
                ['name' => 'One Bedroom Apartment', 'description' => 'Apartment with separate bedroom'],
                ['name' => 'Two Bedroom Apartment', 'description' => 'Apartment with two separate bedrooms'],
                ['name' => 'Three Bedroom Apartment', 'description' => 'Apartment with three bedrooms'],
                ['name' => 'Penthouse', 'description' => 'Luxury top floor apartment'],
                ['name' => 'Serviced Apartment', 'description' => 'Fully furnished apartment with services'],
                ['name' => 'Mini Flat', 'description' => 'Compact apartment with basic amenities'],
            ],
            'house' => [
                ['name' => 'Bungalow', 'description' => 'Single-story detached house'],
                ['name' => 'Duplex', 'description' => 'Two-story house'],
                ['name' => 'Terrace House', 'description' => 'Attached house in a row'],
                ['name' => 'Detached House', 'description' => 'Standalone house with yard'],
                ['name' => 'Semi-Detached House', 'description' => 'House attached on one side'],
                ['name' => 'Mansion', 'description' => 'Large luxury house'],
                ['name' => 'Villa', 'description' => 'Upscale house with amenities'],
                ['name' => 'Townhouse', 'description' => 'Multi-story house sharing walls with neighbors'],
                ['name' => 'Condominium', 'description' => 'Individually owned unit in a complex'],
            ],

            // Land subtypes
            'land' => [
                ['name' => 'Residential Plot', 'description' => 'Land designated for residential development'],
                ['name' => 'Commercial Plot', 'description' => 'Land designated for commercial development'],
                ['name' => 'Agricultural Land', 'description' => 'Land for farming and agricultural use'],
                ['name' => 'Industrial Plot', 'description' => 'Land designated for industrial development'],
                ['name' => 'Mixed-Use Plot', 'description' => 'Land for mixed residential/commercial development'],
                ['name' => 'Estate Development Land', 'description' => 'Large tract for estate development'],
                ['name' => 'Gated Community Plot', 'description' => 'Plot in secured residential community'],
                ['name' => 'Farmland', 'description' => 'Agricultural farming land'],
                ['name' => 'Plantation Land', 'description' => 'Large scale agricultural plantation'],
                ['name' => 'Poultry Farm Land', 'description' => 'Land designated for poultry farming'],
            ],

            // Commercial subtypes
            'commercial' => [
                ['name' => 'Shopping Mall Space', 'description' => 'Retail space in shopping mall'],
                ['name' => 'Street Front Shop', 'description' => 'Shop with direct street access'],
                ['name' => 'Market Stall', 'description' => 'Small retail space in market'],
                ['name' => 'Boutique Space', 'description' => 'Small upscale retail space'],
                ['name' => 'Supermarket', 'description' => 'Large retail food store'],
                ['name' => 'Restaurant Space', 'description' => 'Space for food service business'],
                ['name' => 'Fast Food Outlet', 'description' => 'Quick service restaurant space'],
                ['name' => 'Café Space', 'description' => 'Coffee shop or casual dining space'],
                ['name' => 'Bar/Lounge', 'description' => 'Bar or entertainment venue'],
                ['name' => 'Hotel/Lodge', 'description' => 'Hospitality business space'],
                ['name' => 'Event Center', 'description' => 'Space for events and celebrations'],
                ['name' => 'Pharmacy', 'description' => 'Medical/pharmaceutical retail space'],
            ],

            // Office Space subtypes  
            'office-space' => [
                ['name' => 'Executive Office', 'description' => 'Private office for executives'],
                ['name' => 'Open Plan Office', 'description' => 'Large open workspace'],
                ['name' => 'Shared Office Space', 'description' => 'Co-working office environment'],
                ['name' => 'Medical Office', 'description' => 'Office space for medical practice'],
                ['name' => 'Professional Office', 'description' => 'Office for professional services'],
                ['name' => 'Corporate Office', 'description' => 'Large corporate office space'],
                ['name' => 'Virtual Office', 'description' => 'Business address with minimal physical space'],
                ['name' => 'Serviced Office', 'description' => 'Fully equipped office with services'],
            ],

            // Warehouse subtypes
            'warehouse' => [
                ['name' => 'Storage Warehouse', 'description' => 'General storage facility'],
                ['name' => 'Distribution Center', 'description' => 'Logistics and distribution hub'],
                ['name' => 'Cold Storage', 'description' => 'Refrigerated storage facility'],
                ['name' => 'Manufacturing Warehouse', 'description' => 'Industrial production facility'],
                ['name' => 'Logistics Hub', 'description' => 'Central logistics and shipping facility'],
                ['name' => 'Freight Terminal', 'description' => 'Cargo handling and storage facility'],
                ['name' => 'Light Manufacturing', 'description' => 'Small scale manufacturing facility'],
                ['name' => 'Heavy Manufacturing', 'description' => 'Large scale industrial production'],
            ],
        ];

        foreach ($subtypes as $propertyTypeSlug => $subtypeList) {
            $propertyType = PropertyType::where('slug', $propertyTypeSlug)->first();
            
            if ($propertyType) {
                foreach ($subtypeList as $subtypeData) {
                    PropertySubtype::create([
                        'property_type_id' => $propertyType->id,
                        'name' => $subtypeData['name'],
                        'description' => $subtypeData['description'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Property subtypes seeded successfully!');
    }
}
