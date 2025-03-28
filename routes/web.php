<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\AuthOtpController;
use App\Http\Controllers\Admin\AccountsController;

Route::get('/', function () {
    return Redirect::to(route('login'));
})->middleware('guest');

Route::get('/test/login',[HomeController::class,'temporaryLogin'])->name('testttest.login');
Route::get('/live-feed',[HomeController::class,'live_feed'])->name('testttest.live.feed');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');


    Route::get('/test/logout',[HomeController::class,'temporaryLogout'])->name('testttest.logout');
    Route::get('/admin/users/list', [AccountsController::class, 'users'])->name('users.list');
    Route::get('/admin/users/otp/list', [AccountsController::class, 'user_otp'])->name('users.otp.list');
    Route::get('/admin/users/spam', [AccountsController::class, 'spam'])->middleware('auth')->name('spam');

    Route::get('/admin/user/cash-in/{id}',[AdminCashIn::class,'userCashin'])->middleware('auth')->name('admin.user.cashin');
    Route::get('/admin/users/view/{id}', [AccountsController::class, 'viewUser'])->name('view.user')->middleware('auth');
    Route::post('/winner/info', [ServerInfoController::class,'winner_info'])->name('winner.info')->middleware('auth');
});
// Route::get('/otp/login', [AuthOtpController::class, 'redirectToHome']);
Route::controller(AuthOtpController::class)->group(function(){
    Route::get('/login', 'login')->name('otp.login');
    Route::post('/otp/generate', 'generate')->name('otp.generate');
    Route::get('/otp/verification/{user_id}', 'verification')->name('otp.verification');
    Route::post('/home', 'loginWithOtp')->name('otp.getlogin');

    Route::get('/redirect/otp', 'redirectOtpComponent')->name('forced.redirect');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
