<?php

namespace App\Http\Controllers;

use App\Models\Callbacks;
use App\Models\Winners1ds;
use App\Models\Winners2ds;
use App\Models\Winners3ds;
use App\Models\Winners4ds;
use App\Models\Winners6ds;
use App\Models\Winnersjais;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\Winnerspick2ds;
use App\Models\AdminWithdrawals;
use App\Models\JdPaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use App\Models\LuckypayPaymentGateway;

class PayoutController extends Controller
{
    const GCASH_P2P = 3;
    const GCASH_DIRECT = 12;
    const MAYAB = 3;
    const FAST_DIRECT = 1;
    const GCASH_QR_III = 7;
    const GCASH_WORD = 'gcash';
    const MAYA = 'PMP';
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request): JsonResponse
    {
        

        // process_type
        if(auth()->user()->user_level < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        $amountRegistered = 0;

        if($request->play == 'admin_co'){
            $winner = AdminWithdrawals::find($request->id);
            // $winner->winning_amount = $winner->amount;
            $amountRegistered = $winner->amount;
            $play = '0';
        } else{
            $winner = Winners6ds::find($request->id);
            $amountRegistered = $winner->winning_amount;
            $play = '6';
        }
        if($winner->process_status == 'processing'){

            return $this->sendResponse('error', 'This transaction is being processed.');
        }


        if($request->process_type == 'manual'){
            $winner->claimed = 1;
            $winner->process_status = 'success';
            $winner->approved = auth()->user()->id;
            $winner->save(); 
            
            return $this->sendResponse('success', $winner);
        }
        #JD Pay Payment Gateway
        if($request->pg == 'pg_4'){
            $taxedAmount = $amountRegistered * 2.5 / 100;
            $finalTaxedAmount = $amountRegistered - $taxedAmount - 8;
            // $bank_code = $winner->payout_remittance=='gcash_redirect'?'gcash_redirect':'PMP';
            // $transactionId = str_pad(rand(), 7, '0', STR_PAD_LEFT).'-'.'0';
            $bank_code = $winner->payout_remittance=='gcash'?'gcash':'PMP';
            $transactionId = str_pad(rand(), 6, '0', STR_PAD_LEFT).'_'.$winner->id.'_'.$play;
            $processPayment = new JdPaymentGateway();
            $processPayment->amount = round($finalTaxedAmount, 2); 
            $processPayment->order_id = $transactionId;
            $processPayment->bank_code = $bank_code;
            $processPayment->bank_card_name = $winner->receiver_name;
            $processPayment->bank_card_account = $winner->receiver_mobile_number;
            $processPayment->return_url = URL::to("/");
            $response = $processPayment->processPayout();
            $saveData = [
                        'code' => 0, //this is for successful transaction
                        'msg' => 'success', 
                        'merchant_slug' => 'jd_pay', 
                        'merchant_order_num' => $transactionId, 
                        'action' => 'deposit_attempt', 
                        'order' => json_encode($response), 
                        'user_id' => auth()->user()->id, 
                    ];
            Callbacks::create($saveData);
            $winner->merchant_order_no = $transactionId;
            $winner->approved = auth()->user()->id;
            $winner->process_status = 'processing';
            $winner->save(); 

            return $this->sendResponse('success', $response);
        }

        $processPayment = new PaymentGateway;
        $response = $processPayment->processPayout($winner, $play);
        if($response['code'] === 0){
            if($response['status'] == 'success'){
                $winner->merchant_order_no = $response['merchant_order_no'];
            } else {
                // $winner->claimed = null;
                $winner->merchant_order_no = $response['merchant_order_no'];
            }
        }
        $winner->process_status = 'processing';
        $winner->approved = auth()->user()->id;
        $winner->save(); 
        if($play == 0){
            $this->record(self::PROCESS_ADMIN_WITHDRAWAL_REQUEST_ITEM,$winner);
        }else{
            $this->record(self::PROCESS_WINNER,$winner);
        }
        
        return $this->sendResponse('success', $response);
    } 
    public function reprocess(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->user_level < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        if($request->play == 'admin_co'){
            $winner = AdminWithdrawals::find($request->id);
            $play = '0';
        } else{
            $winner = Winners6ds::find($request->id);
            $play = '6';
        }

        $winner->claimed = 0;
        $winner->approved = 0;
        $winner->process_status = 'processing';
        $winner->payout_remittance = '';
        $winner->receiver_name = '';
        $winner->receiver_mobile_number = '';
        $winner->merchant_order_no = '';
        $winner->save(); 
        if($play == 0){
            $this->record(self::RE_PROCESS_ADMIN_WITHDRAWAL_REQUEST_ITEM,$winner);
        } else {
            $this->record(self::REPROCESS_WINNER,$winner);
        }
        
        return $this->sendResponse('success', $winner);
    } 
    public function delete_record(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->user_level < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        $winner = AdminWithdrawals::find($request->id);
        $winner->delete(); 
        $this->record(self::DELETE_WITHDRAWAL_REQUEST_ITEM,$winner);
        
        return $this->sendResponse('success', $winner);
    } 
    public function delete_winner(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->user_level < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        if($request->play == 'admin_co'){
            $winner = AdminWithdrawals::find($request->id);
        } else{
            $winner = Winners6ds::find($request->id);
        }
        $winner->delete();

        $this->record(self::DELETE_WINNER,$winner);
        
        return $this->sendResponse('success', $winner);
    } 
    public function processReferral(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->user_level < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        $winner = Withdrawals::find($request->id);
        if($winner->process_status == 'processing'){

            return $this->sendResponse('error', 'This transaction is being processed.');
        }

        $play = '0_'.$winner->user_id;
        if($request->pg == 'pg_4'){
            $taxedAmount = $winner->amount * 2.5 / 100;
            $finalTaxedAmount = $winner->amount - $taxedAmount - 8;
            switch ($winner->payout_remittance) {

                #JDPay
                case 'GCash Fast': // primary PG | min amount = 50 | (JDpay)
                    $bank_code = self::GCASH_WORD;

                    break;
                case 'gcash': // primary PG | min amount = 50 | (GCash Easy Pay I)
                     $bank_code = self::GCASH_WORD;

                    break;
                case 'gcash_p2p': // primary PG | min amount = 50 | (GCash P2P/QR PAY I)
                     $bank_code = self::GCASH_WORD;

                    break;
                case 'gcash_p2p_qr': // primary PG | min amount = 50 | (GCash QR Pay I)
                     $bank_code = self::GCASH_WORD;

                    break;
                case 'Maya': // min amount = 50
                    $bank_code = self::MAYA;
                
                    break;
                default:
                    $bank_code = self::GCASH_WORD;
            }
            // $bank_code = $winner->payout_remittance=='gcash'?'gcash':'PMP';
            $transactionId = str_pad(rand(), 7, '0', STR_PAD_LEFT).'_'.'0';
            $processPayment = new JdPaymentGateway();
            $processPayment->amount = round($finalTaxedAmount, 2); 
            $processPayment->order_id = $transactionId;
            $processPayment->bank_code = $bank_code;
            $processPayment->bank_card_name = $winner->receiver_name;
            $processPayment->bank_card_account = $winner->receiver_mobile_number;
            $processPayment->return_url = URL::to("/");
            $response = $processPayment->processPayout();
            // dd($response);
            $saveData = [
                'code' => 0, //this is for successful transaction
                'msg' => 'processing', 
                'merchant_slug' => 'luckypay', 
                'merchant_order_num' => $transactionId, 
                'action' => 'deposit_attempt', 
                'order' => json_encode($response), 
                'user_id' => auth()->user()->id, 
            ];
            Callbacks::create($saveData);
            if($response['status']==1){
                $winner->merchant_order_no = $transactionId;
                $winner->process_status = 'processing';
                $winner->approved = auth()->user()->id;
                $winner->save(); 
            } 

            return $this->sendResponse('success', $response);
        }


        $processPayment = new PaymentGateway;
        $response = $processPayment->agentsPayout($winner, $play);
        if($response['code'] === 0){
            if($response['status'] == 'success'){
                $winner->merchant_order_no = $response['merchant_order_no'];
            } else {
                // $winner->claimed = null;
                $winner->merchant_order_no = $response['merchant_order_no'];
            }
        }
        $winner->process_status = 'processing';
        $winner->approved = auth()->user()->id;
        $winner->save(); 
        $this->record(self::PROCESS_AGENT_WIHDRAWAL,$winner);
        
        return $this->sendResponse('success', $response);
    } 
    public function deleteReferral(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->user_level < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        $winner = Withdrawals::find($request->id);
        if($winner){
            $winner->delete(); 

            return $this->sendResponse('error', 'Record removed');
        }

        return $this->sendResponse('error', 'Invalid record.');
    } 
}
