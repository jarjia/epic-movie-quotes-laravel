<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenresController extends Controller
{
    public function getGenres()
    {
        return Genre::select('id', 'genre')->get();
    }
}
