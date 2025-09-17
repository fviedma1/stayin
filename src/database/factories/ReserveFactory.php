<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Room;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reserve>
 */
class ReserveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('es_ES');

        return [
            'room_id' => Room::factory(),
            'user_id' => User::factory(),
            'people' => random_int(1, 4),
            'notes' => $faker->text,
            'date_in' => $this->faker->date(),
            'date_out' => $this->faker->date(),
            'check_in' => $this->faker->date(),
            'check_out' => $this->faker->date(),
            'status' => $this->faker->randomElement(['pendent', 'checkin']),
            'price' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
