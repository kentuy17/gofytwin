<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard', [
            'user' => Auth::user(),
        ]);
    }

    public function temporaryLogin(){
        $user = User::find(1);
        Auth::login($user);
    }
    public function temporaryLogout(){
        Auth::logout();
    }
}
