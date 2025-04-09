<?php

namespace App\Http\Controllers;

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
    private function currenctMatch()
    {
        $this->current_event = EventGame::where('status', 'ACTIVE')
            ->orderBy('id','desc')->first();

        return Fight::where('event_id', $this->current_event->id)
            ->where('game_winner', null)
            ->orderBy('id', 'desc')
            ->first();
    }

    // public function fetchCurrentData(Request $request)
    // {
    //     $data = [
    //         // meron, wala, fight_no, status
    //         'meron' =>
    //     ];
    // }

    public function updateFight(Request $request)
    {
        try {
            $fight = $this->currenctMatch();

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

            $updated = $fight->update([
                'game_winner' => $request->result,
                'status' => $request->status
            ]);

            if ($updated) {
                // event(new FightEvent($fight));
                Redis::set('fight', $fight);
            }

            if($request->status === 'DONE') {
                $new = Fight::create([
                    'fight_no' => $fight->fight_no + 1,
                    'user_id' => Auth::user()->id,
                    'status' => 'WAITING',
                    'event_id' => $fight->event_id,
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
        if($request->user()->user_level === 0) {
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
}
