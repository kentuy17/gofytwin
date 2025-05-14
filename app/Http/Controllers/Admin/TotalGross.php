<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\User;
use App\Models\Fight;
use App\Models\Topups;
use App\Models\Cashins;
use App\Models\Credits;
use App\Models\Callbacks;
use App\Models\DrawTypes;
use App\Models\Winners3ds;
use App\Models\ActivityLogs;
use Illuminate\Http\Request;
use App\Models\AgentReportGrids;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DailyActiveBettorGrid;

class TotalGross extends Controller
{
    const SUCCESS_STATUS = 'success';

    public $admin_accounts = [
        '15368',
        '39132',
        '85172',
        '85171',
        '68116',
        '52992',
        '68416',
        '68403',
        '62204',
        '68421',
        '68847',
        '15659',
        '356',
        '16546',
        '40556',
        '53018',
    ];

    
 
 
    public function total_gross(Request $request){
        if(auth()->user()->user_level < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // dd($from);

        // $winners = DB::select("SELECT fights.fight_no, 
        //                 fights.status, 
        //                 fights.game_winner, 
        //                 fights.event_id, 
        //                 fights.created_at 
        //             FROM
        //                 fights  
        //             LEFT JOIN bet_histories ON fights.fight_no=bet_histories.fight_id 

        //             WHERE  bet_histories.created_at >= '".$from." 00:00:00' AND bet_histories.created_at <= '".$to." 23:59:59'  ORDER BY fight_no");
        $fights = Fight::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->orderBy('id', 'DESC')->get();

        $total_sales = 0;

        return view(
            'admin.reports.gross',[
                'fights' => $fights,
                'total_sales' => $total_sales,
                'admin_accounts' => $this->admin_accounts,
                'type' => '3D',
                'from' => $from,
                'to' => $to,
                'title' => '3D',
            ]
        );
    }
}
