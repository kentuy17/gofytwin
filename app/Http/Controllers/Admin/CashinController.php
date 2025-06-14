<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\User;
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

class CashinController extends Controller
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

    public function csr_process(Request $request): JsonResponse
    {
        if(auth()->user()->userlevel < self::CSR){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        $id = $request->id;
        $record = Cashins::find($id);
        if($record){
            $ma_id = 0;
            $sa_id = 0;
            $ma_mount = 0;
            $sa_amount = 0;
            $user = User::find($record->user_id);
            if($user->is_referral > 0){
                $agentObj = AgentReportGrids::where('user_id', auth()->user()->id)->first();
                $ma_id = $agentObj->ma_id;
                $sa_id = $agentObj->sa_id;
                $ma_mount = $record->amount * $agentObj->ma_share;
                $sa_amount = $record->amount * $agentObj->sa_share;
            }

            $topup = Topups::where('merchant_order_num',$record->merchant_order_no)->first();
            if($topup){
                #get master agent / sub agent id
                #save to topups so we can track
                #calculations for ma earnings
                #calculations for sa earnings
                # check if user is referral
                $topup->amount = $record->amount;
                $topup->gateway = self::CI_MANUAL;
                // $topup->merchant_order_remark = self::SUCCESS_STATUS;
                $topup->status = self::SUCCESS_STATUS;
                $topup->sign = $record->merchant_order_no;
                $topup->save();

            }
            
            $credits = Credits::where('user_id',$record->user_id)->first();
            if($credits){
                $new_amount = $credits->amount + $record->amount;
                $credits->amount = $new_amount;
                $credits->save();
            }
 
            $record->status = 'success';
            $record->save();
            $full_data = [
                'user_id'=>auth()->user()->id,          
                'user_name'=>auth()->user()->name,          
                'action'=>self::CSR_APPROVAL,          
                'data'=>$request,          
              ];
            ActivityLogs::create($full_data);

            return $this->sendResponse('success', 'Record successfully updated.');
        }

        return $this->sendResponse('error', 'Record not found.');
    }
    public function csr(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','waiting')->where('gateway',self::CI_MANUAL);
        $draw_types = DrawTypes::all();
        $total_sales = $cashin->sum('amount');

        return view(
            'admin.cash-ins.csr',[
                'cashins' => $cashin->get(),
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function index(Request $request){
        if(auth()->user()->user_level < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $cashin = Cashins::select('id', 'merchant_order_no', 'user_id', 'gateway', 'amount', 'created_at')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success');
        $draw_types = [];
        $total_sales = $cashin->sum('amount');

        return view(
            'admin.cash-ins.index',[
                'cashins' => $cashin->get(),
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function net_income(Request $request){
        if(auth()->user()->user_level < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $cashin = Cashins::select('id', 'merchant_order_no', 'user_id', 'gateway', 'amount', 'created_at')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success');
        $draw_types = [];
        $total_sales = $cashin->sum('amount');

        return view(
            'admin.cash-ins.net-income',[
                'cashins' => $cashin->get(),
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function totak_gross(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            // return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $cashin = Cashins::select('id', 'merchant_order_no', 'user_id', 'gateway', 'amount', 'created_at')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success');
        $draw_types = [];
        $total_sales = $cashin->sum('amount');

        return view(
            'admin.cash-ins.total-gross',[
                'cashins' => $cashin->get(),
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function three(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT winners3ds.user_id, 
                        COUNT(winners3ds.user_id) as hits, 
                        SUM(winners3ds.winning_amount) as won_amount, 
                        users.mobile_no as mobile_no, 
                        users.account_status as acc_status, 
                        users.created_at  as created_at 
                    FROM
                        winners3ds  
                    LEFT JOIN users ON users.id=winners3ds.user_id 

                    WHERE  winners3ds.created_at >= '".$from." 00:00:00' AND winners3ds.created_at <= '".$to." 23:59:59' GROUP BY winners3ds.user_id,users.mobile_no,users.created_at,users.account_status ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.winners-board',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'admin_accounts' => $this->admin_accounts,
                'type' => '3D',
                'from' => $from,
                'to' => $to,
                'title' => '3D',
            ]
        );
    }
    public function four(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT winners4ds.user_id, 
                        COUNT(winners4ds.user_id) as hits, 
                        SUM(winners4ds.winning_amount) as won_amount, 
                        users.mobile_no as mobile_no, 
                        users.account_status as acc_status, 
                        users.created_at  as created_at 
                    FROM
                        winners4ds  
                    LEFT JOIN users ON users.id=winners4ds.user_id 

                    WHERE  winners4ds.created_at >= '".$from." 00:00:00' AND winners4ds.created_at <= '".$to." 23:59:59' GROUP BY winners4ds.user_id,users.mobile_no,users.created_at,users.account_status ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.winners-board',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'admin_accounts' => $this->admin_accounts,
                'type' => '4D',
                'from' => $from,
                'to' => $to,
                'title' => '4D',
            ]
        );
    }
    public function six(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT winners6ds.user_id, 
                        COUNT(winners6ds.user_id) as hits, 
                        SUM(winners6ds.winning_amount) as won_amount, 
                        users.mobile_no as mobile_no, 
                        users.account_status as acc_status, 
                        users.created_at  as created_at 
                    FROM
                        winners6ds  
                    LEFT JOIN users ON users.id=winners6ds.user_id 

                    WHERE  winners6ds.created_at >= '".$from." 00:00:00' AND winners6ds.created_at <= '".$to." 23:59:59' GROUP BY winners6ds.user_id,users.mobile_no,users.created_at,users.account_status ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.winners-board',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'admin_accounts' => $this->admin_accounts,
                'type' => '6D',
                'from' => $from,
                'to' => $to,
                'title' => '6D',
            ]
        );
    }
    public function one(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT winners1ds.user_id, 
                        COUNT(winners1ds.user_id) as hits, 
                        SUM(winners1ds.winning_amount) as won_amount, 
                        users.mobile_no as mobile_no, 
                        users.account_status as acc_status, 
                        users.created_at  as created_at 
                    FROM
                        winners1ds  
                    LEFT JOIN users ON users.id=winners1ds.user_id 

                    WHERE  winners1ds.created_at >= '".$from." 00:00:00' AND winners1ds.created_at <= '".$to." 23:59:59' GROUP BY winners1ds.user_id,users.mobile_no,users.created_at,users.account_status ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.winners-board',[
                'cashins' => $winners,
                'admin_accounts' => $this->admin_accounts,
                'total_sales' => $total_sales,
                'from' => $from,
                'to' => $to,
                'type' => 'PICK1',
                'title' => '1D',
            ]
        );
    }
    public function pick2(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT winnerspick2ds.user_id, 
                        COUNT(winnerspick2ds.user_id) as hits, 
                        SUM(winnerspick2ds.winning_amount) as won_amount, 
                        users.mobile_no as mobile_no, 
                        users.account_status as acc_status, 
                        users.created_at  as created_at 
                    FROM
                        winnerspick2ds  
                    LEFT JOIN users ON users.id=winnerspick2ds.user_id 

                    WHERE  winnerspick2ds.created_at >= '".$from." 00:00:00' AND winnerspick2ds.created_at <= '".$to." 23:59:59' GROUP BY winnerspick2ds.user_id,users.mobile_no,users.created_at,users.account_status ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.winners-board',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'admin_accounts' => $this->admin_accounts,
                'from' => $from,
                'to' => $to,
                'type' => 'PICK2',
                'title' => 'Pick 2D',
            ]
        );
    }
    public function two(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT winners2ds.user_id, 
                        COUNT(winners2ds.user_id) as hits, 
                        SUM(winners2ds.winning_amount) as won_amount, 
                        users.mobile_no as mobile_no, 
                        users.account_status as acc_status, 
                        users.created_at  as created_at 
                    FROM
                        winners2ds  
                    LEFT JOIN users ON users.id=winners2ds.user_id 

                    WHERE  winners2ds.created_at >= '".$from." 00:00:00' AND winners2ds.created_at <= '".$to." 23:59:59' GROUP BY winners2ds.user_id,users.mobile_no,users.created_at,users.account_status ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.winners-board',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'from' => $from,
                'to' => $to,
                'admin_accounts' => $this->admin_accounts,
                'type' => '2D',
                'title' => '2D',
            ]
        );
    }
    public function ci(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT cashins.user_id, 
                        users.mobile_no as mobile_no, 
                        COUNT(cashins.user_id) as hits,
                        SUM(cashins.amount) as won_amount, 
                        users.created_at  as created_at, 
                        users.account_status 
                    FROM
                        cashins  
                    LEFT JOIN users ON users.id=cashins.user_id 

                    WHERE  cashins.created_at >= '".$from." 00:00:00' AND cashins.created_at <= '".$to." 23:59:59' 
                    AND cashins.status='success' GROUP BY cashins.user_id,users.mobile_no,users.created_at,users.account_status ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.ci-board',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'from' => $from,
                'to' => $to,
                'title' => '2D',
            ]
        );
    }
    public function daily_active_bettors(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DailyActiveBettorGrid::whereBetween('draw_date', [$from." 00:00:00", $to." 23:59:59"])->get();

        $total_sales = 0;

        return view(
            'admin.reports.daily_active',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'from' => $from,
                'to' => $to,
                'title' => '2D',
            ]
        );
    }
    public function credits(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');

        $winners = DB::select("SELECT credits.user_id, 
                        users.mobile_no as mobile_no, 
                        credits.amount as won_amount, 
                        users.created_at  as created_at, 
                        users.account_status 
                    FROM
                        credits  
                    LEFT JOIN users ON users.id=credits.user_id 
                    WHERE credits.amount >=1 AND users.account_status != 'spam' ORDER BY won_amount");

        $total_sales = 0;

        return view(
            'admin.cash-ins.credits-board',[
                'cashins' => $winners,
                'total_sales' => $total_sales,
                'from' => $from,
                'to' => $to,
                'title' => '2D',
            ]
        );
    }
    public function weekly(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        // $from = isset($request->from) ? $request->from : date('Y-m-d');
        // $to = isset($request->to) ? $request->to : date('Y-m-d');
        
        

        if(isset($request->from)){
            $today = strtotime($request->from);
        } else {
            $today = time();
        }
        $dow = date('w', $today);
        $offset = $dow;
        if ($offset < 0) {
            $offset = 6;
        }
         //calculate timestamp for Suday and Saturday
         $monday = $today - ($offset * 86400);
         $tuesday = $monday + (1 * 86400);
         $wednesday = $monday + (2 * 86400);
         $thursday = $monday + (3 * 86400);
         $friday = $monday + (4 * 86400);
         $saturday = $monday + (5 * 86400);
         $sunday = $monday + (6 * 86400);

  

         //print dates for Suday and Saturday in the current week
         $sunday_date = date("Y-m-d", $monday);
         $monday_date = date("Y-m-d", $tuesday);
         $tuesday_date = date("Y-m-d", $wednesday);
         $wednesday_date = date("Y-m-d", $thursday);
         $thursday_date = date("Y-m-d", $friday);
         $friday_date = date("Y-m-d", $saturday);
         $saturday_date = date("Y-m-d", $sunday);

        
        //  $asdf = DB::select("SELECT ci.user_id, ci.status, ci.gateway, ci.amount, ci.created_at, u.mobile_no    
        //                 FROM 
        //                 cashins ci 
        //                 LEFT JOIN users u on u.id=ci.user_id 
        //                 WHERE ci.created_at >= '2024-06-16 00:00:00' AND ci.created_at <= '2024-06-22 11:59:59' AND ci.status='success' GROUP BY ci.user_id");
        
         $cashins = DB::select("SELECT distinct ci.user_id       
                        FROM 
                        cashins ci 
                        LEFT JOIN users u on u.id=ci.user_id 
                        WHERE ci.created_at >= '".$sunday_date." 00:00:00' AND ci.created_at <= '".$saturday_date." 23:59:59' AND ci.status='success' GROUP BY ci.user_id");
                        // foreach($cashins as $asd){
                        //     echo $asd->user_id."<br>";
                        // }
        $draw_types = DrawTypes::all();
        $total_sales = 0;

        return view(
            'admin.cash-ins.weekly',[
                'cashins' => $cashins,
                'sunday_date' => $sunday_date,
                'monday_date' => $monday_date,
                'tuesday_date' => $tuesday_date,
                'wednesday_date' => $wednesday_date,
                'thursday_date' => $thursday_date,
                'friday_date' => $friday_date,
                'saturday_date' => $saturday_date,
 
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function bi_weekly(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        // $from = isset($request->from) ? $request->from : date('Y-m-d');
        // $to = isset($request->to) ? $request->to : date('Y-m-d');
        
        

        if(isset($request->from)){
            $today = strtotime($request->from);
        } else {
            $today = time();
        }
        $dow = date('w', $today);
        $offset = $dow;
        if ($offset < 0) {
            $offset = 6;
        }
         //calculate timestamp for Suday and Saturday
         $monday = $today - ($offset * 86400);
         $tuesday = $monday + (1 * 86400);
         $wednesday = $monday + (2 * 86400);
         $thursday = $monday + (3 * 86400);
         $friday = $monday + (4 * 86400);
         $saturday = $monday + (5 * 86400);
         $sunday = $monday + (6 * 86400);

         $next_monday = $monday + (-1 * 86400);
         $next_tuesday = $monday + (-2 * 86400);
         $next_wednesday = $monday + (-3 * 86400);
         $next_thursday = $monday + (-4 * 86400);
         $next_friday = $monday + (-5 * 86400);
         $next_saturday = $monday + (-6 * 86400);
         $next_sunday = $monday + (-7 * 86400);

         //print dates for Suday and Saturday in the current week
         $sunday_date = date("Y-m-d", $monday);
         $monday_date = date("Y-m-d", $tuesday);
         $tuesday_date = date("Y-m-d", $wednesday);
         $wednesday_date = date("Y-m-d", $thursday);
         $thursday_date = date("Y-m-d", $friday);
         $friday_date = date("Y-m-d", $saturday);
         $saturday_date = date("Y-m-d", $sunday);

         $prev_saturday = date("Y-m-d", $next_monday);
         $prev_friday = date("Y-m-d", $next_tuesday);
         $prev_thursday = date("Y-m-d", $next_wednesday);
         $prev_wednesday = date("Y-m-d", $next_thursday);
         $prev_tuesday = date("Y-m-d", $next_friday);
         $prev_monday = date("Y-m-d", $next_saturday);
         $prev_sunday = date("Y-m-d", $next_sunday);

        // dd($saturday_date);
        //  $asdf = DB::select("SELECT ci.user_id, ci.status, ci.gateway, ci.amount, ci.created_at, u.mobile_no    
        //                 FROM 
        //                 cashins ci 
        //                 LEFT JOIN users u on u.id=ci.user_id 
        //                 WHERE ci.created_at >= '2024-06-16 00:00:00' AND ci.created_at <= '2024-06-22 11:59:59' AND ci.status='success' GROUP BY ci.user_id");
        
         $cashins = DB::select("SELECT distinct ci.user_id       
                        FROM 
                        cashins ci 
                        LEFT JOIN users u on u.id=ci.user_id 
                        WHERE ci.created_at >= '".$prev_sunday." 00:00:00' AND ci.created_at <= '".$saturday_date." 23:59:59' AND ci.status='success' GROUP BY ci.user_id");
                        // foreach($cashins as $asd){
                        //     echo $asd->user_id."<br>";
                        // }
        $draw_types = DrawTypes::all();
        $total_sales = 0;

        return view(
            'admin.cash-ins.bi-weekly',[
                'cashins' => $cashins,
                'sunday_date' => $sunday_date,
                'monday_date' => $monday_date,
                'tuesday_date' => $tuesday_date,
                'wednesday_date' => $wednesday_date,
                'thursday_date' => $thursday_date,
                'friday_date' => $friday_date,
                'saturday_date' => $saturday_date,


                'next_sunday_date' => $prev_sunday,
                'next_monday_date' => $prev_monday,
                'next_tuesday_date' => $prev_tuesday,
                'next_wednesday_date' => $prev_wednesday,
                'next_thursday_date' => $prev_thursday,
                'next_friday_date' => $prev_friday,
                'next_saturday_date' => $prev_saturday,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function top(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->orderBy('amount', 'DESC');
        // $cashin = Cashins::where('status','success')->orderBy('amount', 'DESC');
        $cashin = DB::select('SELECT 
        cashins.user_id,
        users.mobile_no, 
        users.percentage, 
        users.account_status, 
        users.created_at, 
           
        SUM(amount) AS total_ci
    FROM
        cashins 
        LEFT JOIN users ON users.id=cashins.user_id 
         WHERE status="success"
    GROUP BY user_id, mobile_no, users.percentage, account_status, created_at   
    HAVING SUM(amount) ORDER BY total_ci desc  LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.cash-ins.top',[
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function winners_top_two_digits(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->orderBy('amount', 'DESC');
        // $cashin = Cashins::where('status','success')->orderBy('amount', 'DESC');
        $cashin = DB::select('SELECT 
                    user_id, 
                    contact_no, 

                    SUM(winning_amount) AS total_won
                FROM 
                    winners2ds 
                GROUP BY user_id,contact_no 
                HAVING SUM(winning_amount) ORDER BY total_won DESC LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.winners.top-winners',[
                'win_title' => 'Winners 2D',
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function winners_top_jai(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        $cashin = DB::select('SELECT 
                    user_id, 
                    contact_no, 

                    SUM(winning_amount) AS total_won
                FROM 
                    winnersjais 
                GROUP BY user_id,contact_no 
                HAVING SUM(winning_amount) ORDER BY total_won DESC LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.winners.top-winners',[
                'win_title' => 'Winners Jai Alai',
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function winners_top_three_digits(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->orderBy('amount', 'DESC');
        // $cashin = Cashins::where('status','success')->orderBy('amount', 'DESC');
        $cashin = DB::select('SELECT 
                    user_id, 
                    contact_no, 

                    SUM(winning_amount) AS total_won
                FROM 
                    winners3ds 
                GROUP BY user_id,contact_no 
                HAVING SUM(winning_amount) ORDER BY total_won DESC LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.winners.top-winners',[
                'win_title' => 'Winners 3D',
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function winners_top_one_digits(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->orderBy('amount', 'DESC');
        // $cashin = Cashins::where('status','success')->orderBy('amount', 'DESC');
        $cashin = DB::select('SELECT 
                    user_id, 
                    contact_no, 

                    SUM(winning_amount) AS total_won
                FROM 
                    winners1ds 
                GROUP BY user_id,contact_no 
                HAVING SUM(winning_amount) ORDER BY total_won DESC LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.winners.top-winners',[
                'win_title' => 'Winners 1D',
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function winners_top_one_pick2(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->orderBy('amount', 'DESC');
        // $cashin = Cashins::where('status','success')->orderBy('amount', 'DESC');
        $cashin = DB::select('SELECT 
                    user_id, 
                    contact_no, 

                    SUM(winning_amount) AS total_won
                FROM 
                    winnerspick2ds 
                GROUP BY user_id,contact_no 
                HAVING SUM(winning_amount) ORDER BY total_won DESC LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.winners.top-winners',[
                'win_title' => 'Winners Pick 2',
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function winners_top_four_digits(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->orderBy('amount', 'DESC');
        // $cashin = Cashins::where('status','success')->orderBy('amount', 'DESC');
        $cashin = DB::select('SELECT 
                    user_id, 
                    contact_no, 

                    SUM(winning_amount) AS total_won
                FROM 
                    winners4ds 
                    LEFT JOIN users ON users.id=winners4ds.user_id 
                GROUP BY user_id,contact_no 
                HAVING SUM(winning_amount) ORDER BY total_won DESC LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.winners.top-winners',[
                'win_title' => 'Winners 4D',
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function winners_top_six_digits(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // $cashin = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->orderBy('amount', 'DESC');
        // $cashin = Cashins::where('status','success')->orderBy('amount', 'DESC');
        $cashin = DB::select('SELECT 
                    user_id, 
                    contact_no, 

                    SUM(winning_amount) AS total_won
                FROM 
                    winners6ds 
                    LEFT JOIN users ON users.id=winners6ds.user_id 
                GROUP BY user_id,contact_no 
                HAVING SUM(winning_amount) ORDER BY total_won DESC LIMIT 50');
        $draw_types = DrawTypes::all();
        $total_sales = 100;

        return view(
            'admin.winners.top-winners',[
                'win_title' => 'Winners 6D',
                'cashins' => $cashin,
                'total_sales' => $total_sales,
                'draw_types' => $draw_types
            ]
        );
    }
    public function userCashin(Request $request){
        if(auth()->user()->userlevel < self::CSR){

            return redirect('home');
        }
        // $bets
        $user = User::find($request->id);
        $cashin = Log::where('user_id',$request->id)->orderBy('created_at', 'desc');
        $draw_types = DrawTypes::all();
        $total_sales = $cashin->sum('amount');

        return view(
            'admin.cash-ins.user',[
                'cashins' => $cashin->get(),
                'total_sales' => $total_sales,
                'user' => $user,
                'callback' => new Callbacks,
                'draw_types' => $draw_types
            ]
        );
    }
}
