<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fakerKa = FakerFactory::create('ka_GE');

        return [
            'user_id' => User::factory(),
            'movie' => ['en' => fake()->word(), 'ka' => $fakerKa->realText(10)],
            'director' => ['en' => fake()->word(), 'ka' => $fakerKa->realText(10)],
            'description' => ['en' => fake()->word(), 'ka' => $fakerKa->realText(10)],
            'releaseDate' => fake()->numberBetween(100, 9999),
            'thumbnail' => 'assets/user.png'
        ];
    }
}
