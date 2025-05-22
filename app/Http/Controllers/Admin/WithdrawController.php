<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\DrawTypes;
use App\Models\Winners2ds;
use App\Models\Winners3ds;
use App\Models\Winners4ds;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Withdrawals;
use Illuminate\Database\Eloquent\Builder;

class WithdrawController extends Controller
{
 
    public function threeDigitsCompleteWin(Request $request){
        if(auth()->user()->userlevel < self::CSR){
            
            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = Winners3ds::where('claimed', 1)
                              ->where('approved','>', 0)
                              ->where('process_status', 'success')
                              ->whereBetween('draw_date', [$from, $to]);
        if($request->all()){
            if($request->draw_time){
                $winners->where('draw_time',$request->draw_time);
            }
        }  
        $draw_types = DrawTypes::all();

        return view(
            'admin.winners.completed',[
                'total_winers' => $winners->count(),
                'winners' => $winners->get(),
                'play' => '3D Completed  Winners List',
                'draw_2pm' => $winners->where('draw_time','2PM')->count(),
                'draw_5pm' => $winners->where('draw_time','5PM')->count(),
                'draw_9pm' => $winners->where('draw_time','5PM')->count(),
                'draw_types' => $draw_types
            ]
        );
    }
  
    public function threeDigitsWin(Request $request){
        if(auth()->user()->userlevel < self::CSR){
            
            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = Winners3ds::whereBetween('draw_date', [$from, $to]);
        if($request->all()){
            if($request->draw_time){
                $winners->where('draw_time',$request->draw_time);
            }
        }  
        $draw_types = DrawTypes::all();

        return view(
            'admin.winners.list',[
                'total_winers' => $winners->count(),
                'winners' => $winners->get(),
                'play' => '3D Winners List',
                'draw_2pm' => $winners->where('draw_time','2PM')->count(),
                'draw_5pm' => $winners->where('draw_time','5PM')->count(),
                'draw_9pm' => $winners->where('draw_time','5PM')->count(),
                'draw_types' => $draw_types
            ]
        );
    }
 
    public function twodigits(Request $request){
        $winners = Winners2ds::whereNot(function (Builder $query) {
            $query->where('claimed', 1)
                  ->where('approved', '>', 0)
                  ->where('process_status', 'success');
        });
        if($request->all()){
            if($request->date){
                $winners->where('draw_date',$request->date);
            }
            if($request->draw_time){
                $winners->where('draw_time',$request->draw_time);
            }
        } 
        $draw_types = DrawTypes::all();

        return view(
            'admin.winners.win',[
                'total_winers' => $winners->count(),
                'winners' => $winners->get(),
                'play' => '2D Claimed Winners',
                'draw_2pm' => $winners->where('draw_time','2PM')->count(),
                'draw_5pm' => $winners->where('draw_time','5PM')->count(),
                'draw_9pm' => $winners->where('draw_time','5PM')->count(),
                'draw_types' => $draw_types
            ]
        );
    }

    public function withdrawal(Request $request){
        $winners = Withdrawals::whereNot(function (Builder $query) {
            $query->where('claimed', 1)
                  ->where('approved', '>', 0)
                  ->where('process_status', 'success');
        });
        if($request->all()){
            if($request->date){
                $winners->where('draw_date',$request->date);
            }
            if($request->draw_time){
                $winners->where('draw_time',$request->draw_time);
            }
        } 
        $draw_types = DrawTypes::all();

        return view(
            'admin.withdrawals.win',[
                'total_winers' => $winners->count(),
                'winners' => $winners->get(),
                'play' => 'Agent Withdrawal Requests',
                'draw_types' => $draw_types
            ]
        );
    }
    public function list(Request $request){
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // ->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])

        $winners = Withdrawals::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->get();
        $draw_types = DrawTypes::all();

        return view(
            'admin.withdrawals.trace',[
                'total_winers' => $winners->count(),
                'winners' => $winners,
                'play' => 'Agent Withdrawal List',
                'draw_types' => $draw_types
            ]
        );
    }

    public function fourdigits(Request $request){
        $winners = Winners4ds::whereNot(function (Builder $query) {
            $query->where('claimed', 1)
                  ->where('approved', '>', 0)
                  ->where('process_status', 'success');
        });
        if($request->all()){
            if($request->date){
                $winners->where('draw_date',$request->date);
            }
            if($request->draw_time){
                $winners->where('draw_time',$request->draw_time);
            }
        } 
        $draw_types = DrawTypes::all();

        return view(
            'admin.winners.win',[
                'total_winers' => $winners->count(),
                'winners' => $winners->get(),
                'play' => '4D Claimed Winners',
                'draw_2pm' => $winners->where('draw_time','2PM')->count(),
                'draw_5pm' => $winners->where('draw_time','5PM')->count(),
                'draw_9pm' => $winners->where('draw_time','5PM')->count(),
                'draw_types' => $draw_types
            ]
        );
    }
}
