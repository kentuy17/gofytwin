<?php

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthOtpController;

Route::get('/home', function () {
    // return Inertia::render('welcome');
    return Redirect::to(route('/'));
})->name('home');

Route::get('/', function () {
    return Redirect::to(route('login'));
})->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Route::get('/login', [AuthOtpController::class, 'login'])->name('login');

Route::controller(AuthOtpController::class)->group(function(){
    Route::get('/login', 'login')->name('otp.login');
    Route::post('/otp/generate', 'generate')->name('otp.generate');
    Route::get('/otp/verification/{user_id}', 'verification')->name('otp.verification');
    Route::post('/otp/login', 'loginWithOtp')->name('otp.getlogin');

    Route::get('/redirect/otp', 'redirectOtpComponent')->name('forced.redirect');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
