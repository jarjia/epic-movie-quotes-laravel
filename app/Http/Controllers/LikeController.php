<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Events\QuoteLiked;
use App\Http\Requests\LikeRequests\LikeRequest;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    public function store(LikeRequest $request)
    {
        $attributes = $request->validated();

        $data = [
            'quote_id' => $attributes['quoteId'],
            'user_id' => auth()->user()->id
        ];

        $likeIds = [];

        $like = Like::where('quote_id', $data['quote_id'])
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($like !== null) {
            $like->delete();

            $likes = Like::where('quote_id', $attributes['quoteId'])->get();
            foreach ($likes as $like) {
                array_push($likeIds, $like->user_id);
            }

            event(new QuoteLiked([
                'likes' => $likeIds,
                'quoteId' => $attributes['quoteId']
            ]));

            return response('unliked');
        }

        Like::create($data);

        $likes = Like::where('quote_id', $attributes['quoteId'])->get();

        foreach ($likes as $like) {
            array_push($likeIds, $like->user_id);
        }

        event(new QuoteLiked([
            'likes' => $likeIds,
            'quoteId' => $attributes['quoteId']
        ]));

        $data = [
            'from_user' => auth()->user()->id,
            'quote_id' => $attributes['quoteId'],
            'to_user' => $attributes['to_user'],
            'notification' => 'like',
        ];

        $notify = (object)[
            'to' => $attributes['to_user'],
            'notify' => true
        ];

        if ($attributes['to_user'] !== auth()->user()->id) {
            $notifications = Notification::where('quote_id', $attributes['quoteId'])->get();

            if ($notifications->count() === 0) {
                Notification::create($data);

                event(new NotificationEvent($notify));

                return response('liked', 201);
            }

            foreach ($notifications as $notification) {
                if (
                    $notification->quote_id === $attributes['quoteId'] &&
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
