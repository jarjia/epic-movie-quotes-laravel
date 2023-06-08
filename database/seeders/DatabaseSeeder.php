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
        $defaultGenres = [
            ['en' => 'Animation', 'ka' => 'ანიმაცია'],
            ['en' => 'Action', 'ka' => 'ექშენი'],
            ['en' => 'Romance', 'ka' => 'რომანტიკა'],
            ['en' => 'Horror', 'ka' => 'ჰორორი'],
            ['en' => 'Drama', 'ka' => 'დრამა'],
            ['en' => 'Thriller', 'ka' => 'თრილერი'],
            ['en' => 'Comedy', 'ka' => 'კომედია'],
            ['en' => 'Parody', 'ka' => 'პაროდია'],
            ['en' => 'Fantasy', 'ka' => 'ფენტეზი'],
            ['en' => 'Historical', 'ka' => 'ისტორიული'],
            ['en' => 'Western', 'ka' => 'ვესტერნი'],
            ['en' => 'musical', 'ka' => 'მიუზიკლი'],
            ['en' => 'Criminal', 'ka' => 'კრიმინალური'],
            ['en' => 'Documentary', 'ka' => 'დოკუმენტური'],
            ['en' => 'Adventure', 'ka' => 'სათავგადასავლო'],
        ];

        usort($defaultGenres, function ($a, $b) {
            $locale = app()->getLocale();
            return strcmp($a[$locale], $b[$locale]);
        });

        foreach ($defaultGenres as $genreName) {
            Genre::create(['genre' => $genreName]);
        }
    }
}
