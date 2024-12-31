<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('chat.{senderId}.{receiverId}', function ($user, $senderId, $receiverId) {
    // Allow the channel if the user is either the sender or the receiver
    // return (int) $user->id === (int) $senderId || (int) $user->id === (int) $receiverId;
    return true;
});
