<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequests\StoreQuoteRequest;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function getQuotesForMovie(Request $request): JsonResponse
    {
        App::setLocale($request->locale);
        $quotes = Quote::with('comments.user', 'likes.user')
            ->whereIn('movie_id', [intval($request->movieId)])
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

        App::setLocale($request->locale);

        $file = request()->file('thumbnail')->store('images', 'public');

        $quote = Quote::create([
            'quote' => $attributes['quote'],
            'movie_id' => $attributes['movieId'],
            'thumbnail' => $file
        ]);

        return response()->json($quote);
    }

    public function getQuote(Request $request): JsonResponse
    {
        App::setLocale($request->locale);

        $quote = Quote::with('comments.user', 'likes.user', 'movies.user')->firstWhere('id', intval($request->quoteId));
        $quote->thumbnail = asset('storage/' . $quote->thumbnail);
        $quote['user_id'] = $quote->movies->user_id;
        foreach ($quote->comments as $comment) {
            if (Str::startsWith($comment->user->thumbnail, 'assets')) {
                $comment->user->thumbnail = asset($comment->user->thumbnail);
            } else if (Str::startsWith($comment->user->thumbnail, 'images')) {
                $comment->user->thumbnail = asset('storage/' . $comment->user->thumbnail);
            }
        }

        return response()->json($quote);
    }

    public function update(Quote $quote, Request $request): Response
    {
        $attributes = [
            'quote' => $request->quote,
        ];

        if ($request->hasFile('thumbnail')) {
            $file = request()->file('thumbnail')->store('images', 'public');
            $attributes['thumbnail'] = $file;
        }

        $quote->update($attributes);

        return response('Movie was updated!');
    }

    public function all(Request $request): JsonResponse
    {
        App::setLocale($request->locale);
        $quotes = Quote::searchQuotes($request->search, $request->paginate);

        foreach ($quotes as $quote) {
            $userThumbnail = '';
            if (Str::startsWith($quote->movies->user->thumbnail, 'assets')) {
                $userThumbnail = asset($quote->movies->user->thumbnail);
            } else {
                $userThumbnail = asset('storage/' . $quote->movies->user->thumbnail);
            }
            $quote['thumbnail'] = asset('storage/' . $quote->thumbnail);
            $quote->movies->thumbnail = Str::startsWith($quote->movies->thumbnail, 'http') ?
                $quote->movies->thumbnail : asset('storage/' . $quote->movies->thumbnail);
            $quote->movies->user->thumbnail = $userThumbnail;
            foreach ($quote->comments as $comment) {
                if (Str::startsWith($comment->user->thumbnail, 'assets')) {
                    $comment->user->thumbnail = asset($comment->user->thumbnail);
                } else if (Str::startsWith($comment->user->thumbnail, 'images')) {
                    $comment->user->thumbnail = asset('storage/' . $comment->user->thumbnail);
                }
            }
        };

        return response()->json(['quotes' => $quotes, 'last_page' => Quote::all()->count(), 'current_page' => $request->paginate]);
    }

    public function destroy(Quote $quote, Request $request): Response
    {
        App::setLocale($request->locale);

        $quote->delete();

        return response('Quote deleted', 200);
    }
}
