<?php

namespace Database\Factories;

use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // <-- Añadir esta línea

class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition()
    {
        return [
            'hotel_id' => \App\Models\Hotel::factory(),
            'user_id' => \App\Models\User::factory(),
            'comment' => $this->faker->paragraph,
            'stars' => $this->faker->numberBetween(1, 5),
            'images' => [
                $this->faker->imageUrl(),
                $this->faker->imageUrl()
            ],
            'token' => Str::uuid()->toString(), // <-- Usar la clase importada
            'used' => $this->faker->boolean(30)
        ];
    }
}