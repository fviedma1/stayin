<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $numHotel = max ((int) $this->command->ask('Introdueix la quantitat d\'hotels', 1), 3);
        // Hotel::factory($numHotel)->create([
        //     'receptionist_id' => Usuari::where('name', 'recepcio')->value('name'), // Obtiene el valor del ID
        // ]);

        $hotels = [
            [
                'name' => 'Hotel Mil Maravillas',
                'address' => 'Carrer de la Marina, 19',
                'city' => 'Barcelona',
                'country' => 'España',
                'image' => 'https://media.disneylandparis.com/d4th/es-es/images/n019751_02_2027jul06_world_disneyland-hotel-pink-sky_16-9_tcm797-221019.jpg',
                'telephone' => '932 56 78 90',
                'email' => 'pep@gmail.com',
                'receptionist_id' => 3,
            ],
        ];

        foreach ($hotels as $hotel) {
            Hotel::updateOrCreate(
                [
                    'name' => $hotel['name'],
                    'address' => $hotel['address'],
                    'city' => $hotel['city'],
                    'country' => $hotel['country'],
                    'image' => $hotel['image'],
                    'telephone' => $hotel['telephone'],
                    'email' => $hotel['email'],
                    'receptionist_id' => $hotel['receptionist_id'],
                ]
            );
        }
    }
}
