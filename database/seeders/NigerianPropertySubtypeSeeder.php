<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;
use App\Models\PropertySubtype;

class NigerianPropertySubtypeSeeder extends Seeder
{
    /**
     * Add Nigerian-specific property subtypes
     */
    public function run(): void
    {
        $this->command->info('Adding Nigerian-specific property subtypes...');

        // Add more Nigerian house types
        $houseType = PropertyType::where('slug', 'house')->first();

        if ($houseType) {
            $nigerianHouseTypes = [
                ['name' => '2 Bedroom Bungalow', 'description' => '2 bedroom single-story house popular in Nigeria'],
                ['name' => '3 Bedroom Bungalow', 'description' => '3 bedroom single-story house with sitting room'],
                ['name' => '4 Bedroom Bungalow', 'description' => '4 bedroom single-story family house'],
                ['name' => '2 Bedroom Duplex', 'description' => '2 bedroom two-story house'],
                ['name' => '3 Bedroom Duplex', 'description' => '3 bedroom two-story house with modern amenities'],
                ['name' => '4 Bedroom Duplex', 'description' => '4 bedroom two-story family duplex'],
                ['name' => '5 Bedroom Duplex', 'description' => '5 bedroom luxury two-story house'],
                ['name' => '2 Bedroom Terrace', 'description' => '2 bedroom attached house in estate'],
                ['name' => '3 Bedroom Terrace', 'description' => '3 bedroom attached house popular in Lagos/Abuja'],
                ['name' => '4 Bedroom Terrace', 'description' => '4 bedroom terrace house with BQ'],
                ['name' => '2 Bedroom Detached', 'description' => '2 bedroom standalone house with compound'],
                ['name' => '3 Bedroom Detached', 'description' => '3 bedroom standalone house with garden'],
                ['name' => '4 Bedroom Detached', 'description' => '4 bedroom detached house with Boys Quarter'],
                ['name' => '5+ Bedroom Mansion', 'description' => 'Large luxury house with 5 or more bedrooms'],
            ];

            foreach ($nigerianHouseTypes as $houseData) {
                // Check if it already exists
                $exists = PropertySubtype::where('property_type_id', $houseType->id)
                    ->where('name', $houseData['name'])
                    ->exists();

                if (!$exists) {
                    PropertySubtype::create([
                        'property_type_id' => $houseType->id,
                        'name' => $houseData['name'],
                        'description' => $houseData['description'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Nigerian property subtypes added successfully!');
    }
}