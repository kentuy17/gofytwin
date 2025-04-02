<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $session = request()->session()->get('status');
        if(!$session) {
            Inertia::clearHistory();
        }

        return Inertia::render('dashboard', [
            'user' => Auth::user(),
            'session' => $session,
        ]);
    }

    public function temporaryLogin(){
        $user = User::find(1);
        Auth::login($user);
    }
    public function temporaryLogout(){
        Auth::logout();
    }
    public function live_feed(){
        return view(
            'live-feed',[
                'user_phone' => 'wewewqew',
                 'landing_page' => true,
            ]
        );
    }
}
