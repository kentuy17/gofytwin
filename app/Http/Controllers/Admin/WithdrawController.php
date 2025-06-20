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
 
    
  
    public function withdrawal(Request $request){
        if(auth()->user()->user_level == 0){

            return redirect('home');
        }
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

        return view(
            'admin.withdrawals.win',[
                'total_winers' => $winners->count(),
                'winners' => $winners->get(),
                'play' => 'Agent Withdrawal Requests',
            ]
        );
    }
    public function list(Request $request){
        if(auth()->user()->user_level < self::CSR){

            return redirect('home');
        }
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // ->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])

        $winners = Withdrawals::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->get();
        $draw_types = null;

        return view(
            'admin.withdrawals.trace',[
                'total_winers' => $winners->count(),
                'winners' => $winners,
                'play' => 'Agent Withdrawal List',
                'draw_types' => $draw_types
            ]
        );
    }
}
