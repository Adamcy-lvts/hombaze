<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\State;
use App\Models\City;
use App\Models\Area;

class Phase2LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Phase 2: Location Data...');

        // Validate prerequisites
        $this->validatePrerequisites();

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing location data in reverse dependency order
        $this->command->info('Clearing existing Phase 2 records...');
        Area::truncate();
        City::truncate();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedCities();
        $this->seedAreas();

        $this->command->info('Phase 2 Location Data seeded successfully!');
    }

    /**
     * Validate that prerequisite data exists before seeding
     */
    private function validatePrerequisites(): void
    {
        $this->command->info('Validating prerequisites...');

        // Check if states exist
        $stateCount = State::count();
        if ($stateCount === 0) {
            throw new \Exception('No states found. Please run Phase1FoundationSeeder first.');
        }

        $this->command->info("Prerequisites validated: {$stateCount} states found");
    }

    private function seedCities(): void
    {
        $cities = [
            // Lagos State - Major LGAs
            ['name' => 'Lagos Island', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Ikeja', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Surulere', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Victoria Island', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Ikoyi', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Lekki', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Ajah', 'state_code' => 'LA', 'type' => 'town'],
            ['name' => 'Yaba', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Gbagada', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Magodo', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Alimosho', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Agege', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Mushin', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Oshodi-Isolo', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Shomolu', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Kosofe', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Ikorodu', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Badagry', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Epe', 'state_code' => 'LA', 'type' => 'city'],
            ['name' => 'Ibeju-Lekki', 'state_code' => 'LA', 'type' => 'city'],

            // Federal Capital Territory - All Area Councils
            ['name' => 'Central Business District', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Garki', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Wuse', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Maitama', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Asokoro', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Gwarinpa', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Kubwa', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Lugbe', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Gwagwalada', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Kuje', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Abaji', 'state_code' => 'FCT', 'type' => 'city'],
            ['name' => 'Bwari', 'state_code' => 'FCT', 'type' => 'city'],

            // Rivers State - Major LGAs
            ['name' => 'Port Harcourt', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Obio-Akpor', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Eleme', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Bonny', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Okrika', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Oyigbo', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Ikwerre', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Emohua', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Degema', 'state_code' => 'RI', 'type' => 'city'],
            ['name' => 'Ahoada East', 'state_code' => 'RI', 'type' => 'city'],

            // Borno State - FINALLY ADDING MAIDUGURI AND OTHER LGAs
            ['name' => 'Maiduguri', 'state_code' => 'BO', 'type' => 'city'], // State Capital
            ['name' => 'Jere', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Konduga', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Bama', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Gwoza', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Dikwa', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Biu', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Damboa', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Gubio', 'state_code' => 'BO', 'type' => 'city'],
            ['name' => 'Monguno', 'state_code' => 'BO', 'type' => 'city'],

            // Kano State - Extended LGAs
            ['name' => 'Kano Metropolitan', 'state_code' => 'KN', 'type' => 'city'],
            ['name' => 'Nassarawa', 'state_code' => 'KN', 'type' => 'city'],
            ['name' => 'Fagge', 'state_code' => 'KN', 'type' => 'city'],
            ['name' => 'Dala', 'state_code' => 'KN', 'type' => 'city'],
            ['name' => 'Gwale', 'state_code' => 'KN', 'type' => 'city'],
            ['name' => 'Tarauni', 'state_code' => 'KN', 'type' => 'city'],
            ['name' => 'Ungogo', 'state_code' => 'KN', 'type' => 'city'],
            ['name' => 'Kumbotso', 'state_code' => 'KN', 'type' => 'city'],

            // Oyo State - Extended LGAs
            ['name' => 'Ibadan North', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Ibadan South-West', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Ibadan North-East', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Ibadan South-East', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Ibadan North-West', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Ogbomoso North', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Ogbomoso South', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Oyo East', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Oyo West', 'state_code' => 'OY', 'type' => 'city'],
            ['name' => 'Iseyin', 'state_code' => 'OY', 'type' => 'city'],

            // Kaduna State - Extended LGAs
            ['name' => 'Kaduna North', 'state_code' => 'KD', 'type' => 'city'],
            ['name' => 'Kaduna South', 'state_code' => 'KD', 'type' => 'city'],
            ['name' => 'Zaria', 'state_code' => 'KD', 'type' => 'city'],
            ['name' => 'Sabon Gari', 'state_code' => 'KD', 'type' => 'city'],
            ['name' => 'Chikun', 'state_code' => 'KD', 'type' => 'city'],
            ['name' => 'Igabi', 'state_code' => 'KD', 'type' => 'city'],
            ['name' => 'Giwa', 'state_code' => 'KD', 'type' => 'city'],
            ['name' => 'Makarfi', 'state_code' => 'KD', 'type' => 'city'],

            // Anambra State - Extended LGAs
            ['name' => 'Awka', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Onitsha', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Nnewi North', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Nnewi South', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Idemili North', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Idemili South', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Awka South', 'state_code' => 'AN', 'type' => 'city'],
            ['name' => 'Awka North', 'state_code' => 'AN', 'type' => 'city'],

            // Enugu State - Extended LGAs
            ['name' => 'Enugu East', 'state_code' => 'EN', 'type' => 'city'],
            ['name' => 'Enugu North', 'state_code' => 'EN', 'type' => 'city'],
            ['name' => 'Enugu South', 'state_code' => 'EN', 'type' => 'city'],
            ['name' => 'Nkanu East', 'state_code' => 'EN', 'type' => 'city'],
            ['name' => 'Nkanu West', 'state_code' => 'EN', 'type' => 'city'],
            ['name' => 'Nsukka', 'state_code' => 'EN', 'type' => 'city'],
            ['name' => 'Udi', 'state_code' => 'EN', 'type' => 'city'],
            ['name' => 'Ezeagu', 'state_code' => 'EN', 'type' => 'city'],

            // Cross River State - Extended LGAs
            ['name' => 'Calabar Municipal', 'state_code' => 'CR', 'type' => 'city'],
            ['name' => 'Calabar South', 'state_code' => 'CR', 'type' => 'city'],
            ['name' => 'Akpabuyo', 'state_code' => 'CR', 'type' => 'city'],
            ['name' => 'Odukpani', 'state_code' => 'CR', 'type' => 'city'],
            ['name' => 'Ogoja', 'state_code' => 'CR', 'type' => 'city'],
            ['name' => 'Ikom', 'state_code' => 'CR', 'type' => 'city'],

            // Akwa Ibom State - Extended LGAs
            ['name' => 'Uyo', 'state_code' => 'AK', 'type' => 'city'],
            ['name' => 'Ikot Ekpene', 'state_code' => 'AK', 'type' => 'city'],
            ['name' => 'Eket', 'state_code' => 'AK', 'type' => 'city'],
            ['name' => 'Abak', 'state_code' => 'AK', 'type' => 'city'],
            ['name' => 'Oron', 'state_code' => 'AK', 'type' => 'city'],
            ['name' => 'Essien Udim', 'state_code' => 'AK', 'type' => 'city'],

            // ALL OTHER STATE CAPITALS AND MAJOR LGAs

            // Abia State
            ['name' => 'Umuahia', 'state_code' => 'AB', 'type' => 'city'], // State Capital
            ['name' => 'Aba North', 'state_code' => 'AB', 'type' => 'city'],
            ['name' => 'Aba South', 'state_code' => 'AB', 'type' => 'city'],
            ['name' => 'Arochukwu', 'state_code' => 'AB', 'type' => 'city'],
            ['name' => 'Bende', 'state_code' => 'AB', 'type' => 'city'],

            // Adamawa State
            ['name' => 'Yola North', 'state_code' => 'AD', 'type' => 'city'], // State Capital
            ['name' => 'Yola South', 'state_code' => 'AD', 'type' => 'city'],
            ['name' => 'Mubi North', 'state_code' => 'AD', 'type' => 'city'],
            ['name' => 'Mubi South', 'state_code' => 'AD', 'type' => 'city'],
            ['name' => 'Jimeta', 'state_code' => 'AD', 'type' => 'city'],

            // Bauchi State
            ['name' => 'Bauchi', 'state_code' => 'BA', 'type' => 'city'], // State Capital
            ['name' => 'Azare', 'state_code' => 'BA', 'type' => 'city'],
            ['name' => 'Misau', 'state_code' => 'BA', 'type' => 'city'],
            ['name' => 'Jama\'are', 'state_code' => 'BA', 'type' => 'city'],

            // Bayelsa State
            ['name' => 'Yenagoa', 'state_code' => 'BY', 'type' => 'city'], // State Capital
            ['name' => 'Brass', 'state_code' => 'BY', 'type' => 'city'],
            ['name' => 'Sagbama', 'state_code' => 'BY', 'type' => 'city'],
            ['name' => 'Nembe', 'state_code' => 'BY', 'type' => 'city'],

            // Benue State
            ['name' => 'Makurdi', 'state_code' => 'BE', 'type' => 'city'], // State Capital
            ['name' => 'Gboko', 'state_code' => 'BE', 'type' => 'city'],
            ['name' => 'Otukpo', 'state_code' => 'BE', 'type' => 'city'],
            ['name' => 'Katsina-Ala', 'state_code' => 'BE', 'type' => 'city'],

            // Delta State - Extended
            ['name' => 'Asaba', 'state_code' => 'DE', 'type' => 'city'], // State Capital
            ['name' => 'Warri South', 'state_code' => 'DE', 'type' => 'city'],
            ['name' => 'Warri North', 'state_code' => 'DE', 'type' => 'city'],
            ['name' => 'Warri South-West', 'state_code' => 'DE', 'type' => 'city'],
            ['name' => 'Uvwie', 'state_code' => 'DE', 'type' => 'city'],
            ['name' => 'Sapele', 'state_code' => 'DE', 'type' => 'city'],
            ['name' => 'Ughelli North', 'state_code' => 'DE', 'type' => 'city'],
            ['name' => 'Ughelli South', 'state_code' => 'DE', 'type' => 'city'],

            // Ebonyi State
            ['name' => 'Abakaliki', 'state_code' => 'EB', 'type' => 'city'], // State Capital
            ['name' => 'Afikpo North', 'state_code' => 'EB', 'type' => 'city'],
            ['name' => 'Afikpo South', 'state_code' => 'EB', 'type' => 'city'],
            ['name' => 'Onuimo', 'state_code' => 'EB', 'type' => 'city'],

            // Edo State - Extended
            ['name' => 'Benin City', 'state_code' => 'ED', 'type' => 'city'], // State Capital
            ['name' => 'Ikpoba Okha', 'state_code' => 'ED', 'type' => 'city'],
            ['name' => 'Egor', 'state_code' => 'ED', 'type' => 'city'],
            ['name' => 'Oredo', 'state_code' => 'ED', 'type' => 'city'],
            ['name' => 'Uhunmwonde', 'state_code' => 'ED', 'type' => 'city'],

            // Ekiti State
            ['name' => 'Ado-Ekiti', 'state_code' => 'EK', 'type' => 'city'], // State Capital
            ['name' => 'Ikere', 'state_code' => 'EK', 'type' => 'city'],
            ['name' => 'Oye', 'state_code' => 'EK', 'type' => 'city'],
            ['name' => 'Ijero', 'state_code' => 'EK', 'type' => 'city'],

            // Gombe State
            ['name' => 'Gombe', 'state_code' => 'GO', 'type' => 'city'], // State Capital
            ['name' => 'Billiri', 'state_code' => 'GO', 'type' => 'city'],
            ['name' => 'Dukku', 'state_code' => 'GO', 'type' => 'city'],
            ['name' => 'Kaltungo', 'state_code' => 'GO', 'type' => 'city'],

            // Imo State - Extended
            ['name' => 'Owerri Municipal', 'state_code' => 'IM', 'type' => 'city'], // State Capital
            ['name' => 'Owerri North', 'state_code' => 'IM', 'type' => 'city'],
            ['name' => 'Owerri West', 'state_code' => 'IM', 'type' => 'city'],
            ['name' => 'Orlu', 'state_code' => 'IM', 'type' => 'city'],
            ['name' => 'Okigwe', 'state_code' => 'IM', 'type' => 'city'],

            // Jigawa State
            ['name' => 'Dutse', 'state_code' => 'JI', 'type' => 'city'], // State Capital
            ['name' => 'Hadejia', 'state_code' => 'JI', 'type' => 'city'],
            ['name' => 'Kazaure', 'state_code' => 'JI', 'type' => 'city'],
            ['name' => 'Ringim', 'state_code' => 'JI', 'type' => 'city'],

            // Kebbi State
            ['name' => 'Birnin Kebbi', 'state_code' => 'KB', 'type' => 'city'], // State Capital
            ['name' => 'Argungu', 'state_code' => 'KB', 'type' => 'city'],
            ['name' => 'Yauri', 'state_code' => 'KB', 'type' => 'city'],
            ['name' => 'Zuru', 'state_code' => 'KB', 'type' => 'city'],

            // Kogi State
            ['name' => 'Lokoja', 'state_code' => 'KG', 'type' => 'city'], // State Capital
            ['name' => 'Okene', 'state_code' => 'KG', 'type' => 'city'],
            ['name' => 'Kabba', 'state_code' => 'KG', 'type' => 'city'],
            ['name' => 'Ankpa', 'state_code' => 'KG', 'type' => 'city'],

            // Katsina State
            ['name' => 'Katsina', 'state_code' => 'KT', 'type' => 'city'], // State Capital
            ['name' => 'Daura', 'state_code' => 'KT', 'type' => 'city'],
            ['name' => 'Funtua', 'state_code' => 'KT', 'type' => 'city'],
            ['name' => 'Malumfashi', 'state_code' => 'KT', 'type' => 'city'],

            // Kwara State
            ['name' => 'Ilorin', 'state_code' => 'KW', 'type' => 'city'], // State Capital
            ['name' => 'Offa', 'state_code' => 'KW', 'type' => 'city'],
            ['name' => 'Omu-Aran', 'state_code' => 'KW', 'type' => 'city'],
            ['name' => 'Lafiagi', 'state_code' => 'KW', 'type' => 'city'],

            // Nasarawa State
            ['name' => 'Lafia', 'state_code' => 'NA', 'type' => 'city'], // State Capital
            ['name' => 'Keffi', 'state_code' => 'NA', 'type' => 'city'],
            ['name' => 'Akwanga', 'state_code' => 'NA', 'type' => 'city'],
            ['name' => 'Nasarawa', 'state_code' => 'NA', 'type' => 'city'],

            // Niger State
            ['name' => 'Minna', 'state_code' => 'NI', 'type' => 'city'], // State Capital
            ['name' => 'Bida', 'state_code' => 'NI', 'type' => 'city'],
            ['name' => 'Kontagora', 'state_code' => 'NI', 'type' => 'city'],
            ['name' => 'Suleja', 'state_code' => 'NI', 'type' => 'city'],

            // Ogun State - Extended
            ['name' => 'Abeokuta North', 'state_code' => 'OG', 'type' => 'city'], // State Capital
            ['name' => 'Abeokuta South', 'state_code' => 'OG', 'type' => 'city'],
            ['name' => 'Sagamu', 'state_code' => 'OG', 'type' => 'city'],
            ['name' => 'Ijebu Ode', 'state_code' => 'OG', 'type' => 'city'],
            ['name' => 'Ijebu North', 'state_code' => 'OG', 'type' => 'city'],
            ['name' => 'Ota', 'state_code' => 'OG', 'type' => 'city'],

            // Ondo State
            ['name' => 'Akure', 'state_code' => 'ON', 'type' => 'city'], // State Capital
            ['name' => 'Ondo West', 'state_code' => 'ON', 'type' => 'city'],
            ['name' => 'Ondo East', 'state_code' => 'ON', 'type' => 'city'],
            ['name' => 'Owo', 'state_code' => 'ON', 'type' => 'city'],

            // Osun State
            ['name' => 'Osogbo', 'state_code' => 'OS', 'type' => 'city'], // State Capital
            ['name' => 'Ile-Ife', 'state_code' => 'OS', 'type' => 'city'],
            ['name' => 'Ilesa', 'state_code' => 'OS', 'type' => 'city'],
            ['name' => 'Ede North', 'state_code' => 'OS', 'type' => 'city'],

            // Plateau State - Extended
            ['name' => 'Jos North', 'state_code' => 'PL', 'type' => 'city'], // State Capital
            ['name' => 'Jos South', 'state_code' => 'PL', 'type' => 'city'],
            ['name' => 'Jos East', 'state_code' => 'PL', 'type' => 'city'],
            ['name' => 'Pankshin', 'state_code' => 'PL', 'type' => 'city'],
            ['name' => 'Barkin Ladi', 'state_code' => 'PL', 'type' => 'city'],

            // Sokoto State
            ['name' => 'Sokoto North', 'state_code' => 'SO', 'type' => 'city'], // State Capital
            ['name' => 'Sokoto South', 'state_code' => 'SO', 'type' => 'city'],
            ['name' => 'Wamako', 'state_code' => 'SO', 'type' => 'city'],
            ['name' => 'Gwadabawa', 'state_code' => 'SO', 'type' => 'city'],

            // Taraba State
            ['name' => 'Jalingo', 'state_code' => 'TA', 'type' => 'city'], // State Capital
            ['name' => 'Wukari', 'state_code' => 'TA', 'type' => 'city'],
            ['name' => 'Bali', 'state_code' => 'TA', 'type' => 'city'],
            ['name' => 'Gembu', 'state_code' => 'TA', 'type' => 'city'],

            // Yobe State
            ['name' => 'Damaturu', 'state_code' => 'YO', 'type' => 'city'], // State Capital
            ['name' => 'Potiskum', 'state_code' => 'YO', 'type' => 'city'],
            ['name' => 'Gashua', 'state_code' => 'YO', 'type' => 'city'],
            ['name' => 'Nguru', 'state_code' => 'YO', 'type' => 'city'],

            // Zamfara State
            ['name' => 'Gusau', 'state_code' => 'ZA', 'type' => 'city'], // State Capital
            ['name' => 'Kaura Namoda', 'state_code' => 'ZA', 'type' => 'city'],
            ['name' => 'Talata Mafara', 'state_code' => 'ZA', 'type' => 'city'],
            ['name' => 'Zurmi', 'state_code' => 'ZA', 'type' => 'city']
        ];

        foreach ($cities as $cityData) {
            $state = State::where('code', $cityData['state_code'])->first();
            if ($state) {
                City::create([
                    'name' => $cityData['name'],
                    'state_id' => $state->id,
                    'type' => $cityData['type'],
                    'is_active' => true,
                    'sort_order' => 0
                ]);
            }
        }

        $this->command->info('Cities seeded successfully.');
    }

    private function seedAreas(): void
    {
        $areas = [
            // Lagos State Areas - Comprehensive coverage
            ['city' => 'Victoria Island', 'areas' => [
                ['name' => 'Victoria Island Central', 'type' => 'commercial'],
                ['name' => 'Oniru Estate', 'type' => 'residential'],
                ['name' => 'Tiamiyu Savage', 'type' => 'mixed'],
                ['name' => 'Ahmadu Bello Way', 'type' => 'commercial'],
                ['name' => 'Adetokunbo Ademola', 'type' => 'commercial'],
                ['name' => 'Bar Beach', 'type' => 'mixed'],
                ['name' => 'Kofo Abayomi', 'type' => 'commercial'],
                ['name' => 'Karimu Kotun', 'type' => 'mixed']
            ]],
            ['city' => 'Ikoyi', 'areas' => [
                ['name' => 'Old Ikoyi', 'type' => 'residential'],
                ['name' => 'Ikoyi Central', 'type' => 'mixed'],
                ['name' => 'Parkview Estate', 'type' => 'residential'],
                ['name' => 'Banana Island', 'type' => 'residential'],
                ['name' => 'Dolphin Estate', 'type' => 'residential'],
                ['name' => 'Bourdillon Road', 'type' => 'residential'],
                ['name' => 'Alexander Avenue', 'type' => 'mixed'],
                ['name' => 'Awolowo Road', 'type' => 'mixed']
            ]],
            ['city' => 'Lekki', 'areas' => [
                ['name' => 'Lekki Phase 1', 'type' => 'residential'],
                ['name' => 'Lekki Phase 2', 'type' => 'residential'],
                ['name' => 'Victoria Garden City (VGC)', 'type' => 'residential'],
                ['name' => 'Osapa London', 'type' => 'residential'],
                ['name' => 'Sangotedo', 'type' => 'mixed'],
                ['name' => 'Ajah', 'type' => 'mixed'],
                ['name' => 'Chevron Drive', 'type' => 'residential'],
                ['name' => 'Pinnock Beach Estate', 'type' => 'residential'],
                ['name' => 'Ikate Elegushi', 'type' => 'mixed'],
                ['name' => 'Richmond Estate', 'type' => 'residential'],
                ['name' => 'Oral Estate', 'type' => 'residential'],
                ['name' => 'Elf Estate', 'type' => 'residential']
            ]],
            ['city' => 'Ikeja', 'areas' => [
                ['name' => 'GRA Ikeja', 'type' => 'residential'],
                ['name' => 'Allen Avenue', 'type' => 'commercial'],
                ['name' => 'Computer Village', 'type' => 'commercial'],
                ['name' => 'Maryland', 'type' => 'mixed'],
                ['name' => 'Alausa', 'type' => 'mixed'],
                ['name' => 'Omole Phase 1', 'type' => 'residential'],
                ['name' => 'Omole Phase 2', 'type' => 'residential'],
                ['name' => 'Oregun', 'type' => 'industrial'],
                ['name' => 'Opebi', 'type' => 'mixed'],
                ['name' => 'Agidingbi', 'type' => 'residential'],
                ['name' => 'Toyin Street', 'type' => 'commercial'],
                ['name' => 'Adeniyi Jones', 'type' => 'mixed']
            ]],
            ['city' => 'Surulere', 'areas' => [
                ['name' => 'Surulere Central', 'type' => 'residential'],
                ['name' => 'National Stadium', 'type' => 'mixed'],
                ['name' => 'Adeniran Ogunsanya', 'type' => 'commercial'],
                ['name' => 'Shitta', 'type' => 'residential'],
                ['name' => 'Aguda', 'type' => 'residential'],
                ['name' => 'Ojuelegba', 'type' => 'mixed'],
                ['name' => 'Yaba Tech', 'type' => 'mixed'],
                ['name' => 'Bode Thomas', 'type' => 'residential']
            ]],
            ['city' => 'Yaba', 'areas' => [
                ['name' => 'Yaba Central', 'type' => 'mixed'],
                ['name' => 'Sabo', 'type' => 'residential'],
                ['name' => 'Tejuosho', 'type' => 'commercial'],
                ['name' => 'University of Lagos', 'type' => 'mixed'],
                ['name' => 'Akoka', 'type' => 'mixed'],
                ['name' => 'Fadeyi', 'type' => 'mixed'],
                ['name' => 'Onike', 'type' => 'residential']
            ]],
            ['city' => 'Lagos Island', 'areas' => [
                ['name' => 'Lagos Island Central', 'type' => 'commercial'],
                ['name' => 'Marina', 'type' => 'commercial'],
                ['name' => 'Broad Street', 'type' => 'commercial'],
                ['name' => 'CMS', 'type' => 'commercial'],
                ['name' => 'Tafawa Balewa Square', 'type' => 'mixed'],
                ['name' => 'National Theatre', 'type' => 'mixed'],
                ['name' => 'Isale Eko', 'type' => 'residential']
            ]],
            ['city' => 'Gbagada', 'areas' => [
                ['name' => 'Gbagada Phase 1', 'type' => 'residential'],
                ['name' => 'Gbagada Phase 2', 'type' => 'residential'],
                ['name' => 'New Garage', 'type' => 'mixed'],
                ['name' => 'Ifako', 'type' => 'residential'],
                ['name' => 'Soluyi', 'type' => 'residential']
            ]],
            ['city' => 'Magodo', 'areas' => [
                ['name' => 'Magodo Phase 1', 'type' => 'residential'],
                ['name' => 'Magodo Phase 2', 'type' => 'residential'],
                ['name' => 'CMD Road', 'type' => 'mixed'],
                ['name' => 'Isheri North', 'type' => 'residential']
            ]],

            // Federal Capital Territory (Abuja) Areas - Comprehensive coverage
            ['city' => 'Central Business District', 'areas' => [
                ['name' => 'Three Arms Zone', 'type' => 'government'],
                ['name' => 'Central Area', 'type' => 'commercial'],
                ['name' => 'Eagle Square', 'type' => 'mixed'],
                ['name' => 'Shehu Shagari Way', 'type' => 'commercial']
            ]],
            ['city' => 'Garki', 'areas' => [
                ['name' => 'Garki 1', 'type' => 'mixed'],
                ['name' => 'Garki 2', 'type' => 'mixed'],
                ['name' => 'Area 1', 'type' => 'residential'],
                ['name' => 'Area 2', 'type' => 'residential'],
                ['name' => 'Area 3', 'type' => 'residential'],
                ['name' => 'Area 7', 'type' => 'residential'],
                ['name' => 'Area 8', 'type' => 'residential'],
                ['name' => 'Area 10', 'type' => 'residential'],
                ['name' => 'Area 11', 'type' => 'residential']
            ]],
            ['city' => 'Wuse', 'areas' => [
                ['name' => 'Wuse 1', 'type' => 'mixed'],
                ['name' => 'Wuse 2', 'type' => 'commercial'],
                ['name' => 'Wuse Zone 3', 'type' => 'mixed'],
                ['name' => 'Wuse Zone 4', 'type' => 'residential'],
                ['name' => 'Wuse Zone 5', 'type' => 'residential'],
                ['name' => 'Wuse Zone 6', 'type' => 'residential'],
                ['name' => 'Aminu Kano Crescent', 'type' => 'commercial']
            ]],
            ['city' => 'Maitama', 'areas' => [
                ['name' => 'Maitama District', 'type' => 'residential'],
                ['name' => 'Maitama Extension', 'type' => 'residential'],
                ['name' => 'Diplomatic Zone', 'type' => 'mixed'],
                ['name' => 'IBB Way', 'type' => 'mixed']
            ]],
            ['city' => 'Asokoro', 'areas' => [
                ['name' => 'Asokoro District', 'type' => 'residential'],
                ['name' => 'Asokoro Extension', 'type' => 'residential'],
                ['name' => 'Villa Area', 'type' => 'government']
            ]],
            ['city' => 'Gwarinpa', 'areas' => [
                ['name' => 'Gwarinpa 1st Avenue', 'type' => 'residential'],
                ['name' => 'Gwarinpa 2nd Avenue', 'type' => 'residential'],
                ['name' => 'Gwarinpa 3rd Avenue', 'type' => 'residential'],
                ['name' => 'Gwarinpa Estate', 'type' => 'residential'],
                ['name' => 'Life Camp', 'type' => 'residential']
            ]],
            ['city' => 'Kubwa', 'areas' => [
                ['name' => 'Kubwa Main', 'type' => 'residential'],
                ['name' => 'Kubwa Extension', 'type' => 'residential'],
                ['name' => 'Arab Road', 'type' => 'mixed'],
                ['name' => 'Byazhin', 'type' => 'residential']
            ]],
            ['city' => 'Lugbe', 'areas' => [
                ['name' => 'Lugbe Main', 'type' => 'residential'],
                ['name' => 'Lugbe Extension', 'type' => 'residential'],
                ['name' => 'Airport Road', 'type' => 'mixed'],
                ['name' => 'Trademore Estate', 'type' => 'residential']
            ]],

            // Rivers State (Port Harcourt) Areas - Comprehensive coverage
            ['city' => 'Port Harcourt', 'areas' => [
                ['name' => 'GRA Phase 1', 'type' => 'residential'],
                ['name' => 'GRA Phase 2', 'type' => 'residential'],
                ['name' => 'GRA Phase 3', 'type' => 'residential'],
                ['name' => 'Old GRA', 'type' => 'residential'],
                ['name' => 'New GRA', 'type' => 'residential'],
                ['name' => 'D-Line', 'type' => 'mixed'],
                ['name' => 'Mile 1', 'type' => 'commercial'],
                ['name' => 'Mile 2', 'type' => 'mixed'],
                ['name' => 'Mile 3', 'type' => 'mixed'],
                ['name' => 'Mile 4', 'type' => 'mixed'],
                ['name' => 'Trans Amadi', 'type' => 'industrial'],
                ['name' => 'Elekahia', 'type' => 'residential'],
                ['name' => 'Woji', 'type' => 'residential'],
                ['name' => 'Ada George', 'type' => 'mixed'],
                ['name' => 'Rumuola', 'type' => 'mixed']
            ]],
            ['city' => 'Obio-Akpor', 'areas' => [
                ['name' => 'Rumuigbo', 'type' => 'mixed'],
                ['name' => 'Rumukrushi', 'type' => 'residential'],
                ['name' => 'Rumuokoro', 'type' => 'mixed'],
                ['name' => 'Choba', 'type' => 'mixed'],
                ['name' => 'Shell Location', 'type' => 'residential']
            ]],

            // Kano State Areas - Comprehensive coverage
            ['city' => 'Kano Metropolitan', 'areas' => [
                ['name' => 'Sabon Gari', 'type' => 'mixed'],
                ['name' => 'Bompai', 'type' => 'residential'],
                ['name' => 'GRA Kano', 'type' => 'residential'],
                ['name' => 'Kurna', 'type' => 'commercial'],
                ['name' => 'Fagge', 'type' => 'mixed'],
                ['name' => 'Dala', 'type' => 'residential'],
                ['name' => 'Gwale', 'type' => 'mixed'],
                ['name' => 'Kano Municipal', 'type' => 'mixed'],
                ['name' => 'Nassarawa', 'type' => 'mixed'],
                ['name' => 'Tarauni', 'type' => 'residential']
            ]],

            // Oyo State (Ibadan) Areas - Comprehensive coverage
            ['city' => 'Ibadan North', 'areas' => [
                ['name' => 'Bodija', 'type' => 'residential'],
                ['name' => 'University of Ibadan', 'type' => 'mixed'],
                ['name' => 'Sango', 'type' => 'mixed'],
                ['name' => 'Mokola', 'type' => 'mixed'],
                ['name' => 'Agodi', 'type' => 'mixed'],
                ['name' => 'Dugbe', 'type' => 'commercial'],
                ['name' => 'Oke Ado', 'type' => 'mixed']
            ]],
            ['city' => 'Ibadan South-West', 'areas' => [
                ['name' => 'Ring Road', 'type' => 'commercial'],
                ['name' => 'Orita Challenge', 'type' => 'mixed'],
                ['name' => 'Oke Ado South-West', 'type' => 'mixed'],
                ['name' => 'Liberty Road', 'type' => 'mixed']
            ]],
            ['city' => 'Ibadan North-East', 'areas' => [
                ['name' => 'Iwo Road', 'type' => 'commercial'],
                ['name' => 'New Bodija', 'type' => 'residential'],
                ['name' => 'Polytechnic', 'type' => 'mixed'],
                ['name' => 'Eleyele', 'type' => 'mixed']
            ]],

            // Kaduna State Areas
            ['city' => 'Kaduna North', 'areas' => [
                ['name' => 'GRA Kaduna', 'type' => 'residential'],
                ['name' => 'Sabon Gari', 'type' => 'mixed'],
                ['name' => 'Tudun Wada', 'type' => 'residential'],
                ['name' => 'Ungwan Rimi', 'type' => 'residential'],
                ['name' => 'Kaduna Central', 'type' => 'commercial']
            ]],
            ['city' => 'Kaduna South', 'areas' => [
                ['name' => 'Television', 'type' => 'mixed'],
                ['name' => 'Barnawa', 'type' => 'mixed'],
                ['name' => 'Kakuri', 'type' => 'mixed'],
                ['name' => 'Malali', 'type' => 'residential']
            ]],
            ['city' => 'Zaria', 'areas' => [
                ['name' => 'Zaria City', 'type' => 'mixed'],
                ['name' => 'Samaru', 'type' => 'mixed'],
                ['name' => 'ABU Campus', 'type' => 'mixed'],
                ['name' => 'Sabon Gari Zaria', 'type' => 'mixed']
            ]],

            // Anambra State Areas
            ['city' => 'Awka', 'areas' => [
                ['name' => 'Awka Central', 'type' => 'mixed'],
                ['name' => 'UNIZIK', 'type' => 'mixed'],
                ['name' => 'Nnamdi Azikiwe University', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government']
            ]],
            ['city' => 'Onitsha', 'areas' => [
                ['name' => 'Main Market', 'type' => 'commercial'],
                ['name' => 'Bridge Head', 'type' => 'commercial'],
                ['name' => 'Upper Iweka', 'type' => 'mixed'],
                ['name' => 'GRA Onitsha', 'type' => 'residential']
            ]],
            ['city' => 'Nnewi', 'areas' => [
                ['name' => 'Nnewi North', 'type' => 'mixed'],
                ['name' => 'Nnewi South', 'type' => 'mixed'],
                ['name' => 'Industrial Area', 'type' => 'industrial']
            ]],

            // Enugu State Areas
            ['city' => 'Enugu East', 'areas' => [
                ['name' => 'GRA Enugu', 'type' => 'residential'],
                ['name' => 'Independence Layout', 'type' => 'residential'],
                ['name' => 'Trans Ekulu', 'type' => 'residential'],
                ['name' => 'Coal Camp', 'type' => 'mixed'],
                ['name' => 'Achara Layout', 'type' => 'residential'],
                ['name' => 'New Haven', 'type' => 'residential']
            ]],
            ['city' => 'Enugu North', 'areas' => [
                ['name' => 'Ogbete', 'type' => 'commercial'],
                ['name' => 'Mayor Market', 'type' => 'commercial'],
                ['name' => 'Zik Avenue', 'type' => 'commercial']
            ]],
            ['city' => 'Enugu South', 'areas' => [
                ['name' => 'Uwani', 'type' => 'mixed'],
                ['name' => 'Maryland', 'type' => 'residential'],
                ['name' => 'Asata', 'type' => 'mixed']
            ]],

            // Cross River State Areas
            ['city' => 'Calabar Municipal', 'areas' => [
                ['name' => 'Calabar Central', 'type' => 'mixed'],
                ['name' => 'Mary Slessor', 'type' => 'mixed'],
                ['name' => 'Watt Market', 'type' => 'commercial'],
                ['name' => 'State Housing', 'type' => 'residential']
            ]],
            ['city' => 'Calabar South', 'areas' => [
                ['name' => 'Ikot Ansa', 'type' => 'mixed'],
                ['name' => 'Goldie', 'type' => 'mixed'],
                ['name' => 'Parliamentary Extension', 'type' => 'residential']
            ]],

            // MAIDUGURI - BORNO STATE CAPITAL - COMPREHENSIVE AREAS
            ['city' => 'Maiduguri', 'areas' => [
                ['name' => 'GRA Maiduguri', 'type' => 'residential'],
                ['name' => 'Government House Area', 'type' => 'government'],
                ['name' => 'Shehu Laminu Way', 'type' => 'commercial'],
                ['name' => 'Baga Road', 'type' => 'mixed'],
                ['name' => 'Pompomari', 'type' => 'mixed'],
                ['name' => 'Gamboru Market', 'type' => 'commercial'],
                ['name' => 'Post Office Area', 'type' => 'commercial'],
                ['name' => 'Monday Market', 'type' => 'commercial'],
                ['name' => 'Mairi', 'type' => 'residential'],
                ['name' => 'Gwange', 'type' => 'residential'],
                ['name' => 'Bulumkutu', 'type' => 'mixed'],
                ['name' => 'Shehuri North', 'type' => 'residential'],
                ['name' => 'Shehuri South', 'type' => 'residential'],
                ['name' => 'Lamisula', 'type' => 'residential'],
                ['name' => 'University of Maiduguri', 'type' => 'mixed'],
                ['name' => 'Railway Quarters', 'type' => 'residential'],
                ['name' => 'New Maiduguri', 'type' => 'residential'],
                ['name' => 'Old Maiduguri', 'type' => 'mixed'],
                ['name' => 'Customs Area', 'type' => 'mixed'],
                ['name' => 'Federal Low Cost', 'type' => 'residential']
            ]],

            // YOLA - ADAMAWA STATE CAPITAL
            ['city' => 'Yola North', 'areas' => [
                ['name' => 'GRA Yola', 'type' => 'residential'],
                ['name' => 'Jimeta', 'type' => 'commercial'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Karewa', 'type' => 'mixed'],
                ['name' => 'Doubeli', 'type' => 'residential'],
                ['name' => 'Demsawo', 'type' => 'mixed'],
                ['name' => 'Jambutu', 'type' => 'commercial'],
                ['name' => 'Yolde-Pate', 'type' => 'residential']
            ]],

            // BAUCHI - BAUCHI STATE CAPITAL
            ['city' => 'Bauchi', 'areas' => [
                ['name' => 'GRA Bauchi', 'type' => 'residential'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Emir\'s Palace Area', 'type' => 'mixed'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Yelwa', 'type' => 'residential'],
                ['name' => 'Dass Road', 'type' => 'mixed'],
                ['name' => 'Wunti', 'type' => 'residential'],
                ['name' => 'Nasarawa Quarters', 'type' => 'residential']
            ]],

            // YENAGOA - BAYELSA STATE CAPITAL
            ['city' => 'Yenagoa', 'areas' => [
                ['name' => 'Amarata', 'type' => 'residential'],
                ['name' => 'Epie', 'type' => 'mixed'],
                ['name' => 'Ovom', 'type' => 'residential'],
                ['name' => 'Opolo', 'type' => 'mixed'],
                ['name' => 'Tombia', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Niger Delta University', 'type' => 'mixed'],
                ['name' => 'Swali Market', 'type' => 'commercial']
            ]],

            // MAKURDI - BENUE STATE CAPITAL
            ['city' => 'Makurdi', 'areas' => [
                ['name' => 'GRA Makurdi', 'type' => 'residential'],
                ['name' => 'High Level', 'type' => 'residential'],
                ['name' => 'Low Level', 'type' => 'mixed'],
                ['name' => 'Wurukum', 'type' => 'commercial'],
                ['name' => 'North Bank', 'type' => 'mixed'],
                ['name' => 'Modern Market', 'type' => 'commercial'],
                ['name' => 'University of Agriculture', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government']
            ]],

            // ABAKALIKI - EBONYI STATE CAPITAL
            ['city' => 'Abakaliki', 'areas' => [
                ['name' => 'GRA Abakaliki', 'type' => 'residential'],
                ['name' => 'Kpirikpiri', 'type' => 'mixed'],
                ['name' => 'Waterworks', 'type' => 'residential'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'EBSU Campus', 'type' => 'mixed'],
                ['name' => 'Ezza Road', 'type' => 'commercial'],
                ['name' => 'Ogoja Road', 'type' => 'mixed'],
                ['name' => 'Mile 50', 'type' => 'mixed']
            ]],

            // ADO-EKITI - EKITI STATE CAPITAL
            ['city' => 'Ado-Ekiti', 'areas' => [
                ['name' => 'GRA Ado-Ekiti', 'type' => 'residential'],
                ['name' => 'Oke-Ila', 'type' => 'mixed'],
                ['name' => 'Ajilosun', 'type' => 'residential'],
                ['name' => 'Adebayo', 'type' => 'mixed'],
                ['name' => 'EKSU Campus', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Basiri', 'type' => 'residential'],
                ['name' => 'Dalimore', 'type' => 'residential']
            ]],

            // GOMBE - GOMBE STATE CAPITAL
            ['city' => 'Gombe', 'areas' => [
                ['name' => 'GRA Gombe', 'type' => 'residential'],
                ['name' => 'Pantami', 'type' => 'mixed'],
                ['name' => 'Tudun Wada', 'type' => 'residential'],
                ['name' => 'Bolari', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Federal University', 'type' => 'mixed'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Nasarawo', 'type' => 'residential']
            ]],

            // DUTSE - JIGAWA STATE CAPITAL
            ['city' => 'Dutse', 'areas' => [
                ['name' => 'GRA Dutse', 'type' => 'residential'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Takur', 'type' => 'mixed'],
                ['name' => 'Chamo', 'type' => 'residential'],
                ['name' => 'Fagam', 'type' => 'mixed'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Federal University Dutse', 'type' => 'mixed']
            ]],

            // BIRNIN KEBBI - KEBBI STATE CAPITAL
            ['city' => 'Birnin Kebbi', 'areas' => [
                ['name' => 'GRA Birnin Kebbi', 'type' => 'residential'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Emir\'s Palace', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Zauro', 'type' => 'residential'],
                ['name' => 'Makera', 'type' => 'mixed'],
                ['name' => 'Federal University Birnin Kebbi', 'type' => 'mixed']
            ]],

            // LOKOJA - KOGI STATE CAPITAL
            ['city' => 'Lokoja', 'areas' => [
                ['name' => 'GRA Lokoja', 'type' => 'residential'],
                ['name' => 'Phase 1', 'type' => 'residential'],
                ['name' => 'Phase 2', 'type' => 'residential'],
                ['name' => 'Ganaja', 'type' => 'mixed'],
                ['name' => 'Felele', 'type' => 'residential'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Kogi State University', 'type' => 'mixed'],
                ['name' => 'Old Market', 'type' => 'commercial']
            ]],

            // KATSINA - KATSINA STATE CAPITAL
            ['city' => 'Katsina', 'areas' => [
                ['name' => 'GRA Katsina', 'type' => 'residential'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Emir\'s Palace', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Kofar Marusa', 'type' => 'mixed'],
                ['name' => 'Kofar Sauri', 'type' => 'residential'],
                ['name' => 'Federal University Dutsinma', 'type' => 'mixed']
            ]],

            // ILORIN - KWARA STATE CAPITAL
            ['city' => 'Ilorin', 'areas' => [
                ['name' => 'GRA Ilorin', 'type' => 'residential'],
                ['name' => 'Tanke', 'type' => 'mixed'],
                ['name' => 'Fate Road', 'type' => 'mixed'],
                ['name' => 'Post Office Area', 'type' => 'commercial'],
                ['name' => 'Oja Oba', 'type' => 'commercial'],
                ['name' => 'University of Ilorin', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Sango', 'type' => 'mixed']
            ]],

            // LAFIA - NASARAWA STATE CAPITAL
            ['city' => 'Lafia', 'areas' => [
                ['name' => 'GRA Lafia', 'type' => 'residential'],
                ['name' => 'Tudun Gwandara', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Federal University Lafia', 'type' => 'mixed'],
                ['name' => 'Shabu', 'type' => 'residential'],
                ['name' => 'Bukan Sidi', 'type' => 'mixed']
            ]],

            // MINNA - NIGER STATE CAPITAL
            ['city' => 'Minna', 'areas' => [
                ['name' => 'GRA Minna', 'type' => 'residential'],
                ['name' => 'Bosso Estate', 'type' => 'residential'],
                ['name' => 'Tudun Fulani', 'type' => 'mixed'],
                ['name' => 'Sauka Kahuta', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Federal University of Technology', 'type' => 'mixed'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Kpakungu', 'type' => 'residential']
            ]],

            // AKURE - ONDO STATE CAPITAL
            ['city' => 'Akure', 'areas' => [
                ['name' => 'GRA Akure', 'type' => 'residential'],
                ['name' => 'Alagbaka', 'type' => 'mixed'],
                ['name' => 'Oba Ile', 'type' => 'mixed'],
                ['name' => 'FUTA South Gate', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Federal University of Technology', 'type' => 'mixed'],
                ['name' => 'Oke-Aro', 'type' => 'residential'],
                ['name' => 'Igoba', 'type' => 'mixed']
            ]],

            // OSOGBO - OSUN STATE CAPITAL
            ['city' => 'Osogbo', 'areas' => [
                ['name' => 'GRA Osogbo', 'type' => 'residential'],
                ['name' => 'Oke-Fia', 'type' => 'mixed'],
                ['name' => 'Station Road', 'type' => 'commercial'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Osun State University', 'type' => 'mixed'],
                ['name' => 'Oja Oba', 'type' => 'commercial'],
                ['name' => 'Ayetoro', 'type' => 'residential'],
                ['name' => 'Old Garage', 'type' => 'mixed']
            ]],

            // SOKOTO - SOKOTO STATE CAPITAL
            ['city' => 'Sokoto North', 'areas' => [
                ['name' => 'GRA Sokoto', 'type' => 'residential'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Sultan\'s Palace', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Usmanu Danfodiyo University', 'type' => 'mixed'],
                ['name' => 'Runjin Sambo', 'type' => 'residential'],
                ['name' => 'Arkilla', 'type' => 'mixed']
            ]],

            // JALINGO - TARABA STATE CAPITAL
            ['city' => 'Jalingo', 'areas' => [
                ['name' => 'GRA Jalingo', 'type' => 'residential'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Federal University Wukari', 'type' => 'mixed'],
                ['name' => 'Hammaruwa', 'type' => 'mixed'],
                ['name' => 'Barade', 'type' => 'residential'],
                ['name' => 'Sarkin Dawaki', 'type' => 'mixed']
            ]],

            // DAMATURU - YOBE STATE CAPITAL
            ['city' => 'Damaturu', 'areas' => [
                ['name' => 'GRA Damaturu', 'type' => 'residential'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Federal University Gashua', 'type' => 'mixed'],
                ['name' => 'Bindigari', 'type' => 'mixed'],
                ['name' => 'Sabon Gari', 'type' => 'residential'],
                ['name' => 'Pawari', 'type' => 'residential']
            ]],

            // GUSAU - ZAMFARA STATE CAPITAL
            ['city' => 'Gusau', 'areas' => [
                ['name' => 'GRA Gusau', 'type' => 'residential'],
                ['name' => 'Central Market', 'type' => 'commercial'],
                ['name' => 'Emir\'s Palace', 'type' => 'mixed'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Federal University Gusau', 'type' => 'mixed'],
                ['name' => 'Sabon Gari', 'type' => 'residential'],
                ['name' => 'Tudun Wada', 'type' => 'mixed']
            ]],

            // Additional State Capitals
            ['city' => 'Uyo', 'areas' => [
                ['name' => 'Uyo Central', 'type' => 'mixed'],
                ['name' => 'Wellington Bassey Way', 'type' => 'commercial'],
                ['name' => 'Government House', 'type' => 'government'],
                ['name' => 'Ibom Plaza', 'type' => 'commercial']
            ]],
            ['city' => 'Abeokuta North', 'areas' => [
                ['name' => 'Oke Mosan', 'type' => 'government'],
                ['name' => 'Abeokuta Central', 'type' => 'mixed'],
                ['name' => 'Lafenwa', 'type' => 'mixed'],
                ['name' => 'Isabo', 'type' => 'mixed']
            ]],
            ['city' => 'Benin City', 'areas' => [
                ['name' => 'GRA Benin', 'type' => 'residential'],
                ['name' => 'Ring Road', 'type' => 'commercial'],
                ['name' => 'New Benin', 'type' => 'mixed'],
                ['name' => 'Uselu', 'type' => 'mixed'],
                ['name' => 'UNIBEN', 'type' => 'mixed']
            ]],
            ['city' => 'Jos North', 'areas' => [
                ['name' => 'Jos Central', 'type' => 'mixed'],
                ['name' => 'Rayfield', 'type' => 'residential'],
                ['name' => 'Rantya', 'type' => 'mixed'],
                ['name' => 'UNIJOS', 'type' => 'mixed']
            ]],
            ['city' => 'Warri South', 'areas' => [
                ['name' => 'Warri Central', 'type' => 'mixed'],
                ['name' => 'Effurun', 'type' => 'mixed'],
                ['name' => 'GRA Warri', 'type' => 'residential'],
                ['name' => 'Ekpan', 'type' => 'industrial']
            ]],
            ['city' => 'Asaba', 'areas' => [
                ['name' => 'Asaba Central', 'type' => 'mixed'],
                ['name' => 'GRA Asaba', 'type' => 'residential'],
                ['name' => 'Cable Point', 'type' => 'mixed'],
                ['name' => 'West End', 'type' => 'residential']
            ]],
            ['city' => 'Owerri Municipal', 'areas' => [
                ['name' => 'Owerri Central', 'type' => 'mixed'],
                ['name' => 'GRA Owerri', 'type' => 'residential'],
                ['name' => 'World Bank', 'type' => 'residential'],
                ['name' => 'IMSU', 'type' => 'mixed']
            ]]
        ];

        foreach ($areas as $cityAreas) {
            $city = City::where('name', $cityAreas['city'])->first();
            if ($city) {
                foreach ($cityAreas['areas'] as $areaData) {
                    // Check if area name already exists, if so make it unique
                    $baseName = $areaData['name'];
                    $areaName = $baseName;
                    $counter = 1;
                    
                    while (Area::where('name', $areaName)->exists()) {
                        $areaName = $baseName . ' (' . $city->name . ')';
                        if (Area::where('name', $areaName)->exists()) {
                            $areaName = $baseName . ' (' . $city->name . ' ' . $counter . ')';
                            $counter++;
                        } else {
                            break;
                        }
                    }
                    
                    Area::create([
                        'name' => $areaName,
                        'city_id' => $city->id,
                        'type' => $areaData['type'],
                        'is_active' => true,
                        'sort_order' => 0,
                        'amenities' => $this->getRandomAmenities()
                    ]);
                }
            }
        }

        $this->command->info('Areas seeded successfully.');
    }

    private function getRandomAmenities(): array
    {
        $availableAmenities = [
            'schools', 'hospitals', 'markets', 'banks', 'restaurants',
            'parks', 'transport', 'fuel_stations', 'police', 'pharmacy'
        ];

        // Return 3-6 random amenities
        return array_slice($availableAmenities, 0, rand(3, 6));
    }
}
