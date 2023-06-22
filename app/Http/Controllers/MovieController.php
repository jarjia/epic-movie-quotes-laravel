<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequests\StoreMovieRequest;
use App\Http\Requests\MovieRequests\UpdateMovieRequest;
use App\Models\Movie;
use Dotenv\Util\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class MovieController extends Controller
{
    public function store(StoreMovieRequest $request): Response
    {
        $attributes = $request->validated();

        $file = request()->file('thumbnail')->store('images', 'public');

        App::setLocale($attributes['locale']);

        $movie = Movie::create([
            'movie' => $attributes['movie'],
            'director' => $attributes['director'],
            'description' => $attributes['description'],
            'releaseDate' => $attributes['releaseDate'],
            'thumbnail' => $file,
            'user_id' => $attributes['user_id']
        ]);

        $genres = $attributes['genres'];

        $movie->genres()->sync($genres);

        return response(__('response.movie_created'), 201);
    }

    public function fetch(Request $request): JsonResponse
    {
        App::setLocale($request->locale);
        $search = $request->input('search');
        $movies = Movie::with('quotes')->whereIn('user_id', [auth()->user()->id])
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

    public function update(Movie $movieId, UpdateMovieRequest $request): Response
    {
        $attributes = $request->validated();

        $data = [
            'movie' => $attributes['movie'],
            'director' => $attributes['director'],
            'description' => $attributes['description'],
            'releaseDate' => $attributes['releaseDate'],
        ];

        if ($request->hasFile('thumbnail')) {
            $file = request()->file('thumbnail')->store('images', 'public');
            $data['thumbnail'] = $file;
        }

        $movieId->update($data);

        $genres = $attributes['genres'];

        $movieId->genres()->detach();

        $genreIds = array_map('intval', $genres);

        $movieId->genres()->sync($genreIds, false);

        return response('Movie was updated!');
    }

    public function destroy(Movie $movie): Response
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
