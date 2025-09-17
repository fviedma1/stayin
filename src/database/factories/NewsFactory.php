<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
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
            'title' => $faker->sentence(),
            'short_description' => $faker->paragraph(2),
            'long_description' => $faker->paragraphs(3, true),
            'published' => $faker->boolean(),
        ];
    }
}
