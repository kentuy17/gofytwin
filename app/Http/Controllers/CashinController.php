<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\BetsMail;
use App\Models\Topups;
use App\Models\Cashins;
use App\Models\Receipts;
use App\Mail\MailableName;
use Illuminate\Http\Request;
use App\Models\SettingsModel;
use App\Models\ProcessPayment;
use App\Models\AgentReportGrids;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreFileRequest;
use App\Models\ManualPaymentGateway;

class CashinController extends Controller
{
    protected $emails = [ 
            'gofytwin@gmail.com'
        ];
    protected $bcc = [
            'ryanletterbee@gmail.com',
        ];
    protected $draw_time;
    protected $play;
    protected $url = 'https://gofytwin.com/';
    public $settings;
    const GCASH_P2P = 3;
    const GCASH_DIRECT = 12;
    const MAYAB = 3;
    const SAFE = 3;
    const FAST_DIRECT = 1;
    const GCASH_QR_III = 7;
    const GCASH_WORD = 'gcash';
    const MAYA = 'PMP';
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

    public function cash_in(){
        $background_image = $this->settings->getValueFor('background_image');
        $balance = User::find(auth()->user()->id); 
        $balance = $balance->credits;
        
        return view(
            'cash-in',[
                'balance' => $balance,
                'settings' => $this->settings,
                'og_image' => $this->settings->getValueFor('og_image'),
                'home_banner' => null !== ($this->settings->getValueFor('cashin_banner')) ? $this->settings->getValueFor('cashin_banner') : 'images/homepage-banner.png',
                'background_image' => isset($background_image) ? $background_image : 'images/site-bg.png'
            ]
        );     
    }

    public function processPayment(StoreFileRequest $request){
        
        $return_url = URL::to("/");
        $response = false;
        // dd($request->amount);

        switch ($request->getPg()) {

            #JDPay
            case 'GCash Direct': // primary PG | min amount = 50 | (Fast Direct)
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::SAFE; // 3 = safe
                $processPayment->bank_code = self::GCASH_WORD;
                $response = $processPayment->pay($request);

                break;

            case 'GCash Fast': // primary PG | min amount = 50 | (JDpay)
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::FAST_DIRECT; // JDpay 1 = fast 
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
    public function processCashout(StoreFileRequest $request){
        
        $return_url = URL::to("/");
        $response = false;
        // dd($request->amount);

        switch ($request->getPg()) {

            #JDPay
            case 'GCash': // primary PG | min amount = 50 | (JDpay)
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
