<?php

namespace App\Http\Controllers\API;

use DataTables;
use Carbon\Carbon;
use App\Models\Bet;
use App\Models\Bets;
use App\Models\User;
use App\Models\Fight;
use App\Models\Promos;
use App\Models\Topups;
use App\Models\Bets1ds;
use App\Models\Bets2ds;
use App\Models\Bets3ds;
use App\Models\Bets4ds;
use App\Models\Bets6ds;
use App\Models\Cashins;
use App\Models\Credits;
use App\Models\Pick2ds;
use App\Models\UserCis;
use App\Models\JaiAlais;
use App\Models\PotMoney;
use App\Models\Callbacks;
use App\Models\SubAgents;
use App\Models\BetHistory;
use App\Models\Winners1ds;
use App\Models\Winners2ds;
use App\Models\Winners3ds;
use App\Models\Winners4ds;
use App\Models\Winners6ds;
use App\Models\CashinGrids;
use App\Models\Winnersjais;
use App\Models\Withdrawals;
use App\Models\CheckBalance;
use App\Models\MasterAgents;
use App\Models\TimeSchedule;
use Illuminate\Http\Request;
use App\Models\Winnerspick2ds;
use App\Models\AgentReportGrids;
use App\Models\PotMoneySixDigits;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CashinResource;
use App\Http\Controllers\API\BaseController as BaseController;

class ServerInfoController extends BaseController
{
    const COMPANY_SHARE = 0.10;
    const INACTIVE = 40;
    const NEW_REGISTERS = 20;
    public function serverTime2(Request $request){
        if ($request->ajax()) {
            # Create anchor time and another date time to be compared
            $anchorTime = Carbon::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:00:00"));
            $currentTime = Carbon::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
            # count difference in minutes
            $timeDiff = $anchorTime->diff($currentTime);
            $minuteDiff = 59 - $timeDiff->i;
            $secondDiff = 59 - $timeDiff->s;
            $_result = new TimeSchedule();
            $drawResult = $_result->getDisplayResult();

            $data['time'] = date("h:iA");
            $data['draw_result'] = $drawResult;
            $data['countdown_m'] = $minuteDiff;
            $data['countdown_s'] = $secondDiff;
            $data['minuteDiff'] = $timeDiff->i;
            $data['secondDiff'] = $timeDiff->s;

            $sched = new TimeSchedule();
            $countdownTime = $sched->getCountdownTime();
            $numPadded = sprintf("%02d", $countdownTime['countdown_m']).":".sprintf("%02d", $countdownTime['countdown_s']);
            $data['num_padded'] = $numPadded;

            return $this->sendResponse($data, 'Response passed..');
        }
    }






