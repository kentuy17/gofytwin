<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\BetsMail;
use App\Models\Topups;
use App\Models\Cashins;
use App\Models\Receipts;
use App\Mail\MailableName;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use App\Models\SettingsModel;
use App\Models\ProcessPayment;
use App\Models\AgentReportGrids;
use Illuminate\Support\Facades\URL;
use App\Models\ManualPaymentGateway;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreFileRequest;

class CashoutController extends CashinController

{
    const MAINTANING_BALANCE = 100;
    const MIN_CO = 200;
    const MIN_BALANCE = 200;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        SettingsModel $settings
    )
    {
        $this->settings = $settings;
    }

    public function processPlayerCashout(StoreFileRequest $request){

        $userRecord = User::find(auth()->user()->id);
        $true_balance = $userRecord->credits - self::MAINTANING_BALANCE; // 200
 
        if($true_balance < $request->amount){
            
            return response([
                    'status'=> false,
                    'redirect_url'=> 'Page Error',
                    'message'=> 'Insufficient Funds',
                ],404);
        }
         
        if($true_balance < self::MIN_CO){

            return response([
                    'status'=> false,
                    'redirect_url'=> 'Page Error',
                    'message'=> 'Minimum Cashout Amount is '.self::MIN_CO.' plus additional ₱100.00 maintaning balance.',
                ],404);
                
        }
 
        $haspending = Withdrawals::where('user_id', auth()->user()->id)->whereNot('process_status', 'success')->count();

        if($haspending > 0){

            return response([
                    'status'=> false,
                    'redirect_url'=> 'Page Error',
                    'message'=> 'You already have one pending withdrawal request.',
                ],404);
        }
       
        // $textblast = new TextBlast;
        
        $withdraw = Withdrawals::create([
            'user_id'=>auth()->user()->id,
            'amount'=>$request->amount,
            'reference_no'=>null,
            'contact_no'=>$userRecord->name,
            'receipt'=>null,
            'claimed'=>0,
            'upload'=>0,
            'approved'=>0,
            'payout_remittance'=>$request->pg,
            'receiver_name'=>$request->name,
            'receiver_address'=>null,
            'receiver_mobile_number'=>$request->mobile,
            'merchant_order_no'=>null,
            'claimed_date'=>null,
            'process_status'=>'pending',
            'message'=>null,
        ]);
        if($withdraw){
            $userCredit = User::find(auth()->user()->id);
            $userCurrentCredit = $userCredit->credits;
            $deductedCredits = $userCurrentCredit - $request->amount;
            $userCredit->credits = $deductedCredits;
            $userCredit->save();
        }

        return response([
                    'status'=> true,
                    'redirect_url'=> URL::to("/player/my/cash-out"),
                    'message'=> 'Insufficient Balance',
                ],200);
    }

    
   
    public function processCashout(StoreFileRequest $request){
        
        $return_url = URL::to("/");
        $response = false;
        // dd($request->amount);

        switch ($request->getPg()) {

            #JDPay
            case 'GCash Fast': // primary PG | min amount = 50 | (JDpay)
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::MAYAB; // JDpay 3 = Fast Direct, 1 = Qrcode (same as LukyPay) | JDpay want to use either 7 or 12
                $processPayment->bank_code = self::GCASH_WORD;
                $response = $processPayment->pay($request);

                break;
            case 'gcash': // primary PG | min amount = 50 | (GCash Easy Pay I)
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::GCASH_DIRECT;
                $processPayment->bank_code = self::GCASH_WORD;
                $response = $processPayment->pay($request);

                break;
            case 'gcash_p2p': // primary PG | min amount = 50 | (GCash P2P/QR PAY I)
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::GCASH_P2P;
                $processPayment->bank_code = self::GCASH_WORD;
                $response = $processPayment->pay($request);
                // dd($response);

                break;
            case 'gcash_p2p_qr': // primary PG | min amount = 50 | (GCash QR Pay I)
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::GCASH_QR_III;
                $processPayment->bank_code = self::GCASH_WORD;
                $response = $processPayment->pay($request);

                break;
            case 'Maya': // min amount = 50
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::MAYAB;;
                $processPayment->bank_code = self::MAYA;
                $response = $processPayment->pay($request);
              
                  break;
     
            case self::CI_MANUAL: // min amount = 50
                $path = $request->file('file');
                $userMobile = auth()->user()->mobile_no;
                $name = 'STL Payment';
                //send mail
                $mail = Mail::to($this->emails)->bcc($this->bcc)->send(new MailableName($name, $path , $userMobile, $request->amount));
                $processPayment = new ManualPaymentGateway();
                $response = $processPayment->deposit($request);

                return redirect()->back()->with('success', 'Your Cashin is being processed. Please Wait.');
                  break;
            default:

                return response([
                    'status'=> false,
                    'redirect_url'=> 'Page Error',
                ],404);
        }

        
        $responseToArray = json_decode($response, true);
        // dd($responseToArray);
            
        if($responseToArray['status'] == 1){
            
            return response([
                'status'=> true,
                'redirect_url'=> $responseToArray['redirect_url'],
            ],200);
        } else {

            return response([
                'status'=> false,
                'redirect_url'=> $responseToArray['message'],
            ],200);
        }
        
    }
}
