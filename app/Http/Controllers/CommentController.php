<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequests\PostCommentRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function store(PostCommentRequest $request): Response
    {
        $attributes = [
            'quote_id' => $request->quote_id,
            'user_id' => auth()->user()->id,
            'comment' => $request->comment,
        ];

        Comment::create($attributes);

        return response('Comment added');
    }
}
