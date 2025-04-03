<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Topups;
use App\Models\Bets1ds;
use App\Models\Bets2ds;
use App\Models\Bets3ds;
use App\Models\Bets4ds;
use App\Models\Bets6ds;
use App\Models\Cashins;

use App\Models\Pick2ds;
use App\Models\JaiAlais;
use App\Models\DrawTypes;
use App\Models\GameTypes;
use App\Models\SubAgents;
use App\Models\Winners1ds;
use App\Models\Winners2ds;
use App\Models\Winners3ds;
use App\Models\Winners4ds;
use App\Models\Winners6ds;
use App\Models\MasterAgents;
use Illuminate\Http\Request;
use App\Models\Winnerspick2ds;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{

    public function new_statistics(Request $request){
        if(auth()->user()->userlevel < 50){
            return redirect('home');
        }

        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        
        $gross2d = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross3d = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross4d = Bets4ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross6d = Bets6ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        
        
        //Get total won amount
        $total2dAmount = Winners2ds::whereBetween('draw_date', [$from, $to])
                                    ->sum('winning_amount');
        $total3dAmount = Winners3ds::whereBetween('draw_date', [$from, $to])
                                    ->sum('winning_amount');
        $total4dAmount = Winners4ds::whereBetween('draw_date', [$from, $to])
                                    ->sum('winning_amount');
        $total6dAmount = Winners6ds::whereBetween('draw_date', [$from, $to])
                                    ->sum('winning_amount');

        //Get total prizes claimed
        $total2dClaimed = Winners2ds::where('claimed',1)
                                        ->whereBetween('draw_date', [$from, $to])
                                        ->count();
        $total3dClaimed = Winners3ds::where('claimed',1)
                                        ->whereBetween('draw_date', [$from, $to])
                                        ->count();
        $total4dClaimed = Winners4ds::where('claimed',1)
                                        ->whereBetween('draw_date', [$from, $to])
                                        ->count();
        $total6dClaimed = Winners6ds::where('claimed',1)
                                        ->whereBetween('draw_date', [$from, $to])
                                        ->count();

        //Get all winners
        $total2dWinners = Winners2ds::whereBetween('draw_date', [$from, $to])->count();
        $total3dWinners = Winners3ds::whereBetween('draw_date', [$from, $to])->count();
        $total4dWinners = Winners4ds::whereBetween('draw_date', [$from, $to])->count();
        $total6dWinners = Winners6ds::whereBetween('draw_date', [$from, $to])->count();


        #Master agents / Sub Agents
        $maObj = new MasterAgents;
        $saObj = new SubAgents;
        $topUpObj = new Topups;
        $ma = $maObj->count();
        $sa = $saObj->count();
        // $ma_earnings = $topUpObj->where('ma_id','>',0)->sum('ma_amount');
        $sa_earnings = $topUpObj->where('sa_id','>',0)->sum('sa_amount');

        return view(
            'admin.reports.sarge-statistics',[
                'from' => $from,
                'to' => $to,
                'won_amount' => $total2dAmount + $total3dAmount + $total4dAmount + $total6dAmount,
                
                'total_winners' => $total2dWinners + $total3dWinners + $total4dWinners + $total6dWinners,
                'total_claimed' => $total2dClaimed + $total3dClaimed + $total4dClaimed + $total6dClaimed,
                // 'total_users' => $total_users,
                'total_ma' => $ma,
                'total_sa' => $sa,
                // 'ma_earnings' => $ma_earnings,
                'sa_earnings' => $sa_earnings,
                // 'cash_in' => $cash_in,
                'gross2d' => $gross2d,
                'gross3d' => $gross3d,
                'gross4d' => $gross4d,
                'gross6d' => $gross6d,
                'total2dWinners' => $total2dWinners,
                'total3dWinners' => $total3dWinners,
                'total4dWinners' => $total4dWinners,
                'total6dWinners' => $total6dWinners,
            ]
        );
    }
    public function statistics(Request $request){
        if(auth()->user()->user_level < 50){

            return redirect('home');
        }

        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
 
        $gross  = 0;
        //Get total won amount
        $total6dAmount = 0;
        //Get total prizes claimed
        $totalpick2dClaimed = 0;
        //Get all winners
        $total2dWinners = 0;

  

        return view(
            'admin.reports.statistics',[
                'from' => $from,
                'to' => $to,
                'won_amount' =>   $total6dAmount,
                
                'total_winners' => $total2dWinners ,
                'total_claimed' =>   $totalpick2dClaimed,
                'grossJaiAlai' => $gross,
                'total2dWinners' => $total2dWinners,
            ]
        );
    }

    public function grossIncome(Request $request) {
        if(auth()->user()->userlevel < 50){
            return redirect('home');
        }
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $play_type = isset($request->play_type) ? $request->play_type : '1';
        $draw_time = isset($request->draw_time) ? $request->draw_time : '2PM';
        $bets2d = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->where('game_type_id', $play_type)
                            ->where('draw_time', $draw_time)
                            ->get();
        $bets3d = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->where('game_type_id', $play_type)
                            ->where('draw_time', $draw_time)
                            ->get();
        $bets4d = Bets4ds::whereBetween('draw_date', [$from, $to])
                            ->where('game_type_id', $play_type)
                            ->where('draw_time', '9PM')
                            ->get();
        $total2dBets = Bets2ds::whereBetween('draw_date', [$from, $to])->sum('amount');
        $total3dBets = Bets3ds::whereBetween('draw_date', [$from, $to])->sum('amount');
        $total4dBets = Bets4ds::whereBetween('draw_date', [$from, $to])->sum('amount');

        $gross2d = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross3d = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross4d = Bets4ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross_amount = $gross2d + $gross3d + $gross4d;

        $play_type = GameTypes::all();
        $draw_time = DrawTypes::all();
        return view('admin.reports.gross-income',[
            'bets2d' => $bets2d,
            'bets3d' => $bets3d,
            'bets4d' => $bets4d,
            'play_type' => $play_type,
            'draw_time' => $draw_time,
            'total2dBets' => $total2dBets,
            'total3dBets' => $total3dBets,
            'total4dBets' => $total4dBets,
            'gross_amount' => $gross_amount 
        ]);
    }

    public function netIncome(Request $request) {
        if(auth()->user()->userlevel < 50){
            return redirect('home');
        }
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $play_type = isset($request->play_type) ? $request->play_type : '1';
        $draw_time = isset($request->draw_time) ? $request->draw_time : '2PM';
        $bets2d = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->where('game_type_id', $play_type)
                            ->where('draw_time', $draw_time)
                            ->get();
        $bets3d = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->where('game_type_id', $play_type)
                            ->where('draw_time', $draw_time)
                            ->get();
        $bets4d = Bets4ds::whereBetween('draw_date', [$from, $to])
                            ->where('game_type_id', $play_type)
                            ->where('draw_time', '9PM')
                            ->get();
        $total2dBets = Bets2ds::whereBetween('draw_date', [$from, $to])->sum('amount');
        $total3dBets = Bets3ds::whereBetween('draw_date', [$from, $to])->sum('amount');
        $total4dBets = Bets4ds::whereBetween('draw_date', [$from, $to])->sum('amount');

        $gross2d = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross3d = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross4d = Bets4ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
        $gross_amount = $gross2d + $gross3d + $gross4d;
        $gateway_amount = $gross_amount * 0.02;
        $net_income = $gross_amount - $gateway_amount;

        $play_type = GameTypes::all();
        $draw_time = DrawTypes::all();
        return view('admin.reports.net-income',[
            'bets2d' => $bets2d,
            'bets3d' => $bets3d,
            'bets4d' => $bets4d,
            'play_type' => $play_type,
            'draw_time' => $draw_time,
            'total2dBets' => $total2dBets,
            'total3dBets' => $total3dBets,
            'total4dBets' => $total4dBets,
            'net_amount' => $net_income
        ]);
    }

    public function wonAmount(Request $request) {
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $play_type = isset($request->play_type) ? $request->play_type : '';
        $draw_time = isset($request->draw_time) ? $request->draw_time : '';

        $winners2d = Winners2ds::select('winners2ds.*','game_types.game')
                                ->join('bets2ds','bets2ds.id','=','winners2ds.bet_id')
                                ->join('game_types','game_types.id','=','bets2ds.game_type_id')
                                ->where('bets2ds.game_type_id', $play_type)
                                ->where('bets2ds.draw_time', $draw_time)
                                ->get();
        $winners3d = Winners3ds::select('winners3ds.*','game_types.game')
                                ->join('bets3ds','bets3ds.id','=','winners3ds.bet_id')
                                ->join('game_types','game_types.id','=','bets3ds.game_type_id')
                                ->where('bets3ds.game_type_id', $play_type)
                                ->where('bets3ds.draw_time', $draw_time)
                                ->get();
        $winners4d = Winners4ds::select('winners4ds.*','game_types.game')
                                ->join('bets4ds','bets4ds.id','=','winners4ds.bet_id')
                                ->join('game_types','game_types.id','=','bets4ds.game_type_id')
                                ->where('bets4ds.game_type_id', $play_type)
                                ->where('bets4ds.draw_time', $draw_time)
                                ->get();
        $winners6d = Winners6ds::select('winners6ds.*','game_types.game')
                                ->join('bets6ds','bets6ds.id','=','winners6ds.bet_id')
                                ->join('game_types','game_types.id','=','bets6ds.game_type_id')
                                ->where('bets6ds.game_type_id', $play_type)
                                ->where('bets6ds.draw_time', $draw_time)
                                ->get();

        $total2dAmount = Winners2ds::where('claimed',1)
                                ->sum('winning_amount');
        $total3dAmount = Winners3ds::where('claimed',1)
                                ->sum('winning_amount');
        $total4dAmount = Winners4ds::where('claimed',1)
                                ->sum('winning_amount');
        $total6dAmount = Winners6ds::where('claimed',1)
                                ->sum('winning_amount');

        $total2dBets = Bets2ds::join('winners2ds','winners2ds.bet_id','=','bets2ds.id')
                                ->whereBetween('bets2ds.draw_date', [$from, $to])
                                ->sum('amount');
        $total3dBets = Bets3ds::join('winners3ds','winners3ds.bet_id','=','bets3ds.id')
                                ->whereBetween('bets3ds.draw_date', [$from, $to])
                                ->sum('amount');
        $total4dBets = Bets4ds::join('winners4ds','winners4ds.bet_id','=','bets4ds.id')
                                ->whereBetween('bets4ds.draw_date', [$from, $to])
                                ->sum('amount');
        $total6dBets = Bets6ds::join('winners6ds','winners6ds.bet_id','=','bets6ds.id')
                                ->whereBetween('bets6ds.draw_date', [$from, $to])
                                ->sum('amount');
        
        $play_type = GameTypes::all();
        $draw_time = DrawTypes::all();
        return view('admin.reports.won-amount',[
            'winners2d' => $winners2d,
            'winners3d' => $winners3d,
            'winners4d' => $winners4d,
            'winners6d' => $winners6d,
            'play_type' => $play_type,
            'draw_time' => $draw_time,
            'total2dBets' => $total2dBets,
            'total3dBets' => $total3dBets,
            'total4dBets' => $total4dBets,
            'total6dBets' => $total6dBets,
            'won_amount' => $total2dAmount + $total3dAmount + $total4dAmount + $total6dAmount
        ]);
    }

    public function bettors(Request $request) {
        $from = $request->from;
        $to = $request->to;
        if(null !== $from && null !== $to){
            $bettors2d = collect(Bets2ds::whereBetween('draw_date', [$from, $to])->get());
            $bettors3d = collect(Bets3ds::whereBetween('draw_date', [$from, $to])->get());
            $bettors4d = collect(Bets4ds::whereBetween('draw_date', [$from, $to])->get());
        } else {
            $bettors2d = collect(Bets2ds::all());
            $bettors3d = collect(Bets3ds::all());
            $bettors4d = collect(Bets4ds::all());
        }

        $all_bettors = $bettors2d->merge($bettors3d)->merge($bettors4d);

        $play_type = GameTypes::all();
        return view('admin.reports.bettors',[
            'all_bettors' => $all_bettors,
            'bettors2d' => $bettors2d,
            'bettors3d' => $bettors3d,
            'bettors4d' => $bettors4d,
            'play_type' => $play_type
        ]);
    }

    public function winners(Request $request) {
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        if($request->all()){
            $winners2d = Winners2ds::whereBetween('draw_date', [$from, $to])->get();
            $winners3d = Winners3ds::whereBetween('draw_date', [$from, $to])->get();
            $winners4d = Winners4ds::whereBetween('draw_date', [$from, $to])->get();
        } else {
            $winners2d = Winners2ds::all();
            $winners3d = Winners3ds::all();
            $winners4d = Winners4ds::all();
        }
        $two = collect($winners2d);
        $three = collect($winners3d);
        $four = collect($winners4d);
        $winners = $two->merge($three)->merge($four);

        $play_type = GameTypes::all();

        return view('admin.reports.winners',[
            'from' => $from,
            'to' => $to,
            'winners2d' => $winners2d,
            'winners3d' => $winners3d,
            'winners4d' => $winners4d,
            'winners' => $winners,
            'play_type' => $play_type
        ]);
    }

    public function lossers() {
        
    }

    public function prizesClaimed(Request $request) {
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $claimed2d = collect(Winners2ds::whereBetween('draw_date', [$from, $to])
                                ->where('claimed',1)
                                ->get());
        $claimed3d = collect(Winners3ds::whereBetween('draw_date', [$from, $to])
                                ->where('claimed',1)
                                ->get());
        $claimed4d = collect(Winners4ds::whereBetween('draw_date', [$from, $to])
                                ->where('claimed',1)
                                ->get());
        $all_claimed = $claimed2d->merge($claimed3d)->merge($claimed4d);

        $play_type = GameTypes::all();

        return view('admin.reports.prizes-claimed',[
            'all_claimed' => $all_claimed,
            'claimed2d' => $claimed2d,
            'claimed3d' => $claimed3d,
            'claimed4d' => $claimed4d,
            'play_type' => $play_type
        ]);
    }
}