    public function serverTime(Request $request){
        if ($request->ajax()) {

            return $this->sendResponse(date("h:iA"), 'Response passed..');
        }
    }
    public function balance(Request $request){
        if ($request->ajax()) {
            $balance = User::find($request->user_id);
            $balance = number_format($balance->credits->amount, 2, '.',',');

            return $this->sendResponse($balance, 'Response passed..');
        }
    }
    public function user_ci(Request $request){
        $data = [];
        $orderData = Callbacks::where('merchant_order_num',$request->id)->get();
        $successful_ci = 0;
        foreach ($orderData as $orderData) {
            $dates = date_create($orderData->created_at);
            $timestamp = date_format($dates,'M d, Y h:iA');
            $order_status = json_decode($orderData->order,true);
            $state = isset($order_status['status'])?$order_status['status']:$request->status;
            // echo 'Action: '.'<strong>'.$data->action.'</strong>'.' | '.'Status: '.'<strong>'.$state.'</strong>'.
            // ' | '.'Date: '.'<strong>'.$timestamp.'</strong>'."<br>";


            if(isset($order_status['status']) && $order_status['status'] == 'success'){
                $successful_ci += $request->amount;
            }
            if(isset($order_status['status']) && $order_status['status'] == '5'){
                $successful_ci += $request->amount;
            }

            $data[$request->id]['successful_ci'] = $successful_ci;
            $data[$request->id]['action'] = $orderData->action;
            $data[$request->id]['timestamp'] = $timestamp;
            $data[$request->id]['state'] = $order_status['status'];
            $data[$request->id]['order_status'] = $order_status;
            $data[$request->id]['record_id'] = $request->id;
        }
        // $data['total'] = $successful_ci;

        return $this->sendResponse($data, 'Response passed..');

    }
    public function company_share_info_board_birth(Request $request){
        $from = isset($request->from) ? $request->from : date('Y-m-d');

        $totalEarnings = 0;
        $fights = Fight::all();
        foreach($fights as $fight){
            if($fight->game_winner == 'M' || $fight->game_winner == 'W'){ //there is winning side already
                $fightBets = BetHistory::where('fight_id', $fight->id)->get();
                foreach($fightBets as $bet){
                    $totalEarnings += $bet->amount;
                }
            }
        }
        $total = $totalEarnings * self::COMPANY_SHARE;
        $data['overall_company_share'] = number_format($total, 2, '.',',');

        return $this->sendResponse($data, 'Response passed..');
    }
    public function company_share_info_board(Request $request){
        $from = isset($request->from) ? $request->from : date('Y-m-d');

        $totalEarnings = 0;
        $fights = Fight::whereBetween('created_at', [$from." 00:00:00", $from." 23:59:59"])->get();
        foreach($fights as $fight){
            if($fight->game_winner == 'M' || $fight->game_winner == 'W'){
                $fightBets = BetHistory::where('fight_no', $fight->fight_no)->whereBetween('created_at', [$from." 00:00:00", $from." 23:59:59"])->get();
                foreach($fightBets as $bet){
                    $totalEarnings += $bet->amount;
                }
            }
        }
        $total = $totalEarnings * self::COMPANY_SHARE;
        $data['daily_company_share'] = number_format($total, 2, '.',',');

        return $this->sendResponse($data, 'Response passed..');
    }
    public function winner_info_board(Request $request){
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $type = $request->play;
        $total_bets = 0;
        $company_share = 0;
        $total_meron = 0;
        $total_wala = 0;
        $total_win_amount = 0;
        $total_pool = 0;
        $house_cut = 0;
        $net_pool = 0;
        $payout_rate = 0;
        $win_player_return = 0;
        $multiplied = 0;
        $win_player_profit = 0;
        $fight = Fight::where('id',$request->record_id)->first();
        switch ($type) {
            case 'players':
                if($fight){
                    // $total_bets = BetHistory::where('fight_id',$request->record_id)->where('side','M')->orWhere('side','W')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                    $total_meron = BetHistory::where('fight_id',$request->record_id)->where('side','M')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                    $total_wala = BetHistory::where('fight_id',$request->record_id)->where('side','W')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                    if($fight->game_winner == 'W' || $fight->game_winner == 'M'){
                        $company_share = $total_meron + $total_wala;
                        $total_bets += $company_share;
                        $total_pool += $total_meron + $total_wala;
                        $house_cut += $total_pool * self::COMPANY_SHARE;
                        $net_pool += $total_pool - $house_cut;
                        if($fight->game_winner = 'M'){
                            if($total_meron == 0){
                                $payout_rate = 0;
                            } else {
                                $divider = $net_pool / $total_meron;
                                $payout_rate += $divider * 100;
                            }
                        }
                        if($fight->game_winner = 'W'){
                            if($total_wala == 0){
                                $payout_rate = 0;
                            } else {
                                $divider = $net_pool / $total_wala;
                                $payout_rate += $divider * 100;
                            }
                        }

                        $multiplied = $total_pool * $payout_rate;
                        $win_player_return += $multiplied / 100;

                        $win_player_profit += $win_player_return - $total_pool;
                    }
                }
                break;
            case 'bots_playes':
                $total_bets = Bet::where('user_id',$request->record_id)->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                break;
            default:
                # code...
                break;
        }
        $data['total_pool'] = number_format($total_pool, 2, '.', ',');
        $data['house_cut'] = number_format($house_cut, 2, '.', ',');
        $data['net_pool'] = number_format($net_pool, 2, '.', ',');
        $data['payout_rate'] = number_format($payout_rate, 2, '.', '');
        $data['win_player_return'] = number_format($win_player_return, 2, '.', ',');
        $data['win_player_profit'] = number_format($win_player_profit, 2, '.', ',');


