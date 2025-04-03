<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Receipts;
use App\Mail\MailableName;
use App\Models\SettingsModel;
use App\Models\ProcessPayment;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreFileRequest;
use App\Models\ManualPaymentGateway;

class CashinController extends Controller
{
    protected $emails = [ 
            'playstlofficial@gmail.com'
        ];
    protected $bcc = [
            'ryanletterbee@gmail.com',
            'playonline.pcso@gmail.com',
            'omoyondennis@gmail.com',
            'jovelrsa@gmail.com',
            'ryanbarz89@gmail.com',
            'orlandmanubag01@gmail.com',
            'manubagadrian0399@gmail.com',
        ];
    protected $draw_time;
    protected $play;
    protected $url = 'https://playstl.net/';
    public $settings;
    const GCASH_P2P = 3;
    const GCASH_DIRECT = 12;
    const GCASH_QR_III = 7;
    const GCASH_WORD = 'gcash';
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
        // $balance = $balance->credits->amount;
        $balance = 0;
        
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
            case 'gcash_redirect': // primary PG | min amount = 50 | (GCash Easy Pay I)
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = self::GCASH_DIRECT; // JDpay 3 = Fast Direct, 1 = Qrcode (same as LukyPay) | JDpay want to use either 7 or 12
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
            case 'gcash_qr': // SECONDARY PG | min amount = 100 | (GCash Pay II)
                #secondary PG
                $processPayment = new Receipts;
                $pg = $this->settings->getValueFor('gcash_gateway');
                $response = $processPayment->pay($request->amount, $pg, $return_url);
                // dd($response);

                return redirect($response);
                break;
            case 'paymaya_1': // min amount = 50
                $processPayment = new ProcessPayment();
                $processPayment->payment_type = 3;
                $processPayment->bank_code = 'PMP';
                $response = $processPayment->pay($request);
              
                  break;
            case 'paymaya_2': // SECONDARY PG | min amount = 100
                $processPayment = new Receipts;
                $pg = $this->settings->getValueFor('paymaya_gateway');
                $response = $processPayment->pay($request->amount, $pg, $return_url);

                return redirect($response);
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

                return redirect()->back()->with('error', 'An error has occured.');
        }

        $responseToArray = json_decode($response, true);
        // dd($responseToArray);
            
        if($responseToArray['status'] == 1){
            

            return redirect($responseToArray['redirect_url']);
        } else {
            
            return redirect()->back()->with('error', $responseToArray['message']);
        }

        return redirect()->back()->with('error', 'Unable to process request');
    }
}
