<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Country;

class CountryCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countriesAndCities = [
            'España' => ['Barcelona', 'Madrid', 'Valencia'],
            'Francia' => ['Paris', 'Lyon', 'Marseille'],
            'USA' => ['New York', 'Los Angeles', 'Chicago'],
        ];
        
        foreach ($countriesAndCities as $country => $cities) {
            // Crear o actualizar el país y obtener el ID
            $countryModel = Country::updateOrCreate(
                ['name' => $country],
                ['name' => $country]
            );

            foreach ($cities as $city) {
                // Crear o actualizar la ciudad asociándola al país
                City::updateOrCreate(
                    ['name' => $city, 'country_id' => $countryModel->id],
                    ['name' => $city, 'country_id' => $countryModel->id]
                );
            }
        }

        // Mensaje de confirmación
        $this->command->info('Països i ciutats inicialitzats amb èxit.');
    }
}
