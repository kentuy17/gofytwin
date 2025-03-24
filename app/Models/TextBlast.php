<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\SettingsModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TextBlast extends Model
{
    use HasFactory;
    protected $apiKey; // main
    protected $textBlast; // text blast api key
    protected $sender; // main
    protected $textBlastSender;
    protected $endpoint;
    protected $otp;
    protected $account;

    public function __construct(
    ){
        $settings = new SettingsModel();
        
        $this->textBlast = $settings->getValueFor('text_blast_api_key');

        #main api key parameters
        $this->sender = $settings->getValueFor('sms_sender');
        $this->apiKey = $settings->getValueFor('sms_api_key');
        $this->endpoint = $settings->getValueFor('sms_message_endpoint');


        $this->textBlastSender = $settings->getValueFor('text_blast_sender');
        
        $this->otp = $settings->getValueFor('otp_endpoint');
        $this->account = $settings->getValueFor('account_endpoint');
    }

    public function textResults($results, $draw_date){
        $yesterday = Carbon::yesterday();
        $draw_date = $yesterday->toDateString();
        $date = date_create($draw_date);
        $date_format = date_format($date,'M d, Y');

        $all_users = User::all()->take(10);
        foreach($all_users as $all_user){
            $parameters = array(
                'apikey' => $this->textBlast, //Your API KEY
                'number' => $all_user->mobile_no,
                'message' => $message,
                'sendername' => $this->textBlastSender
            );
            $response = Http::post($this->endpoint, $parameters);
        }
    }
    public function checkBalance(){
        $parameters = array(
            'apikey' => '0c289de0d086ed72d711502c787a3bb7' //Your API KEY
        );

        //Show the server response
        $response = Http::get('https://api.semaphore.co/api/v4/account', $parameters);
        dd($response->body());
    }

    public function sendCreditsRegistrationText($mobile){
$message = <<<EOD
Ka-STL! You have successfully registered to PCSO-STL Online account with Mobile No. {$mobile}. You have now received a FREE BONUS of P20.00.
EOD;
        $ch = curl_init();
        $parameters = array(
            'apikey' => $this->textBlast, //Your API KEY
            'number' => $mobile,
            'message' => $message,
            'sendername' => $this->textBlastSender
        );
        curl_setopt( $ch, CURLOPT_URL, $this->endpoint);
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close ($ch);

        //Show the server response
        return $output;
    }

    public function happyHolidaysText($mobile){
$message = <<<EOD
Ka-STL! Salamat sa pagtangkilik ng PCSO-STL Online. Hatid namin sa inyo ang Hot Numbers sa buwan ng Enero.

2D- 09,09/09,18
3D- 733/703/078
4D- 9814/1731
EOD;
        $ch = curl_init();
        $parameters = array(
            'apikey' => $this->textBlast, //Your API KEY
            'number' => $mobile,
            'message' => $message,
            'sendername' => $this->textBlastSender
        );
        curl_setopt( $ch, CURLOPT_URL, $this->endpoint);
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close ($ch);

        //Show the server response
        return $output;
    }

    public function loginText(){
        $mobile = auth()->user()->mobile_no;
        $url = url('/');
$message = <<<EOD
Welcome to GoFytWin! You have successfully registered your mobile number. Play Now: {$url}
EOD;
        $ch = curl_init();
        $parameters = array(
            'apikey' => $this->apiKey, //Your API KEY
            'number' => $mobile,
            'message' => $message,
            'sendername' => $this->sender
        );
        curl_setopt( $ch, CURLOPT_URL, $this->endpoint);
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close ($ch);

        //Show the server response
        return $output;
    }

    public function otpSend($mobile, $message, $code = '1234'){
                $ch = curl_init();
                $parameters = array(
                    'apikey' => $this->apiKey, //Your API KEY
                    'number' => $mobile,
                    'message' => $message,
                    'code' => $code,
                    'sendername' => $this->sender
                );
                curl_setopt( $ch, CURLOPT_URL, $this->otp);
                curl_setopt( $ch, CURLOPT_POST, 1 );
        
                //Send the parameters set above with the request
                curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );
        
                // Receive response from server
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                $output = curl_exec( $ch );
                curl_close ($ch);
        
                //Show the server response
                return $output;
    }
      


    public function send($mobile, $amount, $ref_no, $play, $draw_time, $date){
$message = <<<EOD
Your payment of ₱{$amount} to GoFytWin (STL) {$play} - Drawtime: {$draw_time} has been successfully processed on {$date}. Ref. Num. {$ref_no}. Thanks!
EOD;
        $str = substr($amount, 1);
        $curl = curl_init();
        $data = array(
        'api_key' => "2YDG7nny1mt5fOsJAJDTaQB0ybd",
        'api_secret' => "AEygGkj5BAUA48uxe6kYsweXcI89uaQsPEFPCz5Y",
        'text' => $message,
        'to' => "63".$str,
        'from' => "MOVIDER"
        );

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.movider.co/v1/sms",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    
}
