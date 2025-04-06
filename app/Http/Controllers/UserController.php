<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    public function deposit(): Response
    {
        return Inertia::render('deposit', [
            'providers' => [
                [
                    'name' => 'GCash',
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
                [
                    'name' => 'UnionBank',
                    'icon' => '/images/union.jpg',
                    'min' => 500,
                    'max' => 50000
                ],
            ]
        ]);
    }
}
