<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('bet-channel', function ($user) {
    // return (int) $user->id === (int) $id;
    return Auth::check() && (int)$user->id === Auth::id();
});

Broadcast::channel('fight-channel', function ($user) {
    return Auth::check() && (int)$user->id === Auth::id();
    // return true;
});