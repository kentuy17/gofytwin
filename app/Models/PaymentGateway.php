<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Topups;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gcash'=> 'GXCHP',
        'paymaya' => 'PAYMP',
        'bpi',
        'draw_date',
        'draw_time',
        'user_id',
    ];
    public $base_url = 'https://api.globaltrustpay.com/v3';
    public $gateway = null;
    public $merchant_slug = 'Playstl_gtp';
    public $callback_url = 'https://api.demo.com/callback/payment';
    public $return_url = 'https://phplaravel-1141774-4042879.cloudwaysapps.com/';
    public $hash_key = '5f4440fc1b493b0eb08222a2eb932bb9';
    public $hash_iv = '45f52daf6caf6c9b';
    public const PERCENTAGE_AMOUNT = 0.02; 


    public function processPayout($winner, $play){
        if($winner->payout_remittance == 'gcash'){
            $bank_code = 'GXCHP';
            $card_holder = $winner->receiver_name;
            $card_number = $winner->receiver_mobile_number;
            $gateway = 'gcash';
        } elseif($winner->payout_remittance == 'paymaya'){
            $bank_code = 'PAYMP';
            $card_holder = $winner->receiver_name;
            $card_number = $winner->receiver_mobile_number;
            $gateway = 'paymaya';
        } else {
            $bank_code = $winner->payout_remittance;
            $card_holder = $winner->receiver_name;
            $card_number = $winner->receiver_mobile_number;
            $gateway = 'gcash';    
        }
        // dd($pg);
        $request_url = $this->base_url . '/withdraw';
        // this route is on the API endpoint
        // route('withdraw') = <base_url>/withdraw
        $callback_url = route('withdraw');
        $errorMessage = "";

        // $orderNumber = str_pad(rand(), 6, '0', STR_PAD_LEFT);
        $orderNumber = str_pad(rand(), 6, '0', STR_PAD_LEFT).'_'.$winner->id.'_'.$play;
        if($play == 0){
            $charge = 0.02 * $winner->amount;
            $charge_with_constant = $charge + 10;
            $final_amount = $winner->amount - $charge_with_constant;
        } else {
            $charge = 0.02 * $winner->winning_amount;
            $charge_with_constant = $charge + 10;
            $final_amount = $winner->winning_amount - $charge_with_constant;
        }
        
 
        $params = array(
            "merchant_slug" => $this->merchant_slug, // ok
            "amount" => (int)$final_amount, // ok
            "merchant_order_time" => $this->microtime_float(), // ok
            "merchant_order_num" => $orderNumber, // ok
            "user_ip" => $_SERVER["REMOTE_ADDR"],
            "callback_url" => $callback_url, // ok
            "gateway" => $gateway, // to check
            "bank_code"=> $bank_code,
            "card_holder"=> $card_holder,
            "card_number"=> $card_number,
            "uid"=> $winner->play_type.'_'.$winner->bet_id,
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
                $order = $this->decrypt($this->hash_key, $this->hash_iv, $responseData['order']);
                $responseData['order'] = $order;
                $orderData = json_decode($order, true);
                Callbacks::create($responseData);
                Log::create([
                    'order_no'=> $orderNumber,
                    'user_id'=> $winner->user_id,
                    'mobile_no'=> $winner->contact_no,
                    'msg'=> null,
                    'amount'=> $params['amount'],
                    'action'=> 'withdraw',
                    'gateway'=> $params['gateway'],
                    'event_type'=> 'withdraw',
                    'status'=> $orderData['status'],
                    'sign'=> $orderData['sign'],
                    'date'=> now(),
                    'data'=> $order,
                ]);

                $responseData['status'] = $orderData['status'];
            } else {
                $responseData['order'] = json_encode([
                            'request' => $params,
                ]);
                Callbacks::create($responseData);
                $responseData['status'] = 'fail';
            }
            $responseData['merchant_order_no'] = $orderNumber;

            return $responseData;
        } else {
            // API request failed
            $errorMessage = 0;
            
            return $errorMessage;
        }

        return false;
    }

    public function agentsPayout($winner, $play){
        if($winner->payout_remittance == 'gcash'){
            $bank_code = 'GXCHP';
            $card_holder = $winner->receiver_name;
            $card_number = $winner->receiver_mobile_number;
            $gateway = 'gcash';
        } elseif($winner->payout_remittance == 'paymaya'){
            $bank_code = 'PAYMP';
            $card_holder = $winner->receiver_name;
            $card_number = $winner->receiver_mobile_number;
            $gateway = 'paymaya';
        } else {
            $bank_code = $winner->payout_remittance;
            $card_holder = $winner->receiver_name;
            $card_number = $winner->receiver_mobile_number;
            $gateway = 'bank';    
        }
        // dd($pg);
        $request_url = $this->base_url . '/withdraw';
        // dd($request_url);
        // this route is on the API endpoint
        // route('withdraw') = <base_url>/withdraw
        $callback_url = route('withdraw');
        // dd($callback_url);   
        $errorMessage = "";

        // $orderNumber = str_pad(rand(), 6, '0', STR_PAD_LEFT);
        $orderNumber = str_pad(rand(), 6, '0', STR_PAD_LEFT).'_'.$winner->id.'_'.$play;
        $charge = 0.02 * $winner->amount;
        $charge_with_constant = $charge + 10;
        $final_amount = $winner->amount - $charge_with_constant;
 
        $params = array(
            "merchant_slug" => $this->merchant_slug, // ok
            "amount" => (int)$final_amount, // ok
            "merchant_order_time" => $this->microtime_float(), // ok
            "merchant_order_num" => $orderNumber, // ok
            "user_ip" => $_SERVER["REMOTE_ADDR"],
            "callback_url" => $callback_url, // ok
            "gateway" => $gateway, // to check
            "bank_code"=> $bank_code,
            "card_holder"=> $card_holder,
            "card_number"=> $card_number,
            "uid"=> $winner->contact_no.'_'.$winner->id,
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
                $order = $this->decrypt($this->hash_key, $this->hash_iv, $responseData['order']);
                $responseData['order'] = $order;
                $orderData = json_decode($order, true);
                Callbacks::create($responseData);
                Log::create([
                    'order_no'=> $orderNumber,
                    'user_id'=> $winner->user_id,
                    'mobile_no'=> $winner->contact_no,
                    'msg'=> null,
                    'amount'=> $params['amount'],
                    'action'=> 'withdraw',
                    'gateway'=> $params['gateway'],
                    'event_type'=> 'withdraw',
                    'status'=> $orderData['status'],
                    'sign'=> $orderData['sign'],
                    'date'=> now(),
                    'data'=> $order,
                ]);

                $responseData['status'] = $orderData['status'];
            } else {
                $responseData['order'] = json_encode([
                            'request' => $params,
                ]);
                Callbacks::create($responseData);
                $responseData['status'] = 'fail';
            }
            $responseData['merchant_order_no'] = $orderNumber;

            return $responseData;
        } else {
            // API request failed
            $errorMessage = 0;
            
            return $errorMessage;
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
