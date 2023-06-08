<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultGenres = config('epicmoviequotes.genres');

        usort($defaultGenres, function ($a, $b) {
            $locale = app()->getLocale();
            return strcmp($a[$locale], $b[$locale]);
        });

        foreach ($defaultGenres as $genreName) {
            Genre::create(['genre' => $genreName]);
        }
    }
}
