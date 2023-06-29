<?php

namespace Database\Factories;

use App\Models\Movie;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
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
            'movie_id' => Movie::factory(),
            'quote' => ['en' => fake()->word(), 'ka' => $fakerKa->realText(10)],
            'thumbnail' => 'assets/user.png'
        ];
    }
}
