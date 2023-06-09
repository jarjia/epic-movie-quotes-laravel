<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Dotenv\Util\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MovieController extends Controller
{
    public function store(Request $request)
    {
        App::setLocale($request->locale);
        $file = request()->file('thumbnail')->store('images', 'public');

        $movie = Movie::create([
            'movie' => $request->movie,
            'director' => $request->director,
            'description' => $request->description,
            'releaseDate' => $request->releaseDate,
            'thumbnail' => $file,
            'user_id' => $request->user_id
        ]);

        $genres = $request->genres;

        $movie->genres()->sync($genres);

        return response(__('response.movie_created'), 201);
    }

    public function fetch(Request $request): JsonResponse
    {
        App::setLocale($request->locale);
        $search = $request->input('search');
        $movies = Movie::whereIn('user_id', [auth()->user()->id])
            ->where('movie->' . app()->getLocale(), 'like', '%' . $search . '%')
            ->get();

        $movies->each(function ($movie) {
            $imageUrl = asset('storage/' . $movie->thumbnail);
            $movie->thumbnail = $imageUrl;
        });

        return response()->json($movies);
    }
}
