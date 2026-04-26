<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\SupportConversation;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('support.conversation.{id}', function ($user, $id) {
    $conversation = SupportConversation::find($id);

    if (!$conversation) {
        return false;
    }

    return (bool) $user->isAdmin || (int) $conversation->user_id === (int) $user->id;
});

Broadcast::channel('admin.support', function ($user) {
    return (bool) $user->isAdmin;
});