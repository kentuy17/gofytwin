<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\FightController;
use App\Http\Controllers\Admin\TotalGross;
use App\Http\Controllers\CashinController;
use App\Http\Controllers\AuthOtpController;
use App\Http\Controllers\Admin\BetsController;
use App\Http\Controllers\ClaimPrizeController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\WithdrawController;
use App\Http\Controllers\API\ServerInfoController;
use App\Http\Controllers\Admin\CashinController as AdminCashIn;

Route::get('/', function () {
    return Redirect::to(route('login'));
})->middleware('guest');
Route::get('/users/list', [AccountsController::class, 'users'])->name('users.list');
// Route::get('/admin/user/cash-in/{id}',[AdminCashIn::class,'userCashin'])->middleware('auth')->name('admin.user.cashin');
Route::get('/admin/users/view/{id}', [AccountsController::class, 'viewUser'])->name('view.user')->middleware('auth');
Route::get('/test/login',[HomeController::class,'temporaryLogin'])->name('testttest.login');
Route::get('/live-feed',[HomeController::class,'live_feed'])->name('testttest.live.feed');
Route::post('/webrequest/captcha', [AuthOtpController::class,'check'])->name('captcha');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/admin/bets/list', [BetsController::class, 'bets'])->name('admin.bets.list');

    Route::get('/test/logout',[HomeController::class,'temporaryLogout'])->name('testttest.logout');
    Route::get('/admin/users/list', [AccountsController::class, 'users'])->name('admin.users.list');
    Route::get('/admin/users/otp/list', [AccountsController::class, 'user_otp'])->name('users.otp.list');
    Route::get('/admin/users/spam', [AccountsController::class, 'spam'])->middleware('auth')->name('spam');

    Route::get('/admin/user/cash-in/{id}',[AdminCashIn::class,'userCashin'])->middleware('auth')->name('admin.user.cashin');
    Route::get('/admin/users/view/{id}', [AccountsController::class, 'viewUser'])->name('view.user')->middleware('auth');
    Route::get('/admin/users/edit/{id}', [AccountsController::class, 'editUser'])->name('edit.user')->middleware('auth');
    Route::post('/admin/users/edit/{id}', [AccountsController::class, 'editUser'])->middleware('auth');

    Route::post('/winner/info', [ServerInfoController::class,'winner_info'])->name('winner.info')->middleware('auth');

    Route::get('/admin/core/statistics', [ReportsController::class, 'statistics'])->middleware('auth')->name('statistics');
    Route::get('/admin/core/statistics/gross-income', [ReportsController::class, 'grossIncome'])->middleware('auth')->name('gross');
    Route::get('/admin/cash-in',[AdminCashIn::class,'index'])->middleware('auth')->name('admin.cashin');
    Route::get('/admin/net-income',[AdminCashIn::class,'net_income'])->middleware('auth')->name('admin.net.income');
    Route::get('/admin/total-gross',[TotalGross::class,'total_gross'])->middleware('auth')->name('admin.total.gross');

    Route::get('/cash-in', [CashinController::class, 'cash_in'])->middleware('auth')->name('cashin');
    Route::post('/cash-in',[CashinController::class,'processPayment'])->middleware('auth')->name('cashin.pay');
    Route::get('/admin/core/statistics/won-amount', [ReportsController::class, 'wonAmount'])->middleware('auth')->name('won amount');
    Route::get('/admin/users', [AccountsController::class, 'users'])->middleware('auth')->name('users');
    Route::get('/admin/core/statistics/prizes-claimed', [ReportsController::class, 'prizesClaimed'])->middleware('auth')->name('prizes.claimed');
    Route::get('/admin/core/statistics/winners', [ReportsController::class, 'winners'])->middleware('auth')->name('winners');
    // Route::get('/admin/claim/requests',[WithdrawController::class,'withdrawal'])->name('withdrawal')->middleware('auth');
    Route::post('/check-balance', [ServerInfoController::class,'check_balance'])->name('check.balance')->middleware('auth');
    Route::post('/get-bettors', [ServerInfoController::class,'get_bettors'])->name('get.bettors')->middleware('auth');
    Route::post('/get-total-agents-earnings', [ServerInfoController::class,'get_total_agent_earnings'])->name('get.total.agents.earnings')->middleware('auth');
    Route::post('/get-inactive', [ServerInfoController::class,'get_inactive'])->name('get.inactive')->middleware('auth');
    // Route::get('/play/3d/receipt',[PlaythreeController::class,'pay'])->name('play.three.pay')->middleware('auth');
    Route::get('/winner/info/cashin', [ServerInfoController::class,'cashin_info_board'])->name('cashin.info')->middleware('auth');

    // React/Inertia Front-end
    Route::get('/player/deposit', [UserController::class, 'deposit'])->name('deposit');
    Route::get('/admin/config', [HomeController::class, 'adminConfig'])->name('admin.config');
    Route::post('/admin/config', [HomeController::class, 'updateConfig'])->name('update.config');
    Route::post('/admin/toggle-video', [HomeController::class, 'toggleVideo']);

    // Betting
    Route::post('/bet/add', [BetController::class, 'addBet'])->name('add.bet');
    Route::get('/fight/update', [FightController::class, 'updateFight'])->name('update.fight');
    Route::post('/fight/update', [FightController::class, 'updateFight'])->name('post.update.fight');
    Route::get('/operator', [FightController::class, 'operator'])->name('operator');
    Route::get('/fight/trend', [FightController::class, 'getFightTrends'])->name('fight.trend');
    Route::post('/fight/lastcall', [FightController::class, 'updateLastCall'])->name('update.lastcall');
    Route::get('/bets', [FightController::class, 'bets'])->name('bets');



    Route::get('/events', [FightController::class, 'events'])->name('events');
    Route::post('/event/add', [FightController::class, 'addEvent'])->name('add.event');
    Route::post('/event/activate/{id}', [FightController::class, 'activateEvent'])->name('activate.event');
    Route::post('/event/update-fight', [FightController::class, 'updateFightNumber'])->name('activate.update-fight');

    //referral processes
    Route::get('/admin/claim/requests',[WithdrawController::class,'withdrawal'])->name('withdrawal')->middleware('auth');
    Route::get('/admin/claim/requests/list',[WithdrawController::class,'list'])->name('withdrawal.list')->middleware('auth');
    Route::post('/claim-prize/process/referral',[ClaimPrizeController::class,'processReferral'])->name('claim.process.referral')->middleware('auth');
    Route::post('/winner/info/winner/board', [ServerInfoController::class,'winner_info_board'])->name('winner.info.winner.board')->middleware('auth');
    // Route::get('/admin/winners-board/from/{from}/to/{to}',[WinnersDownloadController::class,'dldownload'])->name('winners.download')->middleware('auth');


    Route::post('/cash-in',[CashinController::class,'processPayment'])->middleware('auth')->name('cashin.pay');


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
