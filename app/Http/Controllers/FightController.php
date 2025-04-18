<?php

namespace App\Http\Controllers;

use App\Events\FightEvent;
use App\Events\LastCallEvent;
use App\Events\ResultEvent;
use App\Models\EventGame;
use App\Models\Fight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use App\Http\Controllers\BetController;
use App\Http\Controllers\HomeController;
use App\Models\BetHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FightController extends Controller
{
    private $currentFight;
    private $current_event;

    const COMMISSION = 15;

    private function currenctMatch()
    {
        if($this->currentFight) {
            return $this->currentFight;
        }

        $this->current_event = EventGame::where('status', 'ACTIVE')
                ->orderBy('id', 'desc')->first();

        $fight = json_decode(Redis::get('fight'));
        if(!$fight) {
            $fight = Fight::where('event_id', $this->current_event->id)
                ->orderBy('id', 'desc')
                ->first();
        } else {
            $fight = Fight::find($fight->id);
        }

        $this->currentFight = $fight;
        return $fight;
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
                // event(new FightEvent($this->currentFight));
                FightEvent::dispatch($this->currentFight);
                // event(new BetEvent($bet));
                Redis::set('fight', $this->currentFight);

                if($request->status === 'CLOSED') {
                    $req = new Request(['lastCall' => false]);
                    $this->updateLastCall($req);


                }
            }

            if ($request->status === 'DONE') {
                event(new ResultEvent($this->currentFight));

                $this->processPayables($request->status, $this->currentFight);

                $new = Fight::create([
                    'fight_no' => $this->currentFight->fight_no + 1,
                    'user_id' => Auth::user()->id,
                    'status' => 'WAITING',
                    'event_id' => $this->currentFight->event_id,
                    'time_start' => Carbon::now()->toTimeString(),
                ]);

                $this->current_event->current_fight = $new->fight_no;
                $this->current_event->save();

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

    public function events()
    {
        $events = EventGame::orderBy('status','desc')
            ->orderBy('id', 'desc')->get();
        return Inertia::render('events',[
            'events' => $events,
        ]);
    }

    public function addEvent()
    {
        EventGame::create([
            'name' => 'GameFowl Master Event',
            'status' => 'WAITING',
            'event_date' => Carbon::now()->toDateString(),
            'added_by' => 1,
        ]);

        return redirect()->intended(route('events', absolute: false));
    }

    public function activateEvent($id)
    {
        try {
            $current = EventGame::where('status', 'ACTIVE')->first();
            $current->status = 'DONE';
            $current->save();

            $active = EventGame::find($id);
            $active->status = 'ACTIVE';
            $active->save();

            Redis::set('event', $active);

            $fight = $active->fights();
            if(!$fight) {
                $fight = Fight::create([
                    'fight_no' => $active->current_fight,
                    'user_id' => 1,
                    'status' => 'WAITING',
                    'event_id' => $active->id,
                    'time_start' => Carbon::now()->toTimeString(),
                ]);
            } else {
                $fight = $fight->orderBy('fight_no','desc')->first();
                $active->current_fight = $fight->fight_no;
                $active->save();
            }
            Redis::set('fight', $fight);
        } catch (\Throwable $th) {
            throw $th;
        }

        return redirect()->intended(route('events', absolute: false));
    }

    public function getFightTrends(Request $request)
    {
        $event = $this->activeEvent();
        $fights = Fight::where('event_id', $event->id)
            ->whereNot('game_winner', null)
            ->get();

        $results = [];
        foreach ($fights as $fight) {
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

    public function updateLastCall(Request $request): void
    {
        Redis::set('lastCall', $request->lastCall);
        event(new LastCallEvent($request->lastCall));
    }

    public function processPayables($result, Fight $currentFight)
    {
        $home = new HomeController();
        $data = $home->getCurrentData();
        $total_sum = $data['meron'] + $data['wala'];

        # fetch win bets
        if($result === 'M' || $result === 'W') {
            $side_total = $result === 'M' ? $data['M'] : $data['wala'];
            $commission = $this->calculateCommission($side_total, $total_sum);

            $winners = BetHistory::where('side', $result)
                ->where('fight_id', $currentFight->id)
                ->where('fight_no', $currentFight->fight_no)
                ->get();

            foreach ($winners as $winner) {
                # update bet_histories
                $win_amount = ($winner->amount * $commission) / 100;
                $winner->win_amount = $win_amount;
                $winner->save();

                # update bets


                # broadcast

            }
        }
    }

    private function calculateCommission($side_total, $total_sum)
    {
        $commission = ($side_total * self::COMMISSION) / 100;
        $deducted = $total_sum - $commission;
        return ($deducted / $side_total) * 100;
    }


}
