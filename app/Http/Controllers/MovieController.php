<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequests\AllMovieRequest;
use App\Http\Requests\MovieRequests\ShowMovieRequest;
use App\Http\Requests\MovieRequests\StoreMovieRequest;
use App\Http\Requests\MovieRequests\UpdateMovieRequest;
use App\Http\Resources\MovieResource;
use App\Http\Resources\ShowMovieResource;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
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

    public function index(AllMovieRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        App::setLocale($attributes['locale']);
        $movies = Movie::with('quotes')->whereIn('user_id', [auth()->user()->id])
            ->where('movie->' . app()->getLocale(), 'like', '%' . $attributes['search'] . '%')
            ->select('id', 'movie', 'thumbnail', 'releaseDate')
            ->get();

        $transformedMovies = new MovieResource($movies);

        return response()->json(['movies' => $transformedMovies]);
    }

    public function show(string | int $id, ShowMovieRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        App::setLocale($attributes['locale']);
        $movie = Movie::whereIn('user_id', [auth()->user()->id])
            ->with(['genres', 'quotes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'quotes.comments', 'quotes.likes'])
            ->find($id);

        if ($movie !== null) {
            $transforemedMovies = new ShowMovieResource($movie);

            return response()->json($transforemedMovies);
        }

        return response()->json('Movie does not exist', 404);
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
