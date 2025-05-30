<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\BetController;
use App\Http\Controllers\AuthOtpController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\Api\WinnersController;
use App\Http\Controllers\API\ClaimPrizeController;
use App\Http\Controllers\API\ServerInfoController;
use App\Http\Controllers\PaymentGatewayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::resource('/winners', WinnersController::class);
Route::post('/balance', [ServerInfoController::class,'balance'])->name('my.balance');

Route::post('/resend-otp', [AuthOtpController::class,'generate'])->name('resend.otp');
// Route::post('/cart', [CartController::class,'store'])->name('cart');
// Route::post('/register', [AuthController::class,'register']);
// Route::post('/login', [AuthController::class,'login']);
// Route::post('/check-reference', [ClaimPrizeController::class,'checkReference']);
// Route::post('/check-contact', [ClaimPrizeController::class,'checkContact']);
// Route::post('/check-prize', [ClaimPrizeController::class,'checkPrize']);


Route::post('/callback', [PaymentGatewayController::class, 'callback'])->name('callback'); //gtpay
Route::post('/pg/notify', [PaymentGatewayController::class, 'notify'])->name('notify'); //luckypay
Route::post('/pg/notify/jd', [PaymentGatewayController::class, 'jd_notify'])->name('jd.notify'); //jdpay

Route::post('/withdraw', [PaymentGatewayController::class, 'withdraw'])->name('withdraw');

//protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [AuthController::class,'logout']);
});