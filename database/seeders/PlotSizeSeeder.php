<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlotSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plotSizes = [
            [
                'name' => 'Quarter Plot',
                'description' => '15x30m - Common small residential plot',
                'size_value' => 450,
                'unit' => 'sqm',
                'display_text' => '450 sqm',
                'sort_order' => 10,
            ],
            [
                'name' => 'Half Plot',
                'description' => '30x30m - Standard residential plot',
                'size_value' => 900,
                'unit' => 'sqm',
                'display_text' => '900 sqm',
                'sort_order' => 20,
            ],
            [
                'name' => 'Full Plot',
                'description' => '60x30m - Standard Nigerian plot',
                'size_value' => 1,
                'unit' => 'plot',
                'display_text' => '1 Plot (1,800 sqm)',
                'sort_order' => 30,
            ],
            [
                'name' => 'Corner Piece',
                'description' => '50x50m - Prime corner location',
                'size_value' => 2500,
                'unit' => 'sqm',
                'display_text' => '2,500 sqm',
                'sort_order' => 40,
            ],
            [
                'name' => '2 Plots',
                'description' => 'Double standard plot',
                'size_value' => 2,
                'unit' => 'plot',
                'display_text' => '2 Plots (3,600 sqm)',
                'sort_order' => 50,
            ],
            [
                'name' => '3 Plots',
                'description' => 'Triple standard plot',
                'size_value' => 3,
                'unit' => 'plot',
                'display_text' => '3 Plots (5,400 sqm)',
                'sort_order' => 60,
            ],
            [
                'name' => '4 Plots',
                'description' => 'Quadruple standard plot',
                'size_value' => 4,
                'unit' => 'plot',
                'display_text' => '4 Plots (7,200 sqm)',
                'sort_order' => 70,
            ],
            [
                'name' => '5 Plots',
                'description' => 'Large residential development',
                'size_value' => 5,
                'unit' => 'plot',
                'display_text' => '5 Plots (9,000 sqm)',
                'sort_order' => 80,
            ],
            [
                'name' => '1 Acre',
                'description' => 'International standard acre',
                'size_value' => 1,
                'unit' => 'acre',
                'display_text' => '1 Acre (4,047 sqm)',
                'sort_order' => 90,
            ],
            [
                'name' => '2 Acres',
                'description' => 'Large commercial/residential development',
                'size_value' => 2,
                'unit' => 'acre',
                'display_text' => '2 Acres (8,094 sqm)',
                'sort_order' => 100,
            ],
            [
                'name' => '5 Acres',
                'description' => 'Estate development size',
                'size_value' => 5,
                'unit' => 'acre',
                'display_text' => '5 Acres (20,234 sqm)',
                'sort_order' => 110,
            ],
            [
                'name' => '1 Hectare',
                'description' => 'Metric system standard',
                'size_value' => 1,
                'unit' => 'hectare',
                'display_text' => '1 Hectare (10,000 sqm)',
                'sort_order' => 120,
            ],
            [
                'name' => '2 Hectares',
                'description' => 'Large development land',
                'size_value' => 2,
                'unit' => 'hectare',
                'display_text' => '2 Hectares (20,000 sqm)',
                'sort_order' => 130,
            ],
            [
                'name' => '5 Hectares',
                'description' => 'Very large development land',
                'size_value' => 5,
                'unit' => 'hectare',
                'display_text' => '5 Hectares (50,000 sqm)',
                'sort_order' => 140,
            ],
        ];

        foreach ($plotSizes as $plotSize) {
            \App\Models\PlotSize::create($plotSize);
        }
    }
}
