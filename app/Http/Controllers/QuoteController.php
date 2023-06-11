<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class QuoteController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        App::setLocale($request->locale);
        $file = request()->file('thumbnail')->store('images', 'public');

        $quote = Quote::create([
            'quote' => $request->quote,
            'movie_id' => $request->movieId,
            'thumbnail' => $file
        ]);

        return response()->json($quote);
    }
}
