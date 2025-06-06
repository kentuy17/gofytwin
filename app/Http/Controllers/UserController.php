<?php

namespace App\Http\Controllers;

use App\Models\Cashins;
use App\Models\User;
use App\Models\Withdrawals;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class UserController extends Controller
{
    public function index()
    {
        $session = request()->session()->get('status');
        if (!$session) {
            Inertia::clearHistory();
        }

        return Inertia::render('dashboard', [
            'user' => Auth::user(),
            'session' => $session,
        ]);
    }

    public function cashout(): Response
    {
        // dd(auth()->user());
        return Inertia::render('cashout', [
            'providers' => [
                [
                    'name' => 'GCash Fast',
                    'tag' => '(INSTANT)',
                    'icon' => '/images/gcash.png',
                    'min' => 100,
                    'max' => 10000
                ],
                [
                    'name' => 'GCash Direct',
                    'tag' => '(DIRECT)',
                    'icon' => '/images/gcash.png',
                    'min' => 100,
                    'max' => 10000
                ],
                [
                    'name' => 'Maya',
                    'icon' => '/images/maya.png',
                    'min' => 200,
                    'max' => 10000
                ],
            ],
            'xrftoken'=>csrf_token()
        ]);
    }
    public function deposit(): Response
    {
        // dd(auth()->user());
        return Inertia::render('deposit', [
            'providers' => [
                [
                    'name' => 'GCash Fast',
                    'tag' => '(INSTANT)',
                    'icon' => '/images/gcash.png',
                    'min' => 100,
                    'max' => 10000
                ],
                [
                    'name' => 'GCash Direct',
                    'tag' => '(DIRECT)',
                    'icon' => '/images/gcash.png',
                    'min' => 100,
                    'max' => 10000
                ],
                [
                    'name' => 'Maya',
                    'icon' => '/images/maya.png',
                    'min' => 200,
                    'max' => 10000
                ],
            ],
            'xrftoken'=>csrf_token()
        ]);
    }
    public function my_cashin()
    {
        $ci = Cashins::where('user_id',Auth::user()->id)
            ->orderBy('id', 'desc')->get();
        return Inertia::render('my-cashin',[
            'ci' => $ci,
        ]);
    }
    public function my_cashout()
    {
        $co = Withdrawals::where('user_id',Auth::user()->id)
            ->orderBy('id', 'desc')->get();
        return Inertia::render('my-cashout',[
            'co' => $co,
        ]);
    }
}