        $data['total_meron'] = "₱".number_format($total_meron, 2, '.', ',');
        $data['total_wala'] = "₱".number_format($total_wala, 2, '.', ',');
        $data['company_share'] = "₱".number_format($company_share * self::COMPANY_SHARE, 2, '.', ',');
        $data['total_winnings'] = number_format($total_win_amount, 2, '.',',');
        // $data['total_bets'] = number_format($total_bets, 2, '.',',');
        $data['total_bets'] = $total_bets;

        return $this->sendResponse($data, 'Response passed..');
    }
    public function cashin_info_board(Request $request){
        $from = isset($request->from) ? Carbon::createFromDate($request->from) : Carbon::createFromDate(date('Y-m-d'));
        $to = isset($request->to) ? Carbon::createFromDate($request->to) : Carbon::createFromDate(date('Y-m-d'));
        // dd($from->format('Y-m-d')." 00:00:00");

        $search = $request->search['value'];
        if($search){
            $readers = CashinGrids::query()
            ->when($search, function ($query) use ($search) {
                $query->where('user_id', 'like', '%' . $search . '%');
                $query->orWhere('mobile_no', 'like', '%' . $search . '%');
                $query->orWhere('merchant_order_no', 'like', '%' . $search . '%');
                $query->orWhere('sign', 'like', '%' . $search . '%');
                $query->orWhere('gateway', 'like', '%' . $search . '%');
                $query->orderBy('created_at', 'desc');
            })
            ->whereBetween('created_at', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"])
            ->where('status', 'success')
            ->paginate($request->per_page ?? 1000);
        } else {
            $readers = CashinGrids::query()
            ->whereBetween('created_at', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"])
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 1000);
        }


        return CashinResource::collection($readers);
        // $data = CashinResource::collection($readers);

        // return DataTables::of($data)->make(true);
    }
    public function winner_info(Request $request){
        if(!auth()->user()->id){

            return $this->sendResponse('error', 'Error');
        }
        if ($request->ajax()) {
            $user_id = $request->id;
            $total_ci = Cashins::where('user_id', $user_id)->where('status', 'success')->sum('amount');
            $gross = 0;
            $profitability = 100;

            $data['gross'] = number_format($gross, 2, '.',',');
            $data['win_count'] = 0;
            $data['total_winnings'] = number_format(0, 2, '.',',');
            $data['profitability'] = $profitability;
            $data['total_ci'] = number_format($total_ci, 2, '.',',');

            return $this->sendResponse($data, 'Response passed..');
        }
    }
    public function check_winners(Request $request){
        if(!auth()->user()->id){

            return $this->sendResponse('error', 'Error');
        }
        if ($request->ajax()) {
            $data['two_winners'] = 0;
            $data['three_winners'] = 0;
            $data['four_winners'] = 0;

            return $this->sendResponse($data, 'Response passed..');
        }
    }
    public function check_credits(Request $request){
        $user = User::find($request->user_id);

        return $this->sendResponse($user->credits, 'Response passed..');
    }
    public function check_balance(Request $request){
        if ($request->ajax()) {
            $balance = new CheckBalance;
            $bal = $balance->getBalance($request);

            return $this->sendResponse($bal, 'Response passed..');
        }
    }
    public function getDailyRegisters($from, $to){
        $daily_new_registers = DB::table('users')
            ->join('credits', 'users.id', '=', 'credits.user_id')
            ->whereNot('users.website_url',null)
            ->whereBetween('users.created_at', [$from." 00:00:00", $to." 23:59:59"])
            ->count();

        return $daily_new_registers;
    }
    public function getWebsiteCount($search,$from,$to){

        return User::where('website_register','LIKE',"%{$search}%")->whereBetween('users.created_at', [$from." 00:00:00", $to." 23:59:59"])->count();
    }
    public function get_inactive(Request $request){
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $daily_new_registers = $this->getDailyRegisters($from, $to);

        $inactive = Credits::where('amount',self::INACTIVE)->count();
        $new_registers = Credits::where('amount',self::NEW_REGISTERS)->count();

        $users = DB::select("SELECT cashins.user_id,
                    COUNT(cashins.user_id) as hits,
                    users.mobile_no as mobile_no,
                    users.website_register as website_register,
                    users.created_at  as created_at
                FROM
                    users
                LEFT JOIN cashins ON users.id=cashins.user_id

                WHERE  users.created_at >= '".$from." 00:00:00' AND users.created_at <= '".$to." 23:59:59' GROUP BY users.mobile_no,users.created_at,users.website_register,cashins.user_id ORDER BY created_at desc");
        $nap = 0;
        $nip = 0;
        foreach($users as $user):
            if($user->hits > 0){
                $successCi = \App\Models\Cashins::select('status','user_id')->where('status', 'success')->where('user_id', $user->user_id)->count();
                if($successCi > 0){
                    // $status = 'active';
                    $nap++;
                }else{
                    $nip++;
                }
            }else {
                $nip++;
            }
        endforeach;
        $appDL = 'goldlotto.live';
        $appDL = $this->getWebsiteCount($appDL, $from, $to);
        $data['app_dl'] = $appDL;
        $data['nip'] = $nip;
        $data['nap'] = $nap;
        $data['trp'] = $nap + $nip;
        $data['inactive_players'] = $inactive;
        $data['inactive_players'] = $inactive;
        $data['new_registers'] = $new_registers;
        $data['daily_new_registers'] = $daily_new_registers;

        return $this->sendResponse($data, 'Response passed..');
    }
    public function get_bettors(Request $request){
        if ($request->ajax()) {
            // dd($request->from);
            $from = isset($request->from) ? $request->from : date('Y-m-d');
            $to = isset($request->to) ? $request->to : date('Y-m-d');
            $bets = 0;
            $total_cashin = 0;

            $cashins = DB::table('cashins')
                        ->select('id','user_id')
                        ->where('status', 'success')
                        ->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                        ->get();
            $total_cashin += $cashins->count();
            $data['total_bettors'] = $bets;
            $data['total_cashin'] = $total_cashin;
            #daily CI
            $daily_ci = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->sum('amount');
            #daily CI PG1
            $daily_ci_pg_1 = Topups::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->where('merchant_order_remark','pg_1')->count();
            #daily CI PG3
            $daily_ci_pg_3 = Topups::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->where('merchant_order_remark','pg_3')->count();
            #daily CI PG4 (JDPay)
            $daily_ci_pg_4 = Topups::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->where('merchant_order_remark','pg_4')->count();
            #daily CI MANUAL
            $daily_ci_pg_manual = Topups::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->where('merchant_order_remark','manual')->count();
            $data['pg_1_usercount'] = $daily_ci_pg_1;
            $data['pg_3_usercount'] = $daily_ci_pg_3;
            $data['pg_4_usercount'] = $daily_ci_pg_4;
            $data['pg_manual_usercount'] = $daily_ci_pg_manual;
            $data['ci_usercount'] = $daily_ci_pg_1 + $daily_ci_pg_3 + $daily_ci_pg_4;


            $daily_ci_adjusted_deduction = $daily_ci * 0.04;
            $daily_ci_adjusted = $daily_ci - $daily_ci_adjusted_deduction;

            $data['daily_ci'] = number_format($daily_ci,2,'.',',');
            $data['daily_ci_adjusted'] = number_format($daily_ci_adjusted,2,'.',',');

            //Daily won amount


            $daily_won_amount = 0;
            $data['daily_won_amount'] = number_format($daily_won_amount, 2, '.',',');
            $data['total_bets'] = number_format(0,0,',');
            $data['total_users'] = number_format( User::count(),0,',');
            $data['givaways_amount'] = number_format(0,0,',');
            $gross_amount = number_format(0,2,'.',',');
            $data['gross_amount'] = $gross_amount;
            # withdrawal request
            $withdrawal_request = Withdrawals::whereNot('process_status','success')->count();
            $data['withdrawal_request'] = $withdrawal_request;

            return $this->sendResponse($data, 'Response passed..');
        }
    }

    public function get_cashin_total(Request $request){
        if ($request->ajax()) {

            $cashin = Cashins::all()->where('draw_date',date('Y-m-d'))->where('status','success');
            $total = $cashin->unique('user_id')->count();

            $data['total_cashin'] = $total;

            return $this->sendResponse($data, 'Response passed..');
        }
    }



    public function resend_otp(Request $request){
        if ($request->ajax()) {
            $balance = PotMoney::find(1);
            $balance = number_format($balance->amount, 2, '.',',');

            return $this->sendResponse($balance, 'Response passed..');
        }
    }
}
