<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypeRoom;
use App\Models\Service;

class TypeRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipusHabitacio = [
            [
                'name' => 'Individual',
                'description' => 'Habitación individual',
                'price' => 50,
                'bed' => 1,
            ],
            [
                'name' => 'Doble',
                'description' => 'Habitación doble',
                'price' => 80,
                'bed' => 2,
            ],
            [
                'name' => 'Triple',
                'description' => 'Habitación triple',
                'price' => 100,
                'bed' => 3,
            ],
            [
                'name' => 'Suite',
                'description' => 'Habitación suite',
                'price' => 150,
                'bed' => 4,
            ],
            [
                'name' => 'Familiar',
                'description' => 'Habitación familiar',
                'price' => 200,
                'bed' => 5,
            ],
        ];

        // Obtener todos los servicios disponibles
        $services = Service::all();

        foreach ($tipusHabitacio as $tipus) {
            // Crear o actualizar el tipo de habitación
            $typeRoom = TypeRoom::updateOrCreate(
                ['name' => $tipus['name']],
                [
                    'description' => $tipus['description'],
                    'price' => $tipus['price'],
                    'bed' => $tipus['bed'],
                    'images' => json_encode($this->getRandomImages()),
                ]
            );

            // Asociar servicios aleatorios al tipo de habitación
            $numServices = rand(1, 3); // Número aleatorio de servicios a asociar
            $typeRoom->services()->attach($services->random($numServices)->pluck('id'));
        }
    }
    private function getRandomImages(): array
    {
        $numImages = rand(3, 4);
        $images = [];

        for ($i = 0; $i < $numImages; $i++) {
            $images[] = "https://picsum.photos/800/600?=" . rand(1, 1000);
        }

        return $images;
    }
}
