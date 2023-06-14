<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Dotenv\Util\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
            ->select('id', 'movie', 'thumbnail', 'releaseDate')
            ->get();

        $movies->each(function ($movie) {
            $imageUrl = asset('storage/' . $movie->thumbnail);
            $movie->thumbnail = $imageUrl;
        });

        return response()->json(['movies' => $movies]);
    }

    public function show(Request $request): JsonResponse
    {
        App::setLocale($request->locale);
        $movie = Movie::whereIn('user_id', [auth()->user()->id])
            ->with('genres')->find($request->id);

        if ($movie !== null) {
            $movie->thumbnail = asset('storage/' . $movie->thumbnail);

            return response()->json($movie);
        }

        return response()->json('Movie does not exist', 500);
    }

    public function update(Movie $movieId, Request $request)
    {
        $attributes = [
            'movie' => $request->movie,
            'director' => $request->director,
            'description' => $request->description,
            'releaseDate' => $request->releaseDate,
        ];

        if ($request->hasFile('thumbnail')) {
            $file = request()->file('thumbnail')->store('images', 'public');
            $attributes['thumbnail'] = $file;
        }

        $movieId->update($attributes);

        $genres = $request->genres;

        $movieId->genres()->detach();

        $genreIds = array_map('intval', $genres);

        $movieId->genres()->sync($genreIds, false);

        return response('Movie was updated!');
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();

        return response('Movie was deleted');
    }

    public function getMoviesForQuote(): JsonResponse
    {
        $movies = Movie::whereIn('user_id', [auth()->user()->id])
            ->select('id', 'movie')->get();

        return response()->json($movies);
    }
}
