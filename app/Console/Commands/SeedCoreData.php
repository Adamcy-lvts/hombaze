<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedCoreData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Runs seeders necessary for core location and property type data.
     */
    protected $signature = 'seed:core';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed core data (states, cities, areas, property types, features, subtypes, plot sizes, and permissions)';

    public function handle(): int
    {
        $this->info('Seeding core data in correct order...');

        $coreDataSeeders = [
            // Foundation data
            \Database\Seeders\StateSeeder::class,
            \Database\Seeders\PropertyTypeSeeder::class,
            \Database\Seeders\PropertyFeatureSeeder::class,

            // Roles and permissions (required for multi-tenant setup)
            \Database\Seeders\ShieldSeeder::class,

            // Geographic data (depends on states)
            \Database\Seeders\CitySeeder::class,
            \Database\Seeders\AreaSeeder::class,

            // Property-specific data
            \Database\Seeders\PropertySubTypeSeeder::class,
            \Database\Seeders\PlotSizeSeeder::class,
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

}
