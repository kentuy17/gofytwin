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
        if (!$session) {
            Inertia::clearHistory();
        }

        $bets = collect([
            'meron' => 23829,
            'wala' => 24512,
            'fight_no' => 23,
            'status' => 'OPEN',
        ]);

        return Inertia::render('dashboard', [
            'user' => Auth::user(),
            'session' => $session,
            'commonData' => $bets
        ]);
    }

    public function temporaryLogin()
    {
        $user = User::find(1);
        Auth::login($user);
    }

    public function temporaryLogout()
    {
        Auth::logout();
    }

    public function live_feed()
    {
        return view(
            'live-feed',
            [
                'user_phone' => 'wewewqew',
                'landing_page' => true,
            ]
        );
    }
}
