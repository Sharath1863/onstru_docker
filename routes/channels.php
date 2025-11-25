<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

// Log::info('message routes/channels.php');

Broadcast::channel('chat.{userId}', function ($user, $userId) {

    $request = request();

    // Log::info('Broadcast auth attempt', [
    //     'auth_user' => $user?->id,
    //     'channel_user' => $userId,
    //     'url' => $request->fullUrl(),
    //     'headers' => $request->headers->all(),
    //     'token' => $request->bearerToken(),
    // ]);

    // Log::info('Broadcast auth', ['auth_user' => $user->id, 'channel_user' => $userId, 'url' => $request->fullUrl()]);

    return (int) $user->id === (int) $userId;
});

// Broadcast::channel('chat.{userId}', function ($user, $userId) {
//     // Authorize only if the user matches the requested channel
//     Log::info('Authorizing user '.$user->id.' for channel chat.'.$userId);

//     return (int) $user->id === (int) $userId;
// });
