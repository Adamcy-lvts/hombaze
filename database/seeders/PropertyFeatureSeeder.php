<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyFeature;

class PropertyFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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