<?php

namespace App\Http\Controllers;



use Carbon\Carbon;
use App\Models\ActivityLogs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redis;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    const DECLARE_2D_WINNING_COMBINATION = 'declare_2d_winning_combination';
    const PUBLISH_2D_WINNING_COMBINATION = 'publish_2d_winning_combination';


    const DECLARE_3D_WINNING_COMBINATION = 'declare_3d_winning_combination';
    const PUBLISH_3D_WINNING_COMBINATION = 'publish_3d_winning_combination';

    const DECLARE_4D_WINNING_COMBINATION = 'declare_4d_winning_combination';
    const PUBLISH_4D_WINNING_COMBINATION = 'publish_4d_winning_combination';

    const DECLARE_6D_WINNING_COMBINATION = 'declare_6d_winning_combination';
    const PUBLISH_6D_WINNING_COMBINATION = 'publish_6d_winning_combination';

    const DELETE_WINNER = 'delete_winner';
    const RESET_WINNER = 'reset_winner';
    const COMPLETE_WINNER = 'complete_winner';
    const APPROVE_WINNER = 'approve_winner';
    const PROCESS_WINNER = 'process_winner';
    const REPROCESS_WINNER = 're_process_winner';

    const UPDATE_USER = 'update_user';
    const ASSIGN_AS_MASTER_AGENT = 'assign_as_master_agent';
    const EDIT_MASTER_AGENT = 'edits_master_agent';

    const ASSIGN_AS_SUB_AGENT = 'assign_as_sub_agent';
    const EDIT_SUB_AGENT = 'edits_sub_agent';
    const CREATE_WITHDRAWAL_REQUEST = 'create_withdrawal_request';
    const DELETE_WITHDRAWAL_REQUEST_ITEM = 'delete_withdrawal_request_item';
    const PROCESS_ADMIN_WITHDRAWAL_REQUEST_ITEM = 'process_admin_withdrawal_request_item';
    const PROCESS_AGENT_WIHDRAWAL = 'process_agent_withdrawal';
    const RE_PROCESS_ADMIN_WITHDRAWAL_REQUEST_ITEM = 're_process_admin_withdrawal_request_item';
    const SUCCESS = 'success';
    const CI_MANUAL = 'manual';
    const CSR_APPROVAL = 'approve_manual_ci_request';


    const SUPER_ADMIN = 5000;
    const ACCOUNTING = 2000;
    const OPERATIONS = 1000;
    const CSR = 50;
    const BETTORS = 0;

    const PROMO_DOWNLOAD_APP = 'promo_download_app';
    // const MOBILE_APP_URL = 'https://onlinelotto.pro';
    const MOBILE_APP_URL = 'https://goldlotto.live';
    const FAKE_URL = 'https://saturnstl.net/';
    const MAIN_URL = 'https://playstl.net/';
    // const MOBILE_APP_URL = 'http://localhost:8000';
    // const MOBILE_APP_URL = 'https://phplaravel-1081601-4044162.cloudwaysapps.com';

    #Promos
    const PROMO_DOUBLE_AMOUNT = 'promo_double_amount';
    const PROMO_DOWNLOAD_APP_AMOUNT = 0;
    const PROMO_CI_750 = '750_ci_amount__100_credits';
    const PROMO_CI_1300 = '1300_ci_amount__200_credits';
    const PROMO_CI_2000 = '2000_ci_amount__400_credits';
    const DEPOSIT_SUCCESS = 'deposit_success';

    const PROMO_PLUS_50_percent_CI_1500 = 'promo_plus_50_percent_ci_1500_receive_750';
    const PROMO_PLUS_50_percent_CI_2000 = 'promo_plus_50_percent_ci_2000_receive_1000';
    const PROMO_PLUS_50_percent_CI_3000 = 'promo_plus_50_percent_ci_3000_receive_1500';

    const PROMO_PLUS_10_percent_CI_100 = 'promo_plus_10_percent_ci_100_receive_10';
    const PROMO_PLUS_10_percent_CI_400 = 'promo_plus_10_percent_ci_400_receive_40';
    const PROMO_PLUS_10_percent_CI_600 = 'promo_plus_10_percent_ci_600_receive_60';

    const PROMO_PLUS_20_percent_CI_800 = 'promo_plus_10_percent_ci_800_receive_160';
    const PROMO_PLUS_20_percent_CI_1000 = 'promo_plus_10_percent_ci_1000_receive_200';
    const PROMO_PLUS_20_percent_CI_1400 = 'promo_plus_10_percent_ci_1400_receive_280';
    const PROMO_2_percent_reate = 'promo_2_percent_rebate';




    public $csr = 0;
    public $remainingBalance = 0;
    public $allowed = [
                        1,  
                        2,  
                        3,  
                        5,  
                        43768,  
                        79,  
                        15656,  
                        15386,  
                        18258,  
                        15655,  
                        15659  
                    ];
    //admin accounts
    public $allowed_access = [
                        10,  // 09155558006 Dudes
                        12,  // 09171577839
                        13,  // 09364550882 Roy
                        15,  // 09624304182	Userlevel 2
                        16,  // 09087076302	Super Admmin
                        18,  // 09659086255	Super Admmin
                        27,  // 09154547876	Super Admmin
                        33,  // 09111111111	Super Admmin
                        37,  // 09151439675	Super Admmin
                        38,  // 09959905382	100
                        40,  // 09102257551	Userlevel 2
                        41,  // 09651475619	Userlevel 2
                        42,  // 09956474559	Userlevel 2
                        57,  // 09956474559	Operator
                          
                    ];
    public $operations = 1000;
    public $accounting = 2000;
    public $super_admin = 5000;


    protected $twoD_jackpot;
    protected $min_bet_2ds;
    protected $max_bet_2ds;

    protected $threeD_jackpot;
    protected $max_bet_3ds;
    protected $min_bet_3ds;

    protected $min_bet_4ds;
    protected $max_bet_4ds;

    protected $jackpot_4d;
    protected $last_three_4d;
    protected $last_two_4d;

    protected $settings;

    protected $background_image = "images/site-bg.png";
    public function record($activity,$data){
        $record = null;
        $data = json_encode($data);
        $full_data = [
                      'user_id'=>auth()->user()->id,
                      'user_name'=>auth()->user()->name,
                      'action'=>$activity,
                      'data'=>$data,
                    ];
        $record = ActivityLogs::create($full_data);
    }

    public function retrieve($key)
    {
        $data = Redis::get($key);
        return json_decode($data);
    }
    
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }
}
