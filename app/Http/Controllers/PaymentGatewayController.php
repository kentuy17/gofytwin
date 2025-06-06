<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\User;
use App\Models\Promos;
use App\Models\Topups;
use App\Models\Bets3ds;
use App\Models\Cashins;
use App\Models\Credits;
use App\Models\Receipts;
use App\Models\Callbacks;
use App\Models\References;
use App\Models\Winners3ds;
use App\Models\CashinGrids;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use App\Jobs\ProcessReceipts;
use App\Models\AdminWithdrawals;
use Illuminate\Support\Facades\Http;

class PaymentGatewayController extends Controller
{
    //
    public $base_url = 'https://api.globaltrustpay.com/v3';
    public $gateway = 'paymaya';
    public $merchant_slug = 'Playstl_gtp';
    public $callback_url = 'https://api.demo.com/callback/payment';
    public $return_url = 'https://playstl.net/play/3d/receipt/';
    public $hash_key = '5f4440fc1b493b0eb08222a2eb932bb9';
    public $hash_iv = '45f52daf6caf6c9b';

    protected $merchant;
    protected $transaction_id;
    protected $user_id;
    protected $transaction_amount;
    protected $transaction_status = 0;
    
    const SUCCESS_STATUS = 5;
    const FAILED_STATUS = 3;
    

    public function viewdata(Request $request){
        $total = Bets3ds::all()->sum('amount');
        $receipttotal = Receipts::all()->sum('amount');
        echo "Bets amount total: ".$total.'<br>';
        echo "Receipts amount total: ".$receipttotal.'<br>';
        exit();
    }

