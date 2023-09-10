<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequests\AllQuotesRequest;
use App\Http\Requests\QuoteRequests\GetQuotesForMovieRequest;
use App\Http\Requests\QuoteRequests\StoreQuoteRequest;
use App\Http\Resources\AllQuoteResource;
use App\Http\Resources\ShowQuoteResource;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class QuoteController extends Controller
{
    public function getQuotesForMovie(GetQuotesForMovieRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        App::setLocale($attributes['locale']);
        $quotes = Quote::with('comments.user', 'likes.user')
            ->whereIn('movie_id', [intval($attributes['movieId'])])
            ->select('quote', 'thumbnail', 'id', 'movie_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $quotes->each(function ($quote) {
            $imageUrl = asset('storage/' . $quote->thumbnail);
            $quote->thumbnail = $imageUrl;
        });

        return response()->json($quotes);
    }

    public function store(StoreQuoteRequest $request): JsonResponse
    {
        $attributes = $request->validated();

        App::setLocale($attributes['locale']);

        $file = request()->file('thumbnail')->store('images', 'public');

        $quote = Quote::create([
            'quote' => $attributes['quote'],
            'movie_id' => $attributes['movieId'],
            'thumbnail' => $file
        ]);

        return response()->json($quote);
    }

    public function show(int $quoteId): JsonResponse
    {
        $quote = Quote::with('comments.user', 'likes.user', 'movies.user')->firstWhere('id', intval($quoteId));

        $this->authorize('accessQuote', $quote);

        $transformedQuote = new ShowQuoteResource($quote);

        return response()->json($transformedQuote);
    }

    public function update(Quote $quote, Request $request): Response
    {
        $attributes = [
            'quote' => $request->quote,
        ];

        $this->authorize('accessQuote', $quote);

        if ($request->hasFile('thumbnail')) {
            $oldFile = str_replace('images/', '', $quote->thumbnail);

            Storage::disk('public')->delete('/images/'.$oldFile);

            $file = request()->file('thumbnail')->store('images', 'public');
            $attributes['thumbnail'] = $file;
        }

        $quote->update($attributes);

        return response('Movie was updated!');
    }

    public function index(AllQuotesRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        App::setLocale($attributes['locale']);
        $quotes = Quote::searchQuotes($attributes['search'], $attributes['paginate']);

        $transformedQuotes = new AllQuoteResource($quotes);

        return response()->json(['quotes' => $transformedQuotes, 'last_page' => Quote::all()->count(), 'current_page' => $request->paginate]);
    }

    public function destroy(Quote $quote): Response
    {
        $this->authorize('accessQuote', $quote);

        $oldFile = str_replace('images/', '', $quote->thumbnail);

        Storage::disk('public')->delete('/images/'.$oldFile);

        $quote->delete();

        return response('Quote deleted', 200);
    }
}
