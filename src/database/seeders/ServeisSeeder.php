<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServeisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $numServeis = max((int) $this->command->ask('Introdueix la quantitat de serveis', 5), 5);
        // Service::factory($numServeis)->create();

        $serveis = [
            [
                'name' => 'Wifi',
                'description' => 'Internet sense fils',
                'price' => 20,
            ],
            [
                'name' => 'Microones',
                'description' => 'Microones a les habitacions',
                'price' => 5,
            ],
            [
                'name' => 'Aire condicionat',
                'description' => 'Aire condicionat a les habitacions',
                'price' => 10,
            ],
            [
                'name' => 'Televisió',
                'description' => 'Televisió 65 pulgades',
                'price' => 10,
            ],
            [
                'name' => 'Banyera',
                'description' => 'Banyera a les habitacions',
                'price' => 15,
            ],
        ];

        foreach ($serveis as $servei) {
            Service::updateOrCreate(
                [
                    'name' => $servei['name'],
                    'description' => $servei['description'],
                ],
                [
                    'price' => $servei['price'],
                ]
            );
        }
    }
}
