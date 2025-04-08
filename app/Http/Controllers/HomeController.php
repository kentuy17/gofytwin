<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\EventGame;
use App\Models\Fight;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    private $fight;
    private $event;

    public function __construct()
    {
        $this->fight = json_decode(Redis::get('fight'));
        $this->event = EventGame::where('status', 'ACTIVE')
        ->orderBy('id', 'DESC')
        ->first();

    }

    public function index()
    {
        $session = request()->session()->get('status');
        if (!$session) {
            Inertia::clearHistory();
        }

        // $bets = Bet::where('user_id', Auth::user()->id)->get();

        return Inertia::render('poll/dashboard', [
            'user' => Auth::user(),
            'commonData' => $this->getCurrentData(),
            'player' => [
                'bets' => [
                    'meron' => $this->getUserTotalBets('M', $this->fight->id),
                    'wala' => $this->getUserTotalBets('W', $this->fight->id),
                ],
                'credits' => Auth::user()->credits,
            ],
        ]);
    }

    private function getUserTotalBets($side, $fight_id)
    {
        return Bet::where('fight_id', $fight_id)
            ->where('user_id', Auth::user()->id)
            ->where('side', $side)
            ->sum('amount');
    }

    private function getSideBetsPerFight($side, $fight_id)
    {
        return Bet::where('fight_id', $fight_id)
            ->where('side', $side)
            ->sum('amount');
    }

    private function getCurrentData()
    {
        $fight = Redis::get('fight');
        if (!$fight) {
            $event = EventGame::where('status', 'ACTIVE')
                ->orderBy('id', 'DESC')
                ->first();

            $fight = Fight::where('event_id', $event->id)
                ->orderBy('id', 'DESC')
                ->first();

            Redis::set('fight', json_encode($fight));
        }

        $fight = json_decode(Redis::get('fight'));
        $meron = $this->getSideBetsPerFight('M', $fight->id);
        $wala = $this->getSideBetsPerFight('W', $fight->id);

        // dd($fight);

        $data = [
            'meron' => $meron,
            'wala' => $wala,
            'fight_no' => $fight->fight_no,
            'status' => $fight->status,
        ];

        return $data;
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
