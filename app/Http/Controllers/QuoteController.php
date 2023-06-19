<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
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

    public function getQuote(Request $request): JsonResponse
    {
        App::setLocale($request->locale);

        $quote = Quote::with('comments.user', 'likes.user', 'movies.user')->firstWhere('id', intval($request->quoteId));
        $quote->thumbnail = asset('storage/' . $quote->thumbnail);
        $quote['user_id'] = $quote->movies->user_id;

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
            $comments = Comment::whereIn('quote_id', [$quote->id])->with('user')
                ->orderBy('created_at', 'desc')->get();
            $userThumbnail = '';
            if (Str::startsWith($quote->movies->user->thumbnail, 'http')) {
                $userThumbnail = $quote->movies->user->thumbnail;
            } else {
                if ($quote->movies->user->thumbnail === null) {
                    $userThumbnail = null;
                } else {
                    $userThumbnail = asset('storage/' . $quote->movies->user->thumbnail);
                }
            }
            $quote['thumbnail'] = asset('storage/' . $quote->thumbnail);
            $quote->movies->thumbnail = Str::startsWith($quote->movies->thumbnail, 'http') ?
                $quote->movies->thumbnail : asset('storage/' . $quote->movies->thumbnail);
            $quote->movies->user->thumbnail = $userThumbnail;
            $quote->comments = $comments;
            foreach ($quote->likes as $like) {
                $like = $like->user;
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
