<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Runs all seeders in the correct dependency order.
     */
    public function run(): void
    {
        $this->command->info('Starting complete database seeding...');
        $this->command->info('===========================================');

        // Disable foreign key constraints for clean truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Run seeders in dependency order
        $seeders = [
            ShieldSeeder::class, // Permissions and roles
            Phase1FoundationSeeder::class,  // States, Property Types, etc.
            Phase2LocationSeeder::class,    // Cities and Areas (depends on States)
            Phase3PropertySubtypeSeeder::class, // Property Subtypes (depends on Property Types)
            Phase4BusinessEntitiesSeeder::class, // Agencies & Agents (depends on States, Cities, Areas)
            Phase5PropertySeeder::class,    // Properties (depends on all previous phases)
            Phase7EngagementSeeder::class,  // Property engagement features
            ComprehensiveTestingSeeder::class, // Comprehensive testing scenarios
        ];

        foreach ($seeders as $seederClass) {
            $this->command->info("Running: {$seederClass}");
            $this->call($seederClass);
            $this->command->info("Completed: {$seederClass}");
            $this->command->line('---');
        }

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('===========================================');
        $this->command->info('All seeders completed successfully!');
    }
}
