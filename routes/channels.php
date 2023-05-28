<?php

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('chats.{chatId}', function (User $user, int $chatId) {
    $chat = Chat::findOrNew($chatId);
    $chat->load(['post']);

    return (int) $user->id == $chat->user_id ||
            (int) $user->id == $chat->post->user_id;
});