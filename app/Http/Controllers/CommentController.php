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
        $attributes = $request->validated();

        $data = [
            'quote_id' => $attributes['quote_id'],
            'user_id' => auth()->user()->id,
            'comment' => $attributes['comment'],
        ];

        $notify = (object)[
            'to' => $attributes['to_user'],
            'notify' => true
        ];

        $comment = Comment::create($data);
        $comment->user = auth()->user();
        if (strpos($comment->user->thumbnail, 'assets') === 0) {
            $comment->user->thumbnail = asset($comment->user->thumbnail);
        } elseif (strpos($comment->user->thumbnail, 'images') === 0) {
            $comment->user->thumbnail = asset('storage/' . $comment->user->thumbnail);
        }

        $newComment = [
            'id' => $comment->id,
            'quote_id' => $comment->quote_id,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'thumbnail' => $comment->user->thumbnail
            ],
            'created_at' => $comment->created_at,
            'comment' => $comment->comment
        ];

        event(new QuoteComment([
            'new_comment' => $newComment,
        ]));

        $data = [
            'from_user' => auth()->user()->id,
            'to_user' => $attributes['to_user'],
            'quote_id' => $attributes['quote_id'],
            'notification' => 'comment',
        ];

        if ($attributes['to_user'] !== auth()->user()->id) {
            Notification::create($data);

            event(new NotificationEvent($notify));
        }

        return response('comment added', 201);
    }
}
