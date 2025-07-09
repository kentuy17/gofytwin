<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bet;
use Inertia\Inertia;
use App\Models\Fight;
use App\Models\EventGame;
use App\Events\FightEvent;
use App\Models\BetHistory;
use App\Events\ResultEvent;
use Illuminate\Http\Request;
use App\Events\LastCallEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\BetController;
use App\Http\Controllers\HomeController;
use App\Models\Settings;
use Illuminate\Support\Facades\Redirect;

class FightController extends Controller
{
    private $currentFight;
    private $current_event;

    const COMMISSION = 10;

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

            // dd($this->currentFight);

            Redis::set('fight', $this->currentFight);

            if($request->status === 'CLOSED') {
                $req = new Request(['lastCall' => false]);
                $this->updateLastCall($req);

                $bet_histories = BetHistory::where('fight_id', $this->currentFight->id);
                $home = new HomeController();
                $data = $home->getCurrentData();
                $total_sum = $data['meron'] + $data['wala'];

                // MERON WIN PERCENT
                $meron_percent = $this->cocoysFormula($data['meron'], $total_sum);

                // WALA WIN PERCENT
                $wala_percent = $this->cocoysFormula($data['wala'], $total_sum);

                if($meron_percent <= 140 || $wala_percent <= 140) {
                    $updated = $this->currentFight->update([
                        'game_winner' => 'C',
                        'status' => 'DONE'
                    ]);

                    return;
                }

                $bet_histories->where('side', 'M')->update(['percent' => $meron_percent]);
                $bet_histories->where('side', 'W')->update(['percent' => $wala_percent]);
            }

            FightEvent::dispatch($this->currentFight);

            if ($request->status === 'DONE') {
                $this->processPayables($request->result, $this->currentFight);

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
            return $e;
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
            'settings' => [
                'show_video' => Settings::get('show_video'),
                'video_banner' => Settings::get('video_banner'),
                'no_credits_banner' => Settings::get('no_credits_banner'),
                'header_banner' => Settings::get('header_banner'),
                'ci_promo_banner' => Settings::get('ci_promo_banner'),
            ]
        ]);
    }

    public function events()
    {
        $events = EventGame::orderBy('id','desc')->get();
        return Inertia::render('events',[
            'events' => $events,
        ]);
    }
    public function bets()
    {
        $bets = BetHistory::where('user_id',Auth::user()->id)
            ->orderBy('id', 'desc')->get();
        return Inertia::render('bets',[
            'bets' => $bets,
        ]);
    }

    public function addEvent(Request $request)
    {
        $event = EventGame::create([
            'name' => $request->event_name ?? 'GameFowl Master Event',
            'status' => 'WAITING',
            'event_date' => Carbon::parse($request->date)->toDateString(),
            'added_by' => Auth::user()->id,
        ]);

        Fight::create([
            'fight_no' => 1,
            'user_id' => Auth::user()->id,
            'status' => 'WAITING',
            'event_id' => $event->id,
            'time_start' => Carbon::now()->toTimeString(),
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

    // $this->processPayables($request->status, $this->currentFight);
    public function processPayables($result, Fight $currentFight)
    {
        $home = new HomeController();
        $data = $home->getCurrentData();
        $total_sum = $data['meron'] + $data['wala'];

        $player_bets = BetHistory::where('fight_id', $currentFight->id)
            ->where('fight_no', $currentFight->fight_no)
            ->get();

        # fetch win bets
        if($result === 'M' || $result === 'W') {
            $side_total = $result === 'M' ? $data['meron'] : $data['wala'];
            // $commission = $this->calculateCommission($side_total, $total_sum);
            $commission = $this->cocoysFormula($side_total, $total_sum);

            // $accumulated = [];
            foreach ($player_bets as $bet) {
                # update bet_histories
                if($bet->side === $result) {
                    $win_amount = ($bet->amount * $commission) / 100;
                    $bet->win_amount = $win_amount;
                    $bet->current_points += $win_amount;
                    $bet->percent = $commission;
                    $bet->status = 'W';

                    $bet->user->credits += intval($win_amount);
                    $bet->user->save();

                    // if($accumulated[$bet->user_id]) {
                    //     $accumulated[$bet->user_id] += $win_amount;
                    // } else {
                    //     $accumulated[$bet->user_id] = $win_amount;
                    // }

                     # broadcast
                     event(new ResultEvent($bet));
                    // ResultEvent::dispatch($bet);
                } else {
                    $bet->status = 'L';
                }

                $bet->save();

                // update bets
            }
        } else {
            foreach ($player_bets as $bet) {
                # code...
                $bet->win_amount = $bet->amount;
                $bet->percent = 0;
                $bet->current_points += $bet->amount;
                $bet->status = $result === 'X' ? 'D' : $result;
                $bet->save();

                $bet->user->credits += $bet->amount;
                $bet->user->save();

                ResultEvent::dispatch($bet);
            }

        }
    }

    private function calculateCommission($side_total, $total_sum)
    {
        $commission = ($side_total * self::COMMISSION) / 100;
        $deducted = $total_sum - $commission;
        return ($deducted / $side_total) * 100;
    }

    private function cocoysFormula($side_total, $total_sum)
    {
        // Total Pool = Total bets on Red + Total bets on Blue
        // House Cut = Total Pool × House Percentage
        // Net Pool = Total Pool − House Cut
        // Payout Rate (per 100) = (Net Pool ÷ Total Bets on Winning Side) × 100
        // Winning Player's Return = (Player's Bet × Payout Rate) ÷ 100
        // Winning Player's Profit = Winning Player's Return − Player's Bet
        if($total_sum === 0) return 0;

        $total_pool = $total_sum;
        $house_cut = ($total_pool * self::COMMISSION) / 100;
        $net_pool = $total_pool - $house_cut;
        $payout_rate = ($net_pool / $side_total) * 100;
        return $payout_rate;
    }

    public function updateFightNumber(Request $request)
    {
        $event = EventGame::find($request->event_id);
        $check = Fight::where('fight_no', $request->fight)
            ->where('event_id', $event->id)
            ->first();

        $current_fight = $event->fights->where('fight_no', $event->current_fight)->first();

        if(!in_array($current_fight->status, ['WAITING','DONE'])
            || $check || $event->current_fight > $request->fight) {
            return Redirect::back()->withErrors([
                'msg' => 'Unable to update fight'
            ]);
        }

        $fight = Fight::create([
            'fight_no' => $request->fight,
            'user_id' => Auth::user()->id,
            'status' => 'WAITING',
            'event_id' => $event->id,
            'time_start' => Carbon::now()->toTimeString(),
        ]);

        $event->current_fight = $fight->fight_no;
        $event->save();

        Redis::set('fight', $fight);

        return Redirect::back()->with('success', 'updated successfully.');
    }

}
