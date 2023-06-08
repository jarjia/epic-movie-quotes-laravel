<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MovieController extends Controller
{
    public function store(Request $request)
    {
        $file = request()->file('thumbnail')->store('images', 'public');
        $thumbnailPath = config('app.url') . ':8000/storage/' . $file;

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
