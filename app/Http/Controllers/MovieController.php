<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function getGenres()
    {
        return Genre::select('id', 'genre')->get();
    }

    public function store(Request $request)
    {
        $fileName = request()->file('thumbnail')->getClientOriginalName();
        $filePath = request()->file('thumbnail')->storeAs('images', $fileName, 'public');
        $thumbnailPath = config('app.url') . ':8000/storage/' . $filePath;

        $movie = Movie::create([
            'movie' => $request->movie,
            'director' => $request->director,
            'description' => $request->description,
            'releaseDate' => $request->releaseDate,
            'thumbnail' => $thumbnailPath,
            'user_id' => $request->user_id
        ]);

        $genres = $request->genres;

        $movie->genres()->sync($genres);

        return response($movie, 201);
    }

    public function fetch(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $movies = Movie::whereIn('user_id', [auth()->user()->id])->where('movie->' . app()->getLocale(), 'like', '%' . $search . '%')->get();

        return response()->json($movies);
    }
}
