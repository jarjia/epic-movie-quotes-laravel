<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\JsonResponse;

class GenresController extends Controller
{
    public function getGenres(): JsonResponse
    {
        return response()->json(Genre::select('id', 'genre')->get());
    }
}
