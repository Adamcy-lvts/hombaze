<?php

namespace App\Console\Commands;

use Database\Seeders\StateSeeder;
use Database\Seeders\PropertyTypeSeeder;
use Database\Seeders\PropertyFeatureSeeder;
use Database\Seeders\ShieldSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\AreaSeeder;
use Database\Seeders\PropertySubTypeSeeder;
use Database\Seeders\PlotSizeSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedCoreData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Runs seeders necessary for core location and property type data.
     */
    protected $signature = 'seed:core
                            {--fresh : Clear existing data before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed core data (states, cities, areas, property types, features, subtypes, plot sizes, and permissions)';

    public function handle(): int
    {
        $this->info('Seeding core data in correct order...');

        // Clear existing data if --fresh option is used
        if ($this->option('fresh')) {
            $this->clearExistingData();
        }

        $coreDataSeeders = [
            // Foundation data
            StateSeeder::class,
            PropertyTypeSeeder::class,
            PropertyFeatureSeeder::class,

            // Roles and permissions (required for multi-tenant setup)
            ShieldSeeder::class,

            // Geographic data (depends on states)
            CitySeeder::class,
            AreaSeeder::class,

            // Property-specific data
            PropertySubTypeSeeder::class,
            PlotSizeSeeder::class,
        ];

        // Run all core data seeders
        foreach ($coreDataSeeders as $seederClass) {
            $this->line("-> Running: {$seederClass}");
            $exit = $this->call('db:seed', ['--class' => $seederClass]);
            if ($exit !== 0) {
                $this->error("Seeder failed: {$seederClass}");
                return self::FAILURE;
            }
            $this->info("Completed: {$seederClass}");
        }

        $this->info('Core data seeding finished successfully');
        return self::SUCCESS;
    }

    /**
     * Clear existing core data tables to prevent duplicates
     */
    private function clearExistingData(): void
    {
        $this->info('🗑️  Clearing existing core data...');

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear tables in reverse dependency order
        $tablesToClear = [
            'property_subtypes',
            'plot_sizes',
            'areas',
            'cities',
            'property_features',
            'property_types',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'permissions',
            'roles',
            'states',
        ];

        foreach ($tablesToClear as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("   ✅ Cleared: {$table}");
            }
        }

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('✨ Core data tables cleared successfully');
    }

}
