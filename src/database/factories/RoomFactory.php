<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TypeRoom;
use App\Models\Hotel;
use App\Models\Service;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected static $roomNumber = 100;
    public function definition(): array
    {
        $number = self::$roomNumber;
        self::$roomNumber++;

        if (self::$roomNumber > 120 && self::$roomNumber % 100 == 21) {
            self::$roomNumber += 80;
        }

        // Configurar Faker en español
        $faker = \Faker\Factory::create('es_ES');


        return [
            'type_id' => TypeRoom::factory(),
            'name' => $faker->name,
            'number' => $number,
            'extra_bed' => random_int(0, 2),
            'state' => $faker->randomElement(['reservada', 'lliure', 'ocupada']),
            'hotel_id' => Hotel::factory()
        ];
    }
}
