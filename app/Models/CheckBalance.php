<?php

namespace App\Models;

use App\Models\Topups;
use App\Models\Winners1ds;
use App\Models\Winnerspick2ds;
use App\Models\JdPaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckBalance extends Model
{
    use HasFactory;

    public $base_url = 'https://api.globaltrustpay.com/v3';
    public $gateway = null;
    public $merchant_slug = 'Playstl_gtp';
    public $callback_url = 'https://api.demo.com/callback/payment';
    public $return_url = 'https://phplaravel-1141774-4042879.cloudwaysapps.com/';
    public $hash_key = '5f4440fc1b493b0eb08222a2eb932bb9';
    public $hash_iv = '45f52daf6caf6c9b';
    public $gateways = ['gcash','paymaya','paymaya_qr','gcash_qr','gcash_redirect'];
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
    public const PERCENTAGE_AMOUNT = 0.02; 


    public function getBalance($request){
        
        $from = date('Y-m-d');
        $to = date('Y-m-d');
        $total = 0;
        $opex = 0;
        $gcash_amount = 0;
        $gcash_redirect = 0;
        $gcash_qr_amount = 0;
        $maya_qr_amount = 0;
        $pg_1_wallet = 0;
        $correct_profit = 0;
        $manual = 0;
        $pg3_walletBalance = 0;
        $maya_amount = 0;

        $pg4_wallet = new JdPaymentGateway();
        $pg4_walletBalance = $pg4_wallet->checkBalance();
        // dd($pg4_walletBalance);

        // $opex = AdminWithdrawals::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('process_status','success')->sum('amount');


        #Agent commisions
        # total wallet sa MA + total wallet SA, exclude company accounts 
        $gcash_amount+=$pg4_walletBalance['balance'];

        $total += $pg4_walletBalance['balance'];
        #withdrawals
        $callback_amount = Withdrawals::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('process_status','success')->sum('amount');

        #correct calculation
        // $correct_profit = $total - $daily_won_amount + $callback_amount;

        $actual_live_profit = number_format($correct_profit,2,'.',',');
        $responseData['wallets']['total_wallet'] = number_format($total,2,'.',',');
        $responseData['wallets']['total_manual_wallet'] = number_format($manual,2,'.',',');
        $responseData['wallets']['total_pg3_wallet'] = number_format($pg3_walletBalance,2,'.',',');
        $responseData['wallets']['total_pg4_wallet'] = number_format($pg4_walletBalance['balance'],2,'.',',');
        $responseData['wallets']['gcash'] = $gcash_amount;
        $responseData['wallets']['gcash_qr'] = $gcash_qr_amount;
        $responseData['wallets']['gcash_redirect'] = $gcash_redirect;
        $responseData['wallets']['paymaya'] = $maya_amount;
        $responseData['wallets']['paymaya_qr'] = $maya_qr_amount;
        $responseData['wallets']['pg_1_wallet'] = $pg_1_wallet;
        $responseData['wallets']['actual_live_profit'] = $actual_live_profit;
        
        $responseData['wallets']['opex_withdrawals'] = number_format($opex,2,'.',',');
        
        return $responseData['wallets'];
    }

 

    public function encrypt($key, $iv, $data) {
        $cipher = 'AES-256-CBC';
        $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode( $encrypted );
    }
    
    public function decrypt($key, $iv, $data) {
        $cipher = 'AES-256-CBC';

        return openssl_decrypt(base64_decode($data), $cipher, $key, OPENSSL_RAW_DATA, $iv);
    }
    
    public function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }
}
