<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Topups;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JdPaymentGateway extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    const PENDING_STATUS = 'pending';
    const MERCHANT = 'jdpay';
    const DEPOSIT_ATTEMPT = 'jdpay_deposit_attempt';
    const PG = 'pg_4';
   
    public $apiEndpoint = 'https://jdpay.la2568.site/api/transfer';
    public $withdrawal_endpoint = 'https://jdpay.la2568.site/api/daifu';
    public $apiBalanceEndpoint = 'https://jdpay.la2568.site/api/me';
    public $callback_url = '/api/pg/notify/jd';
    public $return_url;
    public $key = 'c43647e3a6d7436ffac50d79f7fb170c';
    public $payment_type; // 3 = fast direct, 1 = QR Code
    public $amount;
    public $ip;
    public $returnUrl;
    public $order_id;
    public $bank_card_name;
    public $bank_card_account;
    protected $payload;
    protected $merchant_id = 'pinkco';
    public $bank_code;// Gcash => gcash ; Paymaya=>PMP ; USDT-TRC=>usdt-trc
   
    public function getBalancePayload(){

        return      [
                        "merchant"=> $this->merchant_id,
                    ];
    }
    public function getWithrawalPayload(){

        return      [
                        "merchant"=> $this->merchant_id,
                        "total_amount"=> $this->amount,
                        "callback_url"=> URL::to("/").$this->callback_url,
                        "order_id"=> $this->order_id,
                        "bank"=> $this->bank_code,
                        "bank_card_name"=> $this->bank_card_name,
                        "bank_card_account"=> $this->bank_card_account,
                        "bank_card_remark"=> $this->bank_card_account
                    ];
    }
    public function getpayload(){

        return      [
                        "merchant"=> $this->merchant_id,
                        "payment_type"=> $this->payment_type,
                        "amount"=> (int)$this->amount,
                        "order_id"=> $this->order_id,
                        "bank_code"=> $this->bank_code,
                        "callback_url"=> URL::to("/").$this->callback_url,
                        "return_url"=> $this->return_url,
                    ];
    }

    public function processPayout(){
        //bank_code String Required Gcash => gcash ; Paymaya=>PMP ; USDT-TRC=>usdt-trc , Other banks at last page
        $this->payload = $this->getWithrawalPayload();
        $load = $this->sortJson($this->payload);
        $concatenated = $this->concatenateString($load).'key='.$this->key;
        $encoded = MD5($concatenated);
        $load['sign'] = $encoded;
        $response = Http::post($this->withdrawal_endpoint, $load);
        $transactionStatus = json_decode($response->body(), true);
      
        return $transactionStatus;


    }
    protected function concatenateString($array){
        $string = null;
        foreach($array as $key=> $value){
            $string .= $key."=".$value."&";
        }

        return $string;
    }
    public function processTest(){
        $payload = $this->getpayload();
        $sign = $this->sortJson($payload);
        
        $concatenated = $this->concatenateString($sign).'key='.$this->key;
        $encoded = MD5($concatenated);
        $sign['sign'] = $encoded;
    }        
    protected function sortJson($json) {
        if (!is_array($json)) {
            return $json;
        }
        ksort($json);
        $sortedJson = [];
        foreach ($json as $key => $value) {
            $sortedJson[$key] = $this->sortJson($value);
        }
        return $sortedJson;
    }
    
    function generateSignature($payload, $privateKey) {
        $encodedPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        openssl_sign($encodedPayload, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        return bin2hex($signature);
    }

    public function checkBalance(){
        $this->payload = $this->getBalancePayload();
        $load = $this->sortJson($this->payload);
        $concatenated = $this->concatenateString($load).'key='.$this->key;
        $encoded = MD5($concatenated);
        $load['sign'] = $encoded;
        $response = Http::post($this->apiBalanceEndpoint, $load);
        $transactionStatus = json_decode($response->body(), true);
      
        return $transactionStatus;
    }
    public function deposit(){
        $this->payload = $this->getpayload();
        $load = $this->sortJson($this->payload);
        $concatenated = $this->concatenateString($load).'key='.$this->key;
        $encoded = MD5($concatenated);
        $load['sign'] = $encoded;
        $response = Http::post($this->apiEndpoint, $load);
        $transactionStatus = json_decode($response->body(), true);

        $saveData = [
            'code' => 0, //this is for successful transaction
            'msg' => 'success', 
            'merchant_slug' => self::MERCHANT, 
            'merchant_order_num' => $this->order_id, 
            'action' => self::DEPOSIT_ATTEMPT, 
            'order' => json_encode($transactionStatus), 
            'user_id' => $this->user_id, 
        ];
        
        Callbacks::create($saveData);
        // if($transactionStatus['status'] == 1){

            $this->recordTransaction();
        // }
        
        return $response;
    }

    protected function recordTransaction(){
        $ma_id = 0;
        $sa_id = 0;
        $ma_mount = 0;
        $sa_amount = 0;
        if(auth()->user()->is_referral > 0){
            $agentObj = AgentReportGrids::where('user_id', auth()->user()->id)->first();
            $ma_id = $agentObj->ma_id;
            $sa_id = $agentObj->sa_id;

            $ma_mount = $this->amount * $agentObj->ma_share;
            $sa_amount = $this->amount * $agentObj->sa_share;


            // #current balance of master agent
            // if($ma_id > 0){
            //     $ma_bal = MasterAgents::where('user_id',$ma_id)->first();
            //     $prev_bal = $ma_bal->total;
            //     $new_bal = $prev_bal + $ma_mount;
            //     $ma_bal->total = $new_bal;
            //     $ma_bal->save();
            // }
            // if($sa_id > 0){
            //     #current balance of sub agent
            //     $sa_bal = SubAgents::where('user_id',$sa_id)->first();
            //     $sa_prev_bal = $sa_bal->total;
            //     $new_bal = $sa_prev_bal + $sa_amount;
            //     $sa_bal->total = $new_bal;
            //     $sa_bal->save();
            // }
        }

        

        // $saveData = [
        //     'code' => 0, //this is for successful transaction
        //     'msg' => 'success', 
        //     'merchant_slug' => self::MERCHANT, 
        //     'merchant_order_num' => $this->order_id, 
        //     'action' => self::DEPOSIT_ATTEMPT, 
        //     'order' => json_encode($this->payload), 
        //     'user_id' => $this->user_id, 
        // ];
        // Callbacks::create($saveData);
        Topups::create([
            'user_id'=> auth()->user()->id,
            'merchant_order_num'=> $this->order_id,
            'action'=> self::DEPOSIT_ATTEMPT,
            'order'=> $this->order_id,
            'amount'=> $this->amount,
            'gateway'=> $this->bank_code,
            'merchant_order_remark'=> self::PG,
            'status'=> self::PENDING_STATUS,
            'sign'=> $this->order_id,
        ]);
        Log::create([
            'order_no'=> $this->order_id,
            'user_id'=> auth()->user()->id,
            'mobile_no'=> auth()->user()->mobile_no,
            'msg'=> self::DEPOSIT_ATTEMPT,
            'amount'=> $this->amount,
            'action'=> self::DEPOSIT_ATTEMPT,
            'gateway'=> $this->payment_type,
            'event_type'=> self::DEPOSIT_ATTEMPT,
            'status'=> self::PENDING_STATUS,
            'sign'=> $this->order_id,
            'date'=> now(),
            'data'=> json_encode($this->payload),
        ]);
        Cashins::create([
            'merchant_order_no'=> $this->order_id,
            'user_id'=> auth()->user()->id,
            'amount'=> $this->amount,
            'gateway'=> $this->payment_type,
            'status'=> self::PENDING_STATUS,    
            'sign'=> $this->order_id,
        ]);
        CashinGrids::create([
            'merchant_order_no'=> $this->order_id,
            'user_id'=> auth()->user()->id,
            'mobile_no'=> auth()->user()->mobile_no,
            'amount'=> $this->amount,
            'gateway'=> $this->payment_type,
            'status'=> self::PENDING_STATUS,
            'sign'=> $this->order_id,
        ]);
    }
}