    public function populate(Request $request){
        $ref = References::where('reference_no','')->delete();
        ProcessReceipts::dispatch();
        return "Finished";
        $total = Bets3ds::all()->sum('amount');
        echo "Finished";
    }
    public function populate_agent(Request $request){
        $ref = References::where('reference_no','')->delete();
        ProcessReceipts::dispatch();

        return response()->json([
            'message' => 'Finished',
        ]);
    }
    public function index(Request $request){
        $request_url = $this->base_url . '/deposit';
        $callback_url = route('callback');
        $params = array(
            "merchant_slug" => $this->merchant_slug,
            "gateway" => $this->gateway,
            "device" => "desktop",
            "amount" => '100.00',
            "merchant_order_time" => $this->microtime_float(),
            "merchant_order_num" => rand(),
            "user_ip" => "13.214.49.66",
            "callback_url" => $callback_url,
            "return_url" => $this->return_url
        );

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
                $link = json_decode($order, true);
                // dd($link);
                // $open_link = 
                Callbacks::create($responseData);
                header("Location: ".$link['navigate_url']);
                
                // return response()->json([
                //     'message' => 'Process successful.',
                // ]);
                // Deposit order was successful
            } else {
                // Deposit order failed
                $errorMessage = $responseData['msg'];
            }
        } else {
            // API request failed
            $errorMessage = 'API request failed';
        }

        return response()->json([
            'message' => $errorMessage,
        ]);
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
    protected function updateStatus(){
        $cash_in = Cashins::where('merchant_order_no',$this->transaction_id)->first();
        if($cash_in){
            $cash_in->status = self::SUCCESS;
            $cash_in->save();
        }
        $withdrawals = Withdrawals::where('merchant_order_no',$this->transaction_id)->first();
        if($withdrawals){
            $withdrawals->process_status = self::SUCCESS;
            $withdrawals->save();
        }
        // $adminWithdrawals = AdminWithdrawals::where('merchant_order_no',$this->transaction_id)->first();
        // if($adminWithdrawals){
        //     $adminWithdrawals->process_status = self::SUCCESS;
        //     $adminWithdrawals->save();
        // }
    }
    protected function checkWinners(){
         
        // $is_winner_3d = Winners3ds::where('merchant_order_no', $this->transaction_id)->first();
        // if($is_winner_3d){
        //     $is_winner_3d->process_status = self::SUCCESS;
        //     $is_winner_3d->save();
        // }
        
    }
    protected function saveRecordToGrid($topup){
        $user = User::find($this->user_id);
        if($user){
            CashinGrids::create([
                'merchant_order_no'=> $this->transaction_id,
                'user_id'=> $this->user_id,
                'mobile_no'=> $user->mobile_no,
                'amount'=> $this->transaction_amount,
                'gateway'=> $this->merchant,
                'status'=> self::SUCCESS,
                'sign'=> $this->merchant,
            ]);
        }
    }
    protected function applyCredits(){
        $topup = Topups::where('merchant_order_num',$this->transaction_id)->first();
        if(!$topup){

            return false;
        }   

        $userId = $topup->user_id;
        if($topup){
            if($this->transaction_status == self::SUCCESS_STATUS || $this->transaction_status == self::SUCCESS){
                $user = User::find($topup->user_id);
                
            }
            $topup->amount = $this->transaction_amount;
            $topup->status = self::SUCCESS;
            $topup->save();
        }
        $credits = User::find($topup->user_id);
        if($credits){
            $this->saveRecordToGrid($topup);
 
            $rebate_amount = $this->transaction_amount * 0.02;
            $new_amount = $credits->amount + $this->transaction_amount + $rebate_amount;
         
            #default. uncomment if there are no promos
            $credits->credits = $credits->credits + $this->transaction_amount;

            $credits->save();
        }
    }
  
     
    public function jd_notify(Request $request){
        $this->merchant = 'jd_pay';
        $data = $request->all();
        $saveDataRecord = [
            'code' => 0, //this is for successful transaction
            'msg' => 'PG notification response load.', 
            'merchant_slug' => 'jd_pay', 
            'merchant_order_num' => 'PG Notification', 
            'action' => 'record_log_purpose', 
            'order' => json_encode($data), 
            'user_id' => null, 
        ];
        Callbacks::create($saveDataRecord);
        if(!isset($data['sign'])){

            return response()->json([
                'message' => 'You are not allowed here',
            ]);    
        }
        // dd($data);
        if(isset($data['status']) && $data['status'] == self::SUCCESS_STATUS){
           
            //check if the order number has already existed and called back 
            $has_callbacked = Callbacks::where('merchant_order_num',$data['order_id'])->where('msg',self::SUCCESS)->where('action',self::DEPOSIT_SUCCESS)->first();
            if($has_callbacked){

                return 'SUCCESS';
            }

            $user_id = explode("_",$data['order_id']);
            $this->user_id = $user_id[1];
            $this->transaction_id = $data['order_id'];
            $this->transaction_amount = $data['amount'];
            $this->transaction_status = $data['status'];
            //@todo check the user id
            $saveData = [
                'code' => 0, //this is for successful transaction
                'msg' => self::SUCCESS, 
                'merchant_slug' => 'jd_pay', 
                'merchant_order_num' => $this->transaction_id, 
                'action' => self::DEPOSIT_SUCCESS, 
                'order' => json_encode($data), 
                'user_id' => $this->user_id, 
            ];
            Callbacks::create($saveData);
            $this->applyCredits();
            // dd('error');
            $this->checkWinners();
            $this->updateStatus();

        } elseif(isset($data['status']) && $data['status'] == self::FAILED_STATUS){
            $success = 0;
            $message = 'Transaction failed. Merchant returned failed transaction.';
        } else {
            $success = 0;
            $message = 'There has been an error in our process';
        }

        return "SUCCESS";
    }
    public function withdraw(Request $request)
    {      
        $winner = null;
        $data = $request->all();
        $order = $this->decrypt($this->hash_key, $this->hash_iv, $data['order']);
        $data['order'] = $order;
        $order = json_decode($order,true);
        $user_id = explode("_",$data['merchant_order_num']);

        Callbacks::create($data);
        Log::create([
            'order_no'=> $data['merchant_order_num'],
            'user_id'=> $user_id[1],
            'mobile_no'=> null,
            'msg'=> $data['msg'],
            'amount'=> $order['amount'],
            'action'=> $data['action'],
            'gateway'=> $order['gateway'],
            'event_type'=> $data['action'],
            'status'=> $order['status'],
            'sign'=> $order['sign'],
            'date'=> now(),
            'data'=> json_encode($order),
        ]);

        //check if record exists, then update
        // if status is success, meaning full success, update the winner id
        $exp = explode("_", $data['merchant_order_num']);
        switch ($exp[2]) {
            
            case "3":
                $winner = Winners3ds::where('merchant_order_no',$data['merchant_order_num'])->first();
                break;
            
            case "0":
                $winner = Withdrawals::where('merchant_order_no',$data['merchant_order_num'])->first();
                break;
            default:
                
            }
        $winner->process_status = $order['status'];
        if($order['status'] == 'fail'){
            $winner->claimed = null;
        } else {
            $winner->claimed = 1;
            #meaning the callback was a success, subtract the amount to the user Balance
            if($exp[2] == 0){
                $user_id = $exp[3];
                // #find master agent or sub agent
                // $masterAgent = MasterAgents::where('user_id',$user_id)->first();
                // if($masterAgent){
                //     $newBalance = $masterAgent->total - $winner->amount;
                //     $masterAgent->total = $newBalance;
                //     $masterAgent->save();
                // } 
                // $subAgent = SubAgents::where('user_id',$user_id)->first();
                // if($subAgent){
                //     $newBalance = $subAgent->total - $winner->amount;
                //     $subAgent->total = $newBalance;
                //     $subAgent->save();
                // }
                
            }
        }
        $winner->save();
   
        return response()->json([
            'message' => 'Payment processed sucessfully',
        ]);
    }

 
}
