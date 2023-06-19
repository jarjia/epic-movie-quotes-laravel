<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Events\QuoteLiked;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    public function store(Request $request)
    {
        $attributes = [
            'quote_id' => $request->quoteId,
            'user_id' => auth()->user()->id
        ];

        $likeIds = [];

        $like = Like::where('quote_id', $attributes['quote_id'])
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($like !== null) {
            $like->delete();

            $likes = Like::where('quote_id', $request->quoteId)->get();
            foreach ($likes as $like) {
                array_push($likeIds, $like->user_id);
            }

            event(new QuoteLiked([
                'likes' => $likeIds
            ]));

            return response('unliked');
        }

        Like::create($attributes);

        $likes = Like::where('quote_id', $request->quoteId)->get();

        foreach ($likes as $like) {
            array_push($likeIds, $like->user_id);
        }

        event(new QuoteLiked([
            'likes' => $likeIds
        ]));

        $data = [
            'from_user' => auth()->user()->id,
            'quote_id' => $request->quoteId,
            'to_user' => $request->to_user,
            'notification' => 'like',
        ];

        $notify = (object)[
            'to' => $request->to_user,
            'notify' => true
        ];

        if ($request->to_user !== auth()->user()->id) {
            $notifications = Notification::where('quote_id', $request->quoteId)->get();

            if ($notifications->count() === 0) {
                Notification::create($data);

                event(new NotificationEvent($notify));

                return response('liked', 201);
            }

            foreach ($notifications as $notification) {
                if (
                    $notification->quote_id === $request->quoteId &&
                    $notification->from_user === auth()->user()->id && $notification->notification === 'like'
                ) {
                    $deleteNotification = Notification::where('id', $notification->id);
                    $deleteNotification->delete();

                    event(new NotificationEvent($notify));

                    Notification::create($data);

                    return response('liked', 201);
                }
            }

            event(new NotificationEvent($notify));

            Notification::create($data);

            return response('liked', 201);
        }
    }
}
