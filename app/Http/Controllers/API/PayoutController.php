<?php

namespace App\Http\Controllers\API;

use App\Models\Winners1ds;
use App\Models\Winners2ds;
use App\Models\Winners3ds;
use App\Models\Winners4ds;
use App\Models\Winners6ds;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\AdminWithdrawals;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Winnerspick2ds;

class PayoutController extends BaseController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->userlevel < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        if($request->play == 'PLAY 2D'){
            $winner = Winners2ds::find($request->id);
            $play = '2';
        } elseif($request->play == 'PLAY 3D'){
            $winner = Winners3ds::find($request->id);
            $play = '3';
        } elseif($request->play == 'PLAY 4D'){
            $winner = Winners4ds::find($request->id);
            $play = '4';
        } elseif($request->play == 'admin_co'){
            $winner = AdminWithdrawals::find($request->id);
            $play = '0';
        } else{
            $winner = Winners6ds::find($request->id);
            $play = '6';
        }

        if($request->process_type == 'manual'){
            $winner->claimed = 1;
            $winner->process_status = 'success';
            $winner->approved = auth()->user()->id;
            $winner->save(); 
            
            return $this->sendResponse('success', $winner);
        }

        $return_url = URL::to("/");
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
        $winner->process_status = $response['status'];
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
        // if(auth()->user()->userlevel < $this->accounting){

        //     return $this->sendResponse('error', 'You are not allowed!.');
        // }
        if($request->play == 'PLAY 2D'){
            $winner = Winners2ds::find($request->id);
            $play = '2';
        } elseif($request->play == 'PLAY 3D'){
            $winner = Winners3ds::find($request->id);
            $play = '3';
        }elseif($request->play == 'PLAY PICK 1'){
            $winner = Winners1ds::find($request->id);
            $play = '5';
        }elseif($request->play == 'PLAY PICK 2'){
            $winner = Winnerspick2ds::find($request->id);
            $play = '6';
        } elseif($request->play == 'PLAY 4D'){
            $winner = Winners4ds::find($request->id);
            $play = '4';
        } elseif($request->play == 'admin_co'){
            $winner = AdminWithdrawals::find($request->id);
            $play = '0';
        } elseif($request->play == 'agent_withdrawals'){
            $winner = Withdrawals::find($request->id);
            $play = '0';
        } else{
            $winner = Winners6ds::find($request->id);
            $play = '6';
        }

        $winner->claimed = 0;
        $winner->approved = 0;
        $winner->process_status = 'pending';
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
        if(auth()->user()->userlevel < $this->accounting){

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
        if(auth()->user()->userlevel < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        if($request->play == 'PLAY 2D'){
            $winner = Winners2ds::find($request->id);
        } elseif($request->play == 'PLAY 3D'){
            $winner = Winners3ds::find($request->id);
        } elseif($request->play == 'PLAY PICK 1'){
            $winner = Winners1ds::find($request->id);
        } elseif($request->play == 'PLAY PICK 2'){
            $winner = Winnerspick2ds::find($request->id);
        } elseif($request->play == 'PLAY 4D'){
            $winner = Winners4ds::find($request->id);
        } elseif($request->play == 'admin_co'){
            $winner = AdminWithdrawals::find($request->id);
        } else{
            $winner = Winners6ds::find($request->id);
        }
        $winner->delete();

        $this->record(self::DELETE_WINNER,$winner);
        
        return $this->sendResponse('success', $winner);
    } 
    public function complete_winner(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->userlevel < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        if($request->play == 'PLAY 2D'){
            $winner = Winners2ds::find($request->id);
        } elseif($request->play == 'PLAY 3D'){
            $winner = Winners3ds::find($request->id);
        } elseif($request->play == 'PLAY PICK 1'){
            $winner = Winners1ds::find($request->id);
        } elseif($request->play == 'PLAY PICK 2'){
            $winner = Winnerspick2ds::find($request->id);
        } elseif($request->play == 'PLAY 4D'){
            $winner = Winners4ds::find($request->id);
        } elseif($request->play == 'admin_co'){
            $winner = AdminWithdrawals::find($request->id);
        } else{
            $winner = Winners6ds::find($request->id);
        }
        $winner->process_status = 'success';
        $winner->save();

        $this->record(self::COMPLETE_WINNER,$winner);
        
        return $this->sendResponse('success', $winner);
    } 
    public function approve_winner(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->userlevel < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }

        if($request->play == 'PLAY 4D'){
            $winner = Winners4ds::find($request->id);
            $winner->process_status = 'pending';
            $winner->save();
            $this->record(self::DELETE_WINNER,$winner);
        } else{
            
            return $this->sendResponse('success', "ERROR");
        } 
        
        return $this->sendResponse('success', $winner);
    } 
    public function reset_winner(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->userlevel < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }

        if($request->play == 'PLAY 4D'){
            $winner = Winners4ds::find($request->id);
            $winner->process_status = 'pending';
            $winner->merchant_order_no = '';
            $winner->approved = 0;
            $winner->save();
            $this->record(self::RESET_WINNER,$winner);
        } elseif ($request->play == 'PLAY 3D'){
            $winner = Winners3ds::find($request->id);
            $winner->process_status = 'pending';
            $winner->merchant_order_no = '';
            $winner->approved = 0;
            $winner->save();
            $this->record(self::RESET_WINNER,$winner);
        } else{
            
            return $this->sendResponse('success', "ERROR");
        } 
        
        return $this->sendResponse('success', $winner);
    } 
    public function processReferral(Request $request): JsonResponse
    {
        // process_type
        if(auth()->user()->userlevel < $this->accounting){

            return $this->sendResponse('error', 'You are not allowed!.');
        }
        $winner = Withdrawals::find($request->id);

        if($request->process_type == 'manual'){
            $winner->claimed = 1;
            $winner->process_status = 'success';
            $winner->approved = auth()->user()->id;
            $winner->save(); 
            
            return $this->sendResponse('success', $winner);
        }
        $play = '0-'.$winner->user_id;
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
        $winner->process_status = $response['status'];
        $winner->approved = auth()->user()->id;
        $winner->save(); 
        $this->record(self::PROCESS_AGENT_WIHDRAWAL,$winner);
        
        return $this->sendResponse('success', $response);
    } 
}
