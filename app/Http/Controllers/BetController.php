<?php

namespace App\Http\Controllers;

use App\Events\BetEvent;
use App\Models\Bet;
use App\Models\BetHistory;
use App\Models\EventGame;
use App\Models\Fight;
use App\Models\Hacking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class BetController extends Controller
{
    //
    private $current_event;
    private $current_fight;

    public function __construct()
    {
        $this->current_event = EventGame::where('status','ACTIVE')
            ->orderBy('id','DESC')
            ->first();

        $fight = json_decode(Redis::get('fight'));

        if($fight) {
            $this->current_fight = $fight;
        } else {
            $this->current_fight = Fight::where('event_id', $this->current_event->id)
                // ->where('status', 'OPEN')
                ->orderBy('id', 'DESC')
                ->first();
        }
    }

    public function addBet(Request $request)
    {
        try {
            $points_before_bet = Auth::user()->credits;

            $this->current_fight = Fight::where('event_id', $this->current_event->id)
                // ->where('status', 'OPEN')
                ->orderBy('id', 'DESC')
                ->first();

            if (!$this->current_fight) {
                $this->hacking($request, 'Illegal Bet');
                return response()->json([
                    'fight' => $this->current_fight,
                    'event' => $this->current_event,
                    'status' => 400,
                    'error' => 'Illegal Bet!!!',
                ], 400);
            }

            if ($this->current_fight->status == 'CLOSED') {
                $this->hacking($request, 'Closed Bet');
                return response()->json([
                    'status' => 400,
                    'error' => 'Betting is Closed!!!',
                ], 400);
            }

            // if ($this->current_fight->fight_no !== $request->fight_no) {
            //     $this->hacking($request, 'Fight number');
            //     return response()->json([
            //         'status' => 400,
            //         'error' => 'Invalid Fight number!!!',
            //         'request' => $request->all(),
            //         'current' => $this->current_fight
            //     ], 400);
            // }

            $bet = Bet::create([
                'fight_id' => $this->current_fight->id,
                'fight_no' => $this->current_fight->fight_no,
                'user_id' => Auth::user()->id,
                'amount' => $request->amount,
                'side' => $request->side,
                'status' => 'F',
            ]);

            event(new BetEvent($bet));
            // event(new BetEvent('hello world'));

            if (Auth::user()->user_level < 3) {
                Auth::user()->decrement('credits', $request->amount);

                $points_after_bet = Auth::user()->credits;
                $current_points = Auth::user()->credits;

                //Add in Bet History
                BetHistory::create([
                    'bet_id' => $bet->id,
                    'user_id' => $request->user_id ?? Auth::user()->id,
                    'fight_id' => $bet->fight_id,
                    'fight_no' => $bet->fight_no,
                    'status' => 'P',
                    'side' => $bet->side,
                    'percent' => 0,
                    'amount' => $bet->amount,
                    'win_amount' => 0,
                    'points_before_bet' => $points_before_bet,
                    'points_after_bet' => $points_after_bet,
                    'current_points' => $current_points,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'OK',
            'data' => $bet
        ]);
    }

    private function hacking(Request $request, $violation)
    {
        Hacking::create([
            'user_id' => Auth::user()->id,
            'request' => json_encode($request->all()),
            'violation' => $violation
        ]);
    }

    public function dummyBets($request)
    {
        $bet = Bet::create([
            'fight_id' => $request['fight_id'],
            'fight_no' => $request['fight_no'],
            'user_id' => 0,
            'amount' => $request['amount'],
            'side' => $request['side'],
            'status' => 'F',
        ]);
        // $bet = collect($data);

        event(new BetEvent($bet));
    }
}
