<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AreaSeeder extends Seeder
{
    /**
     * Insert areas with required fields
     */
    private function insertAreas(array $areas, $now)
    {
        foreach ($areas as &$area) {
            $area['slug'] = Str::slug($area['name']);
            $area['created_at'] = $now;
            $area['updated_at'] = $now;
        }
        DB::table('areas')->insert($areas);
    }

    /**
     * Run the database seeds - Focused on Maiduguri areas
     */
    public function run(): void
    {
        if (DB::table('areas')->count() == 0) {
            $now = now();

            // Find Maiduguri city ID dynamically
            $maiduguriCity = DB::table('cities')
                ->join('states', 'cities.state_id', '=', 'states.id')
                ->where('states.name', 'Borno')
                ->where('cities.name', 'Maiduguri')
                ->select('cities.id')
                ->first();

            if (!$maiduguriCity) {
                $this->command->error('Maiduguri city not found in Borno state!');
                return;
            }

            $maiduguriCityId = $maiduguriCity->id;

            // Major Areas/Districts in Maiduguri, Borno State
            $maiduguriAreas = [
                // Central/Commercial Areas
                ['name' => 'Monday Market', 'city_id' => $maiduguriCityId, 'type' => 'commercial'],
                ['name' => 'Custom Area', 'city_id' => $maiduguriCityId, 'type' => 'commercial'],
                ['name' => 'Post Office Area', 'city_id' => $maiduguriCityId, 'type' => 'commercial'],
                ['name' => 'Gamboru Market', 'city_id' => $maiduguriCityId, 'type' => 'commercial'],

                // Residential Areas - High Income
                ['name' => 'GRA (Government Reserved Area)', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Old GRA', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'New GRA', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Mairi', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Pompomari', 'city_id' => $maiduguriCityId, 'type' => 'residential'],

                // Residential Areas - Middle Income
                ['name' => 'Bulumkutu', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Lamisula', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Gwange', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Hausari', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Mafoni', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Fori', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Jimtilo', 'city_id' => $maiduguriCityId, 'type' => 'residential'],

                // University/Educational Areas
                ['name' => 'University of Maiduguri Area', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Polo Area', 'city_id' => $maiduguriCityId, 'type' => 'residential'],

                // Industrial/Mixed Areas
                ['name' => 'Baga Road', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Kano Road', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Bama Road', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Damboa Road', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],

                // Traditional/Local Areas
                ['name' => 'Shehuri North', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Shehuri South', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Bolori', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Kaleri', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Gamboru', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Ngomari', 'city_id' => $maiduguriCityId, 'type' => 'residential'],

                // Airport/Military Areas
                ['name' => 'Airport Area', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Giwa Barracks Area', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],

                // Newer Development Areas
                ['name' => 'Jere', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Molai', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Cashew', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Dalori', 'city_id' => $maiduguriCityId, 'type' => 'residential'],

                // Outskirts/Suburban Areas
                ['name' => 'Konduga', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Dikwa', 'city_id' => $maiduguriCityId, 'type' => 'residential'],
                ['name' => 'Biu Road', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Gombe Road', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],

                // Market Areas
                ['name' => 'Tashan Bama', 'city_id' => $maiduguriCityId, 'type' => 'commercial'],
                ['name' => 'Yan Awaki Market', 'city_id' => $maiduguriCityId, 'type' => 'commercial'],
                ['name' => 'Kasuwan Shanu', 'city_id' => $maiduguriCityId, 'type' => 'commercial'],

                // Religious/Cultural Areas
                ['name' => 'Shehu Laminu Way', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Ramat Square Area', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],

                // Administrative Areas
                ['name' => 'State Secretariat Area', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
                ['name' => 'Government House Area', 'city_id' => $maiduguriCityId, 'type' => 'mixed'],
            ];

            $this->insertAreas($maiduguriAreas, $now);

            $this->command->info('Maiduguri areas seeded successfully! (' . count($maiduguriAreas) . ' areas)');
        } else {
            $this->command->info('Areas table already has data, skipping seeding.');
        }
    }
}