<?php

namespace App\Http\Controllers;

use App\Events\FightEvent;
use App\Models\EventGame;
use App\Models\Fight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use App\Http\Controllers\BetController;
use App\Http\Controllers\HomeController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FightController extends Controller
{
    private $currentFight;

    private function currenctMatch()
    {
        $this->current_event = EventGame::where('status', 'ACTIVE')
            ->orderBy('id', 'desc')->first();

        return Fight::where('event_id', $this->current_event->id)
            ->where('game_winner', null)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function updateFight(Request $request)
    {
        try {
            $this->currentFight = $this->currenctMatch();

            // if (request()->has('is_operator') && $request->is_operator) {
            //     throw new Exception('Manual update is disabled', 401);
            // }

            // if ($request->status == 'D') {
            //     $this->cleanup($fight->fight_no);
            //     return $this->fightDone($fight, $request->result);
            // }

            // if (!$fight) {
            //     $this->hacking($request, 'Update Fight');
            //     throw new \ErrorException('Invalid request');
            // }

            $updated = $this->currentFight->update([
                'game_winner' => $request->result,
                'status' => $request->status
            ]);

            if ($updated) {
                // dd($this->currentFight);
                event(new FightEvent($this->currentFight));
                // event(new BetEvent($bet));
                Redis::set('fight', $this->currentFight);
            }

            if ($request->status === 'DONE') {
                $new = Fight::create([
                    'fight_no' => $this->currentFight->fight_no + 1,
                    'user_id' => Auth::user()->id,
                    'status' => 'WAITING',
                    'event_id' => $this->currentFight->event_id,
                    'time_start' => Carbon::now()->toTimeString(),
                ]);

                Redis::set('fight', $new);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return [
            'new' => $new ?? $updated,
            'redist' => json_decode(Redis::get('fight')),
        ];
    }

    public function operator(Request $request)
    {
        if ($request->user()->user_level === 0) {
            return Redirect::to('/home');
        }

        $home = new HomeController();
        $fight = json_decode(Redis::get('fight'));

        return Inertia::render('poll/operator', [
            'user' => Auth::user(),
            'commonData' => $home->getCurrentData(),
            'player' => [
                'bets' => [
                    'meron' => $home->getUserTotalBets('M', $fight->id),
                    'wala' => $home->getUserTotalBets('W', $fight->id),
                ],
                'credits' => Auth::user()->credits,
            ],
        ]);
    }

    public function getFightTrends(Request $request)
    {
        $event = $this->activeEvent();
        $fights = Fight::where('event_id', $event->id)
            ->whereNot('game_winner', null)
            ->get();

        $results = [];
        foreach ($fights as $key => $fight) {
            # code...
            $results[] =  [
                'fight_no' => $fight->fight_no,
                'result' => $fight->game_winner,
            ];
        }

        $trends = [
            'results' => $results,
            'summary' => [
                [
                    'label' => 'Meron',
                    'count' => $fights->where('game_winner', 'M')->count(),
                    'class' => 'trend-meron'
                ],[
                    'label' => 'Wala',
                    'count' => $fights->where('game_winner', 'W')->count(),
                    'class' => 'trend-wala'
                ],[
                    'label' => 'Draw',
                    'count' => $fights->where('game_winner', 'X')->count(),
                    'class' => 'trend-draw'
                ],[
                    'label' => 'Cancelled',
                    'count' => $fights->where('game_winner', 'C')->count(),
                    'class' => 'bg-secondary'
                ],
            ],
            'groupings' => $fights->groupBy('game_winner'),
        ];

        return $trends;
    }

    private function activeEvent()
    {
        $check = EventGame::where('status', 'ACTIVE')->orderBy('id', 'desc')->first();
        if (!$check) {
            return $this->create([
                'name' => 'EVENT GAMEFOWL MASTER',
                'event_date' => Carbon::now(),
                'time_start' => '08:00:00',
                'status' => 'ACTIVE',
                'added_by' => 1,
            ]);
        }

        return $check;
    }
}
