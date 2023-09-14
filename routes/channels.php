<?php

use App\Broadcasting\OnlineChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('notification.{userId}', function ($user, $userId) {
    return $user->id === (int)$userId;
});

Broadcast::channel('online', OnlineChannel::class);
