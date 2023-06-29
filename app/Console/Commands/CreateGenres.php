<?php

namespace App\Console\Commands;

use App\Models\Genre;
use Illuminate\Console\Command;

class CreateGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epic-movie-quotes:create-genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates genres for movies';

    /**
     * Execute the console command.
     */
    public function handle()
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
