<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class NeighborhoodDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $neighborhoodData = [
            'Lekki Phase 1' => [
                'education_facilities' => [
                    ['name' => 'Corona Schools', 'distance' => '0.5km', 'type' => 'primary'],
                    ['name' => 'Greensprings School', 'distance' => '1.2km', 'type' => 'secondary'],
                    ['name' => 'British International School', 'distance' => '2.0km', 'type' => 'international']
                ],
                'healthcare_facilities' => [
                    ['name' => 'Reddington Hospital', 'distance' => '1.8km', 'type' => 'hospital'],
                    ['name' => 'Lekki Medicare', 'distance' => '0.6km', 'type' => 'clinic'],
                    ['name' => 'Phase 1 Medical Centre', 'distance' => '0.3km', 'type' => 'clinic']
                ],
                'shopping_facilities' => [
                    ['name' => 'Palms Shopping Mall', 'distance' => '3.2km', 'type' => 'mall'],
                    ['name' => 'Circle Mall', 'distance' => '1.5km', 'type' => 'mall'],
                    ['name' => 'Lekki Market', 'distance' => '0.8km', 'type' => 'market']
                ],
                'transport_facilities' => [
                    ['name' => 'Lekki-Epe Expressway', 'distance' => '0.4km', 'type' => 'highway'],
                    ['name' => 'BRT Lekki Station', 'distance' => '2.1km', 'type' => 'brt'],
                    ['name' => 'Uber/Bolt Hub', 'distance' => '0.2km', 'type' => 'rideshare']
                ],
                'security_rating' => 8.7,
                'security_features' => ['24/7 Security', 'CCTV', 'Gated Estate', 'Police Patrol'],
                'walkability_score' => 7.5,
                'average_rent' => 2500000,
                'lifestyle_tags' => ['Family-Friendly', 'Upscale', 'Quiet', 'Expatriate Community'],
                'electricity_supply' => [
                    'quality' => 'Excellent',
                    'availability' => '20-22 hrs/day',
                    'reliability' => 'Very Good',
                    'backup' => 'Generator Available'
                ],
                'water_supply' => [
                    'quality' => 'Good',
                    'availability' => 'Daily',
                    'source' => 'Borehole + Municipal',
                    'pressure' => 'Good'
                ]
            ],
            
            'Victoria Island Central' => [
                'education_facilities' => [
                    ['name' => 'Lagos Business School', 'distance' => '1.0km', 'type' => 'university'],
                    ['name' => 'Dowen College', 'distance' => '2.5km', 'type' => 'secondary'],
                    ['name' => 'Grange School', 'distance' => '1.8km', 'type' => 'primary']
                ],
                'healthcare_facilities' => [
                    ['name' => 'EKO Hospital', 'distance' => '0.8km', 'type' => 'hospital'],
                    ['name' => 'Gold Cross Hospital', 'distance' => '1.2km', 'type' => 'hospital'],
                    ['name' => 'VI Medical Centre', 'distance' => '0.4km', 'type' => 'clinic']
                ],
                'shopping_facilities' => [
                    ['name' => 'The Palms Lekki', 'distance' => '4.5km', 'type' => 'mall'],
                    ['name' => 'Mega Plaza', 'distance' => '2.1km', 'type' => 'mall'],
                    ['name' => 'VI Market', 'distance' => '0.6km', 'type' => 'market']
                ],
                'transport_facilities' => [
                    ['name' => 'Third Mainland Bridge', 'distance' => '1.2km', 'type' => 'bridge'],
                    ['name' => 'Falomo Bridge', 'distance' => '0.8km', 'type' => 'bridge'],
                    ['name' => 'VI Ferry Terminal', 'distance' => '2.0km', 'type' => 'ferry']
                ],
                'security_rating' => 9.1,
                'security_features' => ['24/7 Security', 'CCTV', 'Police Checkpoints', 'Corporate Security'],
                'walkability_score' => 8.8,
                'average_rent' => 4200000,
                'lifestyle_tags' => ['Business District', 'Bustling', 'Upscale', 'International'],
                'electricity_supply' => [
                    'quality' => 'Excellent',
                    'availability' => '22-24 hrs/day',
                    'reliability' => 'Excellent',
                    'backup' => 'Multiple Generators'
                ],
                'water_supply' => [
                    'quality' => 'Excellent',
                    'availability' => '24/7',
                    'source' => 'Treated Municipal',
                    'pressure' => 'Excellent'
                ]
            ],
            
            'Ikoyi Central' => [
                'education_facilities' => [
                    ['name' => 'Loyola Jesuit College', 'distance' => '1.5km', 'type' => 'secondary'],
                    ['name' => 'Ikoyi Club Golf Course', 'distance' => '0.7km', 'type' => 'recreational'],
                    ['name' => 'American International School', 'distance' => '2.2km', 'type' => 'international']
                ],
                'healthcare_facilities' => [
                    ['name' => 'Ikoyi Medical Centre', 'distance' => '0.5km', 'type' => 'clinic'],
                    ['name' => 'St. Nicholas Hospital', 'distance' => '1.1km', 'type' => 'hospital'],
                    ['name' => 'Premier Specialist Hospital', 'distance' => '1.8km', 'type' => 'hospital']
                ],
                'shopping_facilities' => [
                    ['name' => 'Silverbird Galleria', 'distance' => '2.8km', 'type' => 'mall'],
                    ['name' => 'Park n Shop', 'distance' => '0.9km', 'type' => 'supermarket'],
                    ['name' => 'Ikoyi Club', 'distance' => '0.7km', 'type' => 'exclusive']
                ],
                'transport_facilities' => [
                    ['name' => 'Ikoyi Bridge', 'distance' => '1.0km', 'type' => 'bridge'],
                    ['name' => 'VI Link Bridge', 'distance' => '1.5km', 'type' => 'bridge'],
                    ['name' => 'Water Taxi Terminal', 'distance' => '2.3km', 'type' => 'ferry']
                ],
                'security_rating' => 9.3,
                'security_features' => ['Elite Security', 'CCTV', 'Private Guards', 'Controlled Access'],
                'walkability_score' => 8.2,
                'average_rent' => 5500000,
                'lifestyle_tags' => ['Elite', 'Prestigious', 'Quiet', 'Diplomatic Quarter'],
                'electricity_supply' => [
                    'quality' => 'Premium',
                    'availability' => '23-24 hrs/day',
                    'reliability' => 'Premium',
                    'backup' => 'Dedicated Generators'
                ],
                'water_supply' => [
                    'quality' => 'Premium',
                    'availability' => '24/7',
                    'source' => 'Treated + Filtered',
                    'pressure' => 'Excellent'
                ]
            ],
            
            'GRA Ikeja' => [
                'education_facilities' => [
                    ['name' => 'Lagos State University', 'distance' => '3.2km', 'type' => 'university'],
                    ['name' => 'Ikeja High School', 'distance' => '1.1km', 'type' => 'secondary'],
                    ['name' => 'GRA Primary School', 'distance' => '0.6km', 'type' => 'primary']
                ],
                'healthcare_facilities' => [
                    ['name' => 'Lagos University Teaching Hospital', 'distance' => '2.5km', 'type' => 'hospital'],
                    ['name' => 'Ikeja Medical Centre', 'distance' => '0.8km', 'type' => 'clinic'],
                    ['name' => 'GRA Health Centre', 'distance' => '0.4km', 'type' => 'clinic']
                ],
                'shopping_facilities' => [
                    ['name' => 'Ikeja City Mall', 'distance' => '1.8km', 'type' => 'mall'],
                    ['name' => 'Computer Village', 'distance' => '2.2km', 'type' => 'electronics'],
                    ['name' => 'Allen Avenue Market', 'distance' => '1.5km', 'type' => 'market']
                ],
                'transport_facilities' => [
                    ['name' => 'Murtala Muhammed Airport', 'distance' => '4.5km', 'type' => 'airport'],
                    ['name' => 'Ikeja BRT Station', 'distance' => '1.2km', 'type' => 'brt'],
                    ['name' => 'Lagos-Ibadan Expressway', 'distance' => '2.0km', 'type' => 'highway']
                ],
                'security_rating' => 7.8,
                'security_features' => ['Gated Community', 'CCTV', 'Security Patrol', 'Street Lighting'],
                'walkability_score' => 6.5,
                'average_rent' => 1800000,
                'lifestyle_tags' => ['Government Quarter', 'Accessible', 'Established', 'Central'],
                'electricity_supply' => [
                    'quality' => 'Good',
                    'availability' => '16-18 hrs/day',
                    'reliability' => 'Fair',
                    'backup' => 'Community Generator'
                ],
                'water_supply' => [
                    'quality' => 'Good',
                    'availability' => 'Daily',
                    'source' => 'Municipal + Well',
                    'pressure' => 'Fair'
                ]
            ]
        ];

        foreach ($neighborhoodData as $areaName => $data) {
            $area = Area::where('name', $areaName)->first();
            
            if ($area) {
                $area->update($data);
                $this->command->info("Updated neighborhood data for: {$areaName}");
            } else {
                $this->command->warn("Area not found: {$areaName}");
            }
        }
    }
}