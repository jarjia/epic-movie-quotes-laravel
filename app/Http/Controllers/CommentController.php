<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Events\QuoteComment;
use App\Http\Requests\CommentRequests\PostCommentRequest;
use App\Models\Comment;
use App\Models\Notification;
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

        $notify = (object)[
            'to' => $request->to_user,
            'notify' => true
        ];

        $comment = Comment::create($attributes);
        $comment->user = auth()->user();

        event(new QuoteComment([
            'new_comment' => $comment,
        ]));

        $data = [
            'from_user' => auth()->user()->id,
            'to_user' => $request->to_user,
            'quote_id' => $request->quote_id,
            'notification' => 'comment',
        ];

        if ($request->to_user !== auth()->user()->id) {
            Notification::create($data);

            event(new NotificationEvent($notify));
        }

        return response('comment added', 201);
    }
}
