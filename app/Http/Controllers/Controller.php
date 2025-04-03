<?php

namespace App\Http\Controllers;



use Carbon\Carbon;
use App\Models\User;
use App\Models\Carts;
use App\Models\Bets1ds;
use App\Models\Bets2ds;
use App\Models\Bets3ds;
use App\Models\Bets4ds;
use App\Models\Bets6ds;
use App\Models\Cart1ds;
use App\Models\Cart2ds;
use App\Models\Cart3ds;
use App\Models\Cart4ds;
use App\Models\Cashins;

use App\Models\Pick2ds;
use App\Models\JaiAlais;
use App\Models\SubAgents;
use App\Models\Cartpick2ds;
use App\Models\ActivityLogs;
use App\Models\MasterAgents;
use App\Models\CartsJaiAlais;
use App\Models\HotNumbers1ds;
use App\Models\HotNumbers2ds;
use App\Models\HotNumbers3ds;
use App\Models\HotNumbers4ds;
use App\Models\SettingsModel;
use App\Models\HotNumbersjais;
use App\Models\AgentReportGrids;
use App\Models\Hotnumberspick2ds;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\DailyActiveBettorGrid;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
                        1, // Ryan
                        2, // Dudes
                        3, // Iero
                        5, // bishop
                        43768, // RAZOR
                        79, // Nate
                        15656, // pitbike
                        15386, // Buluyagon
                        18258, // PURPLE
                        15655, // SATURDAY
                        15659 // Green
                    ];
    public $allowed_access = [
                        1, // Ryan
                        2, // Dudes
                        3, // Iero
                        5, // bishop
                        6, // Snow Bear
                        11, // Sarge
                        15, // Gomla
                        17, // Spaz
                        43768, // RAZOR
                        79, // Nate
                        15656, // pitbike
                        15386, // Buluyagon
                        18258, // PURPLE
                        15655, // SATURDAY
                        87, // Indigo
                        15659 // Green
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
}
