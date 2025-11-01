<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StateSeeder extends Seeder
{
   /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (State::count() == 0) {
            $now = now();

            $states = [
                ['name' => "Abia", 'code' => 'AB'],
                ['name' => "Adamawa", 'code' => 'AD'],
                ['name' => "Anambra", 'code' => 'AN'],
                ['name' => "Akwa Ibom", 'code' => 'AK'],
                ['name' => "Bauchi", 'code' => 'BA'],
                ['name' => "Bayelsa", 'code' => 'BY'],
                ['name' => "Benue", 'code' => 'BE'],
                ['name' => "Borno", 'code' => 'BO'],
                ['name' => "Cross River", 'code' => 'CR'],
                ['name' => "Delta", 'code' => 'DE'],
                ['name' => "Ebonyi", 'code' => 'EB'],
                ['name' => "Enugu", 'code' => 'EN'],
                ['name' => "Edo", 'code' => 'ED'],
                ['name' => "Ekiti", 'code' => 'EK'],
                ['name' => "FCT - Abuja", 'code' => 'FC'],
                ['name' => "Gombe", 'code' => 'GO'],
                ['name' => "Imo", 'code' => 'IM'],
                ['name' => "Jigawa", 'code' => 'JI'],
                ['name' => "Kaduna", 'code' => 'KD'],
                ['name' => "Kano", 'code' => 'KN'],
                ['name' => "Katsina", 'code' => 'KT'],
                ['name' => "Kebbi", 'code' => 'KE'],
                ['name' => "Kogi", 'code' => 'KO'],
                ['name' => "Kwara", 'code' => 'KW'],
                ['name' => "Lagos", 'code' => 'LA'],
                ['name' => "Nasarawa", 'code' => 'NA'],
                ['name' => "Niger", 'code' => 'NI'],
                ['name' => "Ogun", 'code' => 'OG'],
                ['name' => "Ondo", 'code' => 'ON'],
                ['name' => "Osun", 'code' => 'OS'],
                ['name' => "Oyo", 'code' => 'OY'],
                ['name' => "Plateau", 'code' => 'PL'],
                ['name' => "Rivers", 'code' => 'RI'],
                ['name' => "Sokoto", 'code' => 'SO'],
                ['name' => "Taraba", 'code' => 'TA'],
                ['name' => "Yobe", 'code' => 'YO'],
                ['name' => "Zamfara", 'code' => 'ZA']
            ];

            // Add timestamps to each state
            foreach ($states as &$state) {
                $state['created_at'] = $now;
                $state['updated_at'] = $now;
            }

            State::insert($states);
        }
    }
}
