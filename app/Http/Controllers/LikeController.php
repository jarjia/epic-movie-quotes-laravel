<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    public function store(Request $request): Response
    {
        $attributes = [
            'quote_id' => $request->quoteId,
            'user_id' => auth()->user()->id
        ];

        $like = Like::where('quote_id', $attributes['quote_id'])
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($like !== null) {
            $like->delete();

            return response('unliked');
        }

        Like::create($attributes);

        return response('liked');
    }
}
