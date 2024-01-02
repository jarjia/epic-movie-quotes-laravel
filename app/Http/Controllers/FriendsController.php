<?php

namespace App\Http\Controllers;

use App\Events\FriendEvent;
use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

use function GuzzleHttp\Promise\all;

class FriendsController extends Controller
{
    public function addFriend(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $user2 = User::find($request->to_id);

        $user->friends()->attach($request->to_id, ['status' => 'recieved']);
        $user2->friends()->attach(auth()->user()->id, ['status' => 'pending']);

        $notify = (object)[
            'to' => $request->to_id,
            'notify' => true
        ];

        $status = (object)[
            'to' => $request->to_id,
            'activity' => true,
            'refetchFriends' => true
        ];

        event(new FriendEvent($status));

        $data = [
            'from_user' => auth()->user()->id,
            'to_user' => $request->to_id,
            'notification' => 'friend-request',
        ];

        Notification::create($data);

        event(new NotificationEvent($notify));

        return response('Friend request sent', 201);
    }

    public function reject(Request $request)
    {
        $user = User::find($request->friend_id);
        $user2 = User::find(auth()->user()->id);

        $user->friends()->detach($user2->id);
        $user2->friends()->detach($user->id);

        $status = (object)[
            'to' => $request->friend_id,
            'activity' => true,
            'refetchFriends' => false
        ];

        if($request->unfriend) {
            event(new FriendEvent($status));

            return response('your friend was unfriended');
        }

        $notify = (object)[
            'to' => auth()->user()->id,
            'notify' => true
        ];

        if($request->not_id === null) {
            $notification = Notification::where('from_user', $request->friend_id)
                ->where('to_user', auth()->user()->id)
                ->where('notification', 'friend-request')->get();

            foreach ($notification as $notificationSingle) {
                $notificationSingle->delete();
            }
        } else {
            $notification = Notification::find($request->not_id);

            $notification->delete();
        }

        event(new FriendEvent($status));

        event(new NotificationEvent($notify));

        return response('friend request was rejected');
    }

    public function accept(Request $request)
    {
        $user = User::find($request->sender_id);
        $user2 = User::find($request->friend_id);

        $user->friends()->updateExistingPivot($request->friend_id, ['status' => 'friends']);
        $user2->friends()->updateExistingPivot($request->sender_id, ['status' => 'friends']);

        $status = (object)[
            'to' => $request->sender_id,
            'activity' => true,
            'refetchFriends' => true
        ];

        $notify = (object)[
            'to' => auth()->user()->id,
            'notify' => true
        ];

        if($request->not_id === null) {
            $notification = Notification::where('from_user', $request->sender_id)
                ->where('to_user', $request->friend_id)
                ->where('notification', 'friend-request')->get();

            foreach ($notification as $notificationSingle) {
                $notificationSingle->delete();
            }
        } else {
            $notification = Notification::find($request->not_id);

            $notification->delete();
        }

        event(new FriendEvent($status));

        event(new NotificationEvent($notify));

        return response('Friend request was approved');
    }

    public function index()
    {
        $usersFriends = User::find(auth()->user()->id)->friends;

        $arr = [];

        foreach($usersFriends as $friend) {
            if($friend->pivot->status === 'friends') {
                $userImage = '';
                if (strpos($friend->thumbnail, 'assets') === 0) {
                    $userImage = asset($friend->thumbnail);
                } else {
                    $userImage = asset('storage/' . $friend->thumbnail);
                }

                $friend['thumbnail'] = $userImage;

                array_push($arr, $friend);
            }
        }

        return response()->json($arr);
    }
}
