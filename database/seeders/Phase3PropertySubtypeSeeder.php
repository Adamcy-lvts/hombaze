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

        // Delegate actual subtype creation to the extracted seeder
        $this->call(\Database\Seeders\PropertySubTypeSeeder::class);

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
}
