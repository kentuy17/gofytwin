<?php

namespace App\Models;

use App\Models\JdPaymentGateway;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcessPayment extends Model
{
    use HasFactory;

    public $payment_type;
    public $bank_code;
    
    protected function generateTransactionId(){
        
        return str_pad(rand(), 6, '0', STR_PAD_LEFT).'_'.auth()->user()->id;
    }

    public function pay($request){
        $amount = $request->amount;
        $transactionId = $this->generateTransactionId();
        
        if($request->getPg() == 'gcash_redirect'){
            
            #JD Payment Gateway
            $pgModel = new JdPaymentGateway();
        } else {
            // $pgModel = new LuckypayPaymentGateway();
            # now removing LuckyPay and use JDpay instead. 
            $pgModel = new JdPaymentGateway(); //using this for future proof
        }
        $pgModel->payment_type = $this->payment_type; // 3 = fast direct, 1 = QR Code
        $pgModel->amount = $amount;
        $pgModel->return_url = URL::to("/");
        $pgModel->order_id = $transactionId;
        $pgModel->bank_code = $this->bank_code;// Gcash => gcash ; Paymaya=>PMP ; USDT-TRC=>usdt-trc
        $response = $pgModel->deposit();

        return $response;
    }

    

    
}
