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
        $request_url = $this->base_url . '/balance';
 
        $params = array(
            "merchant_slug" => $this->merchant_slug, // ok
            "merchant_time" => $this->microtime_float(), // ok
            "gateways" => $this->gateways, // ok
        );

        // $callback_url = route('callback');
        ksort($params);

        $params['sign'] = sha1(json_encode($params));
       
        //for postman: json encode this modafackah
        $body = array(
            "merchant_slug" => $this->merchant_slug,
            "data" => $this->encrypt($this->hash_key, $this->hash_iv, json_encode($params))
        );
        

        $response = Http::post($request_url, $body);
        if ($response->successful()) {
            $responseData = json_decode($response->body(), true);
            if ($responseData['code'] === 0) {
                $from = isset($request->from) ? $request->from : date('Y-m-d');
                $to = isset($request->to) ? $request->to : date('Y-m-d');
                $total1d = Winners1ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
                $totalpick2 = Winnerspick2ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
                $total2dAmount = Winners2ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
                $total3dAmount = Winners3ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
                $total4dAmount = Winners4ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
                $total6dAmount = Winners6ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
                $totalPick1Amount = Winners1ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');
                $totalPick2Amount = Winnerspick2ds::whereBetween('draw_date', [$from, $to])
                    ->sum('winning_amount');

                $daily_won_amount = $total1d + $totalpick2 + $total2dAmount + $total3dAmount + $total4dAmount + $total6dAmount + $totalPick1Amount + $totalPick2Amount;
                $gcash_amount = number_format($responseData['wallets']['gcash'],2,'.',',');
                $gcash_qr_amount = number_format($responseData['wallets']['gcash_qr'],2,'.',',');
                $gcash_redirect = number_format($responseData['wallets']['gcash_redirect'],2,'.',',');
                $maya_amount = number_format($responseData['wallets']['paymaya'],2,'.',',');
                $maya_qr_amount = number_format($responseData['wallets']['paymaya_qr'],2,'.',',');
                $pg_1_wallet = number_format($responseData['wallets']['gcash'] 
                                + $responseData['wallets']['gcash_qr'] 
                                + $responseData['wallets']['gcash_redirect'] 
                                + $responseData['wallets']['paymaya'] 
                                + $responseData['wallets']['paymaya_qr'],2,'.',',');
                // $from = isset($request->from) ? $request->from : date('Y-m-d');
                // $to = isset($request->to) ? $request->to : date('Y-m-d');
                // $manual_amount = Topups::where();

                $from = date('Y-m-d');
                $to = date('Y-m-d');
                //manual wallet
                $manual = Topups::where('gateway','manual')->where('status','success')->sum('amount');


                $pg3_wallet = new LuckypayPaymentGateway();
                $pg3_walletBalance = $pg3_wallet->checkBalance();

                $pg4_wallet = new JdPaymentGateway();
                $pg4_walletBalance = $pg4_wallet->checkBalance();
                // dd();

                $opex = AdminWithdrawals::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('process_status','success')->sum('amount');


                $total_ma_earnings = 0;
                $all_mas = MasterAgents::all();
                foreach($all_mas as $all_ma){
                    if(! in_array($all_ma->user_id,$this->admin_accounts)){
                        $total_ma_earnings += $all_ma->total;
                    }
                    
                }
                $data['master_agent_earnings'] = number_format($total_ma_earnings,2,'.',',');
    
                #get SA earnings
                $total_sa_earnings = 0;
                $all_sas = SubAgents::all();
                foreach($all_sas as $all_sa){
                    if(! in_array($all_sa->user_id,$this->admin_accounts)){
                        $total_sa_earnings += $all_sa->total;
                    }
                }
                $data['sub_agent_earnings'] = number_format($total_sa_earnings,2,'.',',');
    
    
                #Agent commisions
                # total wallet sa MA + total wallet SA, exclude company accounts 
                $agent_commision = $total_ma_earnings + $total_sa_earnings;


                $total = $responseData['wallets']['gcash'] +  
                         $responseData['wallets']['gcash_qr']  +  
                         $responseData['wallets']['gcash_redirect']  +  
                         $responseData['wallets']['paymaya']  +  
                         $responseData['wallets']['paymaya_qr'] + 
                         $pg3_walletBalance['balance']+ 
                         $pg4_walletBalance['balance'];
                $prof = $total - $daily_won_amount;
                // $prof2 = $prof - $opex - $agent_commision; 
                #withdrawals
                $callback_amount = Withdrawals::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])->where('process_status','success')->sum('amount');

                #correct calculation
                $correct_profit = $total - $agent_commision - $daily_won_amount + $callback_amount;

                $actual_live_profit = number_format($correct_profit,2,'.',',');
                $responseData['wallets']['total_wallet'] = number_format($total,2,'.',',');
                $responseData['wallets']['total_manual_wallet'] = number_format($manual,2,'.',',');
                $responseData['wallets']['total_pg3_wallet'] = number_format($pg3_walletBalance['balance'],2,'.',',');
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
            } else {
                // Deposit order failed
                $errorMessage = $responseData['msg'];
            }
        } else {
            // API request failed
            $errorMessage = 'API request failed';
        }

        return false;
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
