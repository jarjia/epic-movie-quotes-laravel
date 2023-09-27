<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequests\AllNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function all(AllNotificationRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $filter = $request->filter;
        $notifications = Notification::with('from')
            ->where('to_user', auth()->user()->id)
            ->offset(0)
            ->limit($attributes['paginate'])
            ->orderBy('created_at', 'desc');

        if (!is_null($filter) && $filter !== '') {
            if ($filter === 'new') {
                $notifications->where('seen', '0');
            } else {
                $notifications->where('notification', $filter);
            }
        }

        $filteredNotifications = $notifications->get();

        $transformedNotifications = new NotificationResource($filteredNotifications);

        return response()->json([
            'notifications' => $transformedNotifications,
            'cur_page' => $request->paginate,
            'last_page' => Notification::where('to_user', auth()->user()->id)->get()->count()
        ]);
    }

    public function read(Notification $notifyId): Response
    {
        $notifyId->update(['seen' => 1]);

        return response('read');
    }

    public function readAll(): Response
    {
        $notifications = Notification::where('to_user', auth()->user()->id)->get();

        foreach ($notifications as $notification) {
            if($notification->notification !== 'friend-request') {
                $notification->update(['seen' => 1]);
            }
        }

        return response('All marked as read');
    }

    public function getNotSeen(): JsonResponse
    {
        $notifications = Notification::where('to_user', auth()->user()->id)->where('seen', '0');

        return response()->json(['new' => $notifications->count()]);
    }
}
