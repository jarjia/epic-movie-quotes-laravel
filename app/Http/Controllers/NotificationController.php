<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function all(Request $request): JsonResponse
    {
        $notifications = Notification::with('from')->where('to_user', auth()->user()->id)
            ->offset(0)->limit($request->paginate)
            ->orderBy('created_at', 'desc')->get();

        return response()->json([
            'notifications' => $notifications,
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
            $notification->update(['seen' => 1]);
        }

        return response('All marked as read');
    }

    public function getNotSeen(): JsonResponse
    {
        $notifications = Notification::where('to_user', auth()->user()->id)->where('seen', '0');

        return response()->json(['new' => $notifications->count()]);
    }
}
