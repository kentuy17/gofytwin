<?php
   
namespace App\Http\Controllers\API;
   
use DataTables;
use Carbon\Carbon;
use App\Models\User;
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
    public function winner_info_board(Request $request){
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $type = $request->play;
        $total_win_amount = 0;
        switch ($type) {
            case '2D':
                $total_bets = \App\Models\Bets2ds::where('user_id',$request->user_id)->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                break;

            case '3D':
                $total_bets = \App\Models\Bets3ds::where('user_id',$request->user_id)->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                break;
            case '4D':
                $total_bets = \App\Models\Bets4ds::where('user_id',$request->user_id)->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                break;
            case '6D':
                $total_bets = \App\Models\Bets6ds::where('user_id',$request->user_id)->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                break;
            
            case 'Pick 2D':
                $total_bets = \App\Models\Pick2ds::where('user_id',$request->user_id)->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                break;

            case '1D':
                $total_bets = \App\Models\Bets1ds::where('user_id',$request->user_id)->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
                break;
            
            default:
                # code...
                break;
        }

        $total_win_amount_1= \App\Models\Winners1ds::where('user_id', $request->user_id)->sum('winning_amount');
        $total_win_amount_2= \App\Models\Winners2ds::where('user_id', $request->user_id)->sum('winning_amount');
        $total_win_amount_3= \App\Models\Winners3ds::where('user_id', $request->user_id)->sum('winning_amount');
        $total_win_amount_4= \App\Models\Winners4ds::where('user_id', $request->user_id)->sum('winning_amount');
        $total_win_amount_6= \App\Models\Winners6ds::where('user_id', $request->user_id)->sum('winning_amount');
        $total_win_amount_pick_2= \App\Models\Winnerspick2ds::where('user_id', $request->user_id)->sum('winning_amount');
        $total_win_amount = $total_win_amount_1 + $total_win_amount_2 + $total_win_amount_3 + $total_win_amount_4 + $total_win_amount_6 + $total_win_amount_pick_2;
        
        
        
        // ->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
        // Cashin / total winnings x 100 - 100 = %
        // $profitability_1 = $total_bets / $cashin->won_amount;
        // $profitability_2 = $profitability_1 * 100;
        // $profitability = $profitability_2 - 100;
        

        $profitability = 100;

        if($total_win_amount > 0){
            $profitability_1 = $total_bets / $total_win_amount;
            $profitability_2 = $profitability_1 * 100;
                $profitability = $profitability_2 - 100;
        }
        $data['profitability'] = $profitability;
        $data['total_winnings'] = number_format($total_win_amount, 2, '.',',');
        $data['total_bets'] = number_format($total_bets, 2, '.',',');

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
            $gross1 = Bets1ds::where('user_id', $user_id)->sum('amount');
            $grosspick2 = Pick2ds::where('user_id', $user_id)->sum('amount');
            $gross2 = Bets2ds::where('user_id', $user_id)->sum('amount');
            $gross3 = Bets3ds::where('user_id', $user_id)->sum('amount');
            $gross4 = Bets4ds::where('user_id', $user_id)->sum('amount');
            $gross6 = Bets6ds::where('user_id', $user_id)->sum('amount');

            $gross = $gross1 + $grosspick2 + $gross2 + $gross3 + $gross4 + $gross6;


            $total_win_amount_1= Winners1ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->sum('winning_amount');
            $total_win_amount_2= Winners2ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->sum('winning_amount');
            $total_win_amount_3= Winners3ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->sum('winning_amount');
            $total_win_amount_4= Winners4ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->sum('winning_amount');
            $total_win_amount_6= Winners6ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->sum('winning_amount');
            $total_win_amount_pick_2= Winnerspick2ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->sum('winning_amount');
            $total_win_amount = $total_win_amount_1 + $total_win_amount_2 + $total_win_amount_3 + $total_win_amount_4 + $total_win_amount_6 + $total_win_amount_pick_2;

            $total_win_count_1 = Winners1ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->count();
            $total_win_count_2 = Winners2ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->count();
            $total_win_count_pick_2 = Winnerspick2ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->count();
            $total_win_count_3 = Winners3ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->count();
            $total_win_count_4 = Winners4ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->count();
            $total_win_count_6 = Winners6ds::where('user_id', $user_id)->where('process_status', 'success')->where('approved', '>', 0)->count();
            $total_win_count = $total_win_count_1 +$total_win_count_2 +$total_win_count_pick_2 +$total_win_count_3 +$total_win_count_4 +$total_win_count_6;

            
            $profitability = 100;

            if($total_win_amount > 0){
                switch ($request->play) {
                    case 'PICK1':
                        if($total_win_amount_1 > 0){
                            $profitability_1 = $gross1 / $total_win_amount_1;
                        } else {
                            $profitability_1 = 100;
                        }
                        
                        $total_win_amount = $total_win_amount_1;

                        break;
                    case 'PICK2':
                        if($total_win_amount_pick_2 > 0){
                            $profitability_1 = $grosspick2 / $total_win_amount_pick_2;
                        } else {
                            $profitability_1 = 100;
                        }
                        
                        $total_win_amount = $total_win_amount_pick_2;

                        break;
                    case '2D':
                        if($total_win_amount_2 > 0){
                            $profitability_1 = $gross2 / $total_win_amount_2;
                        } else {
                            $profitability_1 = 100;
                        }
                        
                        $total_win_amount = $total_win_amount_2;

                        break;
                    case '3D':
                        if($total_win_amount_3 > 0){
                            $profitability_1 = $gross3 / $total_win_amount_3;
                        } else {
                            $profitability_1 = 100;
                        }
                        
                        $total_win_amount = $total_win_amount_3;

                        break;
                    case '4D':
                        if($total_win_amount_4 > 0){
                            $profitability_1 = $gross4 / $total_win_amount_4;
                        } else {
                            $profitability_1 = 100;
                        }
                        
                        $total_win_amount = $total_win_amount_4;
                        
                        break;
                    case '6D':
                        if($total_win_amount_6 > 0){
                            $profitability_1 = $gross6 / $total_win_amount_6;
                        } else {
                            $profitability_1 = 100;
                        }
                        
                        $total_win_amount = $total_win_amount_6;
                        
                        break;
                    case 'spam':
                        if($total_win_amount > 0){
                            $profitability_1 = $gross / $total_win_amount;
                        } else {
                            $profitability_1 = 100;
                        }
                        
                        $total_win_amount = $total_win_amount;
                        
                        break;
                    default:
                        abort(403,'Invalid draw');
                    break;
                }
                
                $profitability_2 = $profitability_1 * 100;
                $profitability = $profitability_2 - 100;
            } 
            $data['gross'] = number_format($gross, 2, '.',',');
            $data['gross_pick1'] = number_format($gross1, 2, '.',',');
            $data['gross_pick2'] = number_format($grosspick2, 2, '.',',');
            $data['gross_2d'] = number_format($gross2, 2, '.',',');
            $data['gross_3d'] = number_format($gross3, 2, '.',',');
            $data['gross_4d'] = number_format($gross4, 2, '.',',');
            $data['gross_6d'] = number_format($gross6, 2, '.',',');
            $data['win_count'] = $total_win_count;
            $data['total_winnings'] = number_format($total_win_amount, 2, '.',',');
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
            $masterAgent = MasterAgents::where('user_id',auth()->user()->id)->first();
            $two_winners = Winners2ds::where('draw_date', date('Y-m-d'))->get();
            $pick_one_winners = Winners1ds::where('draw_date', date('Y-m-d'))->get();
            $pick_two_winners = Winnerspick2ds::where('draw_date', date('Y-m-d'))->get();
            $three_winners = Winners3ds::where('draw_date', date('Y-m-d'))->get();
            $four_winners = Winners4ds::where('draw_date', date('Y-m-d'))->get();
            if($masterAgent){
                foreach($pick_one_winners as $pick_one_winner){
                    $user_id = $pick_one_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['pick_one_winner']++;
                    }
                }
                foreach($pick_two_winners as $pick_two_winner){
                    $user_id = $pick_two_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['pick_two_winner']++;
                    }
                }
                foreach($two_winners as $two_winner){
                    $user_id = $two_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['two_winners']++;
                    }
                }
                foreach($three_winners as $three_winner){
                    $user_id = $three_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['three_winners']++;
                    }
                }
                foreach($four_winners as $four_winner){
                    $user_id = $four_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['four_winners']++;
                    }
                }
            } 
            $subAgent = SubAgents::where('user_id',auth()->user()->id)->first();
            if($subAgent){
                foreach($pick_one_winners as $pick_one_winner){
                    $user_id = $pick_one_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['pick_one_winner']++;
                    }
                }
                foreach($pick_two_winners as $pick_two_winner){
                    $user_id = $pick_two_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['pick_two_winner']++;
                    }
                }
                foreach($two_winners as $two_winner){
                    $user_id = $two_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['two_winners']++;
                    }
                }
                foreach($three_winners as $three_winner){
                    $user_id = $three_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['three_winners']++;
                    }
                }
                foreach($four_winners as $four_winner){
                    $user_id = $four_winner->user_id;
                    $found = AgentReportGrids::where('user_id',$user_id)->where('ma_id',auth()->user()->id)->count();
                    if($found > 0){
                        $data['four_winners']++;
                    }
                }
            }
            
            return $this->sendResponse($data, 'Response passed..');
        }
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

        $donel = 'donelstl.live';
        $donel = $this->getWebsiteCount($donel, $from, $to);

        $alphamax = 'alphamaxstl.live';
        $alphamax = $this->getWebsiteCount($alphamax, $from, $to);

        $betstl = 'betstl.live';
        $betstl = $this->getWebsiteCount($betstl, $from, $to);

        $diamondstl = 'diamondstl.live';
        $diamondstl = $this->getWebsiteCount($diamondstl, $from, $to);

        $luckstl = 'luckstl.live';
        $luckstl = $this->getWebsiteCount($luckstl, $from, $to);

        $onlinelottopro = 'onlinelottopro.live';
        $onlinelottopro = $this->getWebsiteCount($onlinelottopro, $from, $to);

        $onlinestl = 'onlinestl.live';
        $onlinestl = $this->getWebsiteCount($onlinestl, $from, $to);

        $saturnstl = 'saturnstl.live';
        $saturnstl = $this->getWebsiteCount($saturnstl, $from, $to);

        $stlonline = 'stlonline.live';
        $stlonline = $this->getWebsiteCount($stlonline, $from, $to);

        $swertres = 'swertres.live';
        $swertres = $this->getWebsiteCount($swertres, $from, $to);

        $pickstl = 'pickstl.live';
        $pickstl = $this->getWebsiteCount($pickstl, $from, $to);

        $silverlotto = 'silverlotto';
        $silverlotto = $this->getWebsiteCount($silverlotto, $from, $to);

        $suncove = 'suncove.live';
        $suncove = $this->getWebsiteCount($suncove, $from, $to);

        $appDL = 'goldlotto.live';
        $appDL = $this->getWebsiteCount($appDL, $from, $to);


        $data['donel'] = $donel;
        $data['alphamax'] = $alphamax;
        $data['onlinelottopro'] = $onlinelottopro;
        $data['betstl'] = $betstl;
        $data['diamondstl'] = $diamondstl;
        $data['luckstl'] = $luckstl;
        $data['onlinestl'] = $onlinestl;
        $data['saturnstl'] = $saturnstl;
        $data['stlonline'] = $stlonline;
        $data['swertres'] = $swertres;
        $data['pickstl'] = $pickstl;
        $data['silverlotto'] = $silverlotto;
        $data['suncove'] = $suncove;
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
            $all_user_id = [];
            $total_cashin = 0;
            $bets1d = DB::table('bets1ds')
                        ->select('id','user_id')
                        ->whereBetween('draw_date', [$from, $to])
                        ->get();

            foreach($bets1d as $bet1){
                if(! in_array($bet1->user_id, $all_user_id)){
                    array_push($all_user_id, $bet1->user_id);
                    $bets ++;
                }
            }

            $betspick2d = DB::table('pick2ds')
                        ->select('id','user_id')
                        ->whereBetween('draw_date', [$from, $to])
                        ->get();

            foreach($betspick2d as $betpick2d){
                if(! in_array($betpick2d->user_id, $all_user_id)){
                    array_push($all_user_id, $betpick2d->user_id);
                    $bets ++;
                }
            }

            $bets2d = DB::table('bets2ds')
                        ->select('id','user_id')
                        ->whereBetween('draw_date', [$from, $to])
                        ->get();
            foreach($bets2d as $bet2d){
                if(! in_array($bet2d->user_id, $all_user_id)){
                    array_push($all_user_id, $bet2d->user_id);
                    $bets ++;
                }
            }

            $bets3d = DB::table('bets3ds')
                    ->select('id','user_id')
                    ->whereBetween('draw_date', [$from, $to])
                    ->get();
            foreach($bets3d as $bet3d){
                if(! in_array($bet3d->user_id, $all_user_id)){
                    array_push($all_user_id, $bet3d->user_id);
                    $bets ++;
                }
            }

            $bets4d = DB::table('bets4ds')
                        ->select('id','user_id')
                        ->whereBetween('draw_date', [$from, $to])
                        ->get();
            foreach($bets4d as $bet4d){
                if(! in_array($bet4d->user_id, $all_user_id)){
                    array_push($all_user_id, $bet4d->user_id);
                    $bets ++;
                }
            }
        

            $bets6d = DB::table('bets6ds')
                        ->select('id','user_id')
                        ->whereBetween('draw_date', [$from, $to])
                        ->get();
            foreach($bets6d as $bet6d){
                if(! in_array($bet6d->user_id, $all_user_id)){
                    array_push($all_user_id, $bet6d->user_id);
                    $bets ++;
                }
            }

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

            # total bettors
            $total1dBettors = Bets1ds::whereBetween('draw_date', [$from, $to])->count();
            $totalpick2dBettors = Pick2ds::whereBetween('draw_date', [$from, $to])->count();
            $total2dBettors = Bets2ds::whereBetween('draw_date', [$from, $to])->count();
            $total3dBettors = Bets3ds::whereBetween('draw_date', [$from, $to])->count();
            $total4dBettors = Bets4ds::whereBetween('draw_date', [$from, $to])->count();
            $total6dBettors = Bets6ds::whereBetween('draw_date', [$from, $to])->count();
            $totalJaiBettors = JaiAlais::whereBetween('draw_date', [$from, $to])->count();

            //Daily won amount
            //Get total won amount
            $totalJaiAmount = Winnersjais::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
            $total1dAmount = Winners1ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
            $total1d2pmWonAmount = Winners1ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','2PM')
                    ->sum('winning_amount');
            $total1d5pmWonAmount = Winners1ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','5PM')
                    ->sum('winning_amount');
            $total1d9pmWonAmount = Winners1ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','9PM')
                    ->sum('winning_amount');
                
            #pick 2d        

            $totalpick2dAmount = Winnerspick2ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
            $totalpick2d2pmWonAmount = Winnerspick2ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','2PM')
                    ->sum('winning_amount');
            $totalpick2d5pmWonAmount = Winnerspick2ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','5PM')
                    ->sum('winning_amount');
            $totalpick2d9pmWonAmount = Winnerspick2ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','9PM')
                    ->sum('winning_amount');


            $total2dAmount = Winners2ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
            $total2d2pmWonAmount = Winners2ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','2PM')
                    ->sum('winning_amount');
            $total2d5pmWonAmount = Winners2ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','5PM')
                    ->sum('winning_amount');
            $total2d9pmWonAmount = Winners2ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','9PM')
                    ->sum('winning_amount');


            $total3dAmount = Winners3ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
            $total3d2pmWonAmount = Winners3ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','2PM')
                    ->sum('winning_amount');
            $total3d5pmWonAmount = Winners3ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','5PM')
                    ->sum('winning_amount');
            $total3d9pmWonAmount = Winners3ds::whereBetween('draw_date', [$from, $to])
                    ->where('draw_time','9PM')
                    ->sum('winning_amount');



            $total4dAmount = Winners4ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
            $total6dAmount = Winners6ds::whereBetween('draw_date', [$from, $to])
                ->sum('winning_amount');
            
            


            //play 2D
            $total_2d_2pm_bettors = DB::table('bets2ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->where('draw_time', '2PM')
                ->distinct('user_id')
                ->count();
            $total_2d_5pm_bettors = DB::table('bets2ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->where('draw_time', '5PM')
                ->distinct('user_id')
                ->count();
            $total_2d_9pm_bettors = DB::table('bets2ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->where('draw_time', '9PM')
                ->distinct('user_id')
                ->count();
            $total_2d_bettors = DB::table('bets2ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->distinct('user_id')
                ->count();

            //play 3D
            $total_3d_2pm_bettors = DB::table('bets3ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->where('draw_time', '2PM')
                ->distinct('user_id')
                ->count();
            $total_3d_5pm_bettors = DB::table('bets3ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->where('draw_time', '5PM')
                ->distinct('user_id')
                ->count();
            $total_3d_9pm_bettors = DB::table('bets3ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->where('draw_time', '9PM')
                ->distinct('user_id')
                ->count();
            $total_3d_bettors = DB::table('bets3ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->distinct('user_id')
                ->count();


            //play 4D
            $total_4d_bettors = DB::table('bets4ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->distinct('user_id')
                ->count();
            //play 6D
            $total_6d_bettors = DB::table('bets6ds')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->distinct('user_id')
                ->count();
            //play Jai Alai
            $total_jai_bettors = DB::table('jai_alais')
                ->select('id','user_id')
                ->whereBetween('draw_date', [$from, $to])
                ->distinct('user_id')
                ->count();


            $daily_won_amount = $total2dAmount + $total3dAmount + $total4dAmount + $total6dAmount + $totalpick2dAmount + $total1dAmount + $totalJaiAmount;

            #Jai Alai
            $data['daily_jai_won_amount'] = $totalpick2dAmount;
            // $data['daily_pick2_2pm_won_amount'] = $totalpick2d2pmWonAmount;
            // $data['daily_pick2_5pm_won_amount'] = $totalpick2d5pmWonAmount;
            // $data['daily_pick2_9pm_won_amount'] = $totalpick2d9pmWonAmount;

            #pick 2
            $data['daily_pick2_won_amount'] = $totalpick2dAmount;
            $data['daily_pick2_2pm_won_amount'] = $totalpick2d2pmWonAmount;
            $data['daily_pick2_5pm_won_amount'] = $totalpick2d5pmWonAmount;
            $data['daily_pick2_9pm_won_amount'] = $totalpick2d9pmWonAmount;

            #one digits
            $data['daily_1d_won_amount'] = $total1dAmount;
            $data['daily_1d_2pm_won_amount'] = $total1d2pmWonAmount;
            $data['daily_1d_5pm_won_amount'] = $total1d5pmWonAmount;
            $data['daily_1d_9pm_won_amount'] = $total1d9pmWonAmount;


            $data['daily_2d_won_amount'] = $total2dAmount;
            $data['daily_2d_2pm_won_amount'] = $total2d2pmWonAmount;
            $data['daily_2d_5pm_won_amount'] = $total2d5pmWonAmount;
            $data['daily_2d_9pm_won_amount'] = $total2d9pmWonAmount;



            $data['daily_3d_won_amount'] = $total3dAmount;
            $data['daily_3d_2pm_won_amount'] = $total3d2pmWonAmount;
            $data['daily_3d_5pm_won_amount'] = $total3d5pmWonAmount;
            $data['daily_3d_9pm_won_amount'] = $total3d9pmWonAmount;



            $data['daily_4d_won_amount'] = $total4dAmount;
            $data['daily_6d_won_amount'] = $total6dAmount;



            $data['daily_won_amount'] = number_format($daily_won_amount, 2, '.',',');
            $data['total_4d_bettors'] = $total_4d_bettors;
            $data['total_6d_bettors'] = $total_6d_bettors;
            $data['total_jai_bettors'] = $total_jai_bettors;

            $data['total_3d_9pm_bettors'] = $total_3d_9pm_bettors;
            $data['total_3d_2pm_bettors'] = $total_3d_2pm_bettors;
            $data['total_3d_5pm_bettors'] = $total_3d_5pm_bettors;
            $data['total_3d_bettors'] = $total_3d_bettors;

            $data['total_2d_9pm_bettors'] = $total_2d_9pm_bettors;
            $data['total_2d_2pm_bettors'] = $total_2d_2pm_bettors;
            $data['total_2d_5pm_bettors'] = $total_2d_5pm_bettors;
            $data['total_2d_bettors'] = $total_2d_bettors;
            $data['total_bets'] = number_format( $total2dBettors + $total3dBettors + $total4dBettors + $total6dBettors + $totalJaiBettors + $total1dBettors + $totalpick2dBettors,0,',');

            // $total_ci_promo_1000 = Promos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('promo_type', '1000_ci_amount')->sum('amount');
            // $total_ci_promo_1500 = Promos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('promo_type', '1500_ci_amount')->sum('amount');
            // $total_ci_promo_2000 = Promos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('promo_type', '2000_ci_amount')->sum('amount');
            // $total_ci_promo_2000 = Promos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('promo_type', '2000_ci_amount')->sum('amount');
            $total_double_promo = Promos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('promo_type', 'promo_double_amount')->sum('amount');
            $total_ci_promo = $total_double_promo;
            


            $data['total_users'] = number_format( User::count(),0,',');
            #gross amount
            $gross2d = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
            $gross2d2pm = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','2PM')
                            ->sum('amount');
            $gross2d5pm = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','5PM')
                            ->sum('amount');
            $gross2d9pm = Bets2ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','9PM')
                            ->sum('amount');

            #Pick 1
            $grossPick1 = Bets1ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
            $gross1d2pm = Bets1ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','2PM')
                            ->sum('amount');
            $gross1d5pm = Bets1ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','5PM')
                            ->sum('amount');
            $gross1d9pm = Bets1ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','9PM')
                            ->sum('amount');

            #Pick 2
            $grossPick2 = Pick2ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
            $grosspick22pm = Pick2ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','2PM')
                            ->sum('amount');
            $grosspick25pm = Pick2ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','5PM')
                            ->sum('amount');
            $grosspick29pm = Pick2ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','9PM')
                            ->sum('amount');



            $gross3d = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->sum('amount');
            $gross3d2pm = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','2PM')
                            ->sum('amount');
            $gross3d5pm = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','5PM')
                            ->sum('amount');
            $gross3d9pm = Bets3ds::whereBetween('draw_date', [$from, $to])
                            ->where('draw_time','9PM')
                            ->sum('amount');


            $gross4d = Bets4ds::whereBetween('draw_date', [$from, $to])
                                ->sum('amount');
            $gross6d = Bets6ds::whereBetween('draw_date', [$from, $to])
                                ->sum('amount');
            $gross6Jai = JaiAlais::whereBetween('draw_date', [$from, $to])
                                ->sum('amount');


            $givaways_amount = Promos::whereNot('promo_type','successful_otp')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                                ->sum('amount');
            

                                
            // $new_registers_total = $this->getDailyRegisters($from, $to);

            $data['givaways_amount'] = number_format($givaways_amount,0,',');
            
            #Pick 1
            $data['daily_1d_total_amount'] = number_format($grossPick1,0,',');
            $data['daily_1d_2pm_amount'] = number_format($gross1d2pm,0,',');
            $data['daily_1d_5pm_amount'] = number_format($gross1d5pm,0,',');
            $data['daily_1d_9pm_amount'] = number_format($gross1d9pm,0,',');

            #Pick 2
            $data['daily_pick2_total_amount'] = number_format($grossPick2,0,',');
            $data['daily_pick2_2pm_amount'] = number_format($grosspick22pm,0,',');
            $data['daily_pick2_5pm_amount'] = number_format($grosspick25pm,0,',');
            $data['daily_pick2_9pm_amount'] = number_format($grosspick29pm,0,',');



            $data['daily_2d_total_amount'] = number_format($gross2d,0,',');
            $data['daily_2d_2pm_amount'] = number_format($gross2d2pm,0,',');
            $data['daily_2d_5pm_amount'] = number_format($gross2d5pm,0,',');
            $data['daily_2d_9pm_amount'] = number_format($gross2d9pm,0,',');


            $data['daily_3d_total_amount'] = number_format($gross3d,0,',');
            $data['daily_3d_2pm_amount'] = number_format($gross3d2pm,0,',');
            $data['daily_3d_5pm_amount'] = number_format($gross3d5pm,0,',');
            $data['daily_3d_9pm_amount'] = number_format($gross3d9pm,0,',');


            $data['daily_4d_total_amount'] = number_format($gross4d,0,',');
            $data['daily_6d_total_amount'] = number_format($gross6d,0,',');
            $data['daily_jai_total_amount'] = number_format($gross6Jai,0,',');
                                
            
            $gross_amount = $gross2d + $gross3d + $gross4d + $gross6d + $grossPick2 + $grossPick1 + $gross6Jai;
            $gross_amount = number_format($gross_amount,2,'.',',');
            $data['gross_amount'] = $gross_amount;

            # withdrawal request
            $withdrawal_request = Withdrawals::whereNot('process_status','success')->count();
            $data['withdrawal_request'] = $withdrawal_request;

            # get master agent earnings
            //get all master agents
            //check all master agent bets amount
            // = total master agent
            // $ma_earnings = $topUpObj->where('ma_id','>',0)->sum('ma_amount');
            $total_ma_earnings = 0;
            $all_mas = MasterAgents::all();
            foreach($all_mas as $all_ma){
                $total_ma_earnings += $all_ma->total;
            }
            $data['master_agent_earnings'] = number_format($total_ma_earnings,2,'.',',');

            #get SA earnings
            $total_sa_earnings = 0;
            $all_sas = SubAgents::all();
            foreach($all_sas as $all_sa){
                $total_sa_earnings += $all_sa->total;
            }
            $data['sub_agent_earnings'] = number_format($total_sa_earnings,2,'.',',');


            #Agent commisions
            $agent_commision = $total_ma_earnings + $total_sa_earnings;
            $data['agent_commision'] = number_format($agent_commision,2,'.',',');

            $total_ma_count = MasterAgents::count();
            $total_sa_count = SubAgents::count();
            $total_no_agents = $total_ma_count + $total_sa_count;
            $data['total_no_agents'] = $total_no_agents;


            #Agents 
            $users = User::where('userlevel', 0)->whereNot('percentage',0);
            $total_agents = $users->count();
            $data['total_agents'] = $total_agents;


            #income = gross sa ila players dili sa cash ins. ipa appear ila percentage ->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
            // $new_registers_total = Promos::where('promo_type','successful_otp')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
            $promo_1000_ci_amount = Promos::where('promo_type','1000_ci_amount')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
            $PROMO_CI_1500 = Promos::where('promo_type','1500_ci_amount')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
            $PROMO_CI_2000 = Promos::where('promo_type','2000_ci_amount')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
            // $promo_double_amount = Promos::where('promo_type','promo_double_amount')->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->sum('amount');
            $divided_double_amount = $total_ci_promo / 2;

            $new_registers_total = $this->getDailyRegisters($from, $to);
            $total_sales = $gross2d + $gross3d + $gross4d + $gross6d + $gross6Jai;

            // $expenses = 0;
            // $new_registers_total = $new_registers_total * 20;    
            // $total_promos = $new_registers_total + $promo_1000_ci_amount + $PROMO_CI_1500 + $PROMO_CI_2000;
            // $total_won_amount = $total2dAmount + $total3dAmount + $total4dAmount + $total6dAmount;

            // $expenses = $total_promos + $total_won_amount + $total_sales;

            $expenses = 0;
            $new_registers_total1= 0;
            $new_registers_total1= $new_registers_total * 20;   
            $total_promos = $divided_double_amount;
            $total_won_amount = $total2dAmount + $total3dAmount + $total4dAmount + $total6dAmount;
            $expenses = $new_registers_total1 + $total_promos + $total_won_amount;

            $overallGross = $total_sales - $expenses;

           
            $data['total_ci_promo'] = number_format($divided_double_amount + $new_registers_total1,0,',');
            $data['overall_gross'] = number_format($overallGross,2,'.',',');
            $data['free_credits_amount'] = number_format($new_registers_total,0,',');

            $total2dWinners = Winners2ds::whereBetween('draw_date', [$from, $to])->count();
            $total3dWinners = Winners3ds::whereBetween('draw_date', [$from, $to])->count();
            $total4dWinners = Winners4ds::whereBetween('draw_date', [$from, $to])->count();
            $total6dWinners = Winners6ds::whereBetween('draw_date', [$from, $to])->count();
            
            $data['winners_2_g'] = $total2dWinners;
            $data['winners_3_g'] = $total3dWinners;
            $data['winners_4_g'] = $total4dWinners;
            $data['winners_6_g'] = $total6dWinners;


            #


            
            return $this->sendResponse($data, 'Response passed..');
        }
    }

    public function get_total_agent_earnings(Request $request){
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        #Agents 
        $total_agent_earnings = 0;
        $users = User::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('userlevel', 0)->whereNot('percentage',0)->get();
        foreach($users as $agent){
            $bet2 = Bets2ds::where('user_id',$agent->id)->sum('amount');
            $bet3 = Bets3ds::where('user_id',$agent->id)->sum('amount');
            $bet4 = Bets4ds::where('user_id',$agent->id)->sum('amount');
            $bet6 = Bets6ds::where('user_id',$agent->id)->sum('amount');
            $total_agent_earnings += $bet2 + $bet3 + $bet4 + $bet6;
        }
        $data['total_agent_earnings'] = number_format($total_agent_earnings,2,'.',',');


        

        return $this->sendResponse($data, 'Response passed..');
    }
    public function get_cashin_total(Request $request){
        if ($request->ajax()) {

            $cashin = Cashins::all()->where('draw_date',date('Y-m-d'))->where('status','success');
            $total = $cashin->unique('user_id')->count();
            
            $data['total_cashin'] = $total;
            
            return $this->sendResponse($data, 'Response passed..');
        }
    }
     
    public function pot_balance_6d(Request $request){
        if ($request->ajax()) {
            $balance = PotMoneySixDigits::find(1); 
            $balance = number_format($balance->amount, 2, '.',',');
            
            return $this->sendResponse($balance, 'Response passed..');
        }
    }
    public function pot_balance(Request $request){
        if ($request->ajax()) {
            $balance = PotMoney::find(1); 
            $balance = number_format($balance->amount, 2, '.',',');
            
            return $this->sendResponse($balance, 'Response passed..');
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