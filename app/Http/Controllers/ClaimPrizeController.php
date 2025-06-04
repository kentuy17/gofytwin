<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SubAgents;
use App\Models\TextBlast;
use App\Models\Winners2ds;
use App\Models\Winners3ds;
use App\Models\Winners4ds;
use App\Models\Winners6ds;
use App\Models\Withdrawals;
use App\Models\MasterAgents;
use Illuminate\Http\Request;
use App\Models\SettingsModel;
use App\Models\Winners1ds;
use App\Models\Winnerspick2ds;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClaimPrizeController extends Controller
{
    protected $settings;
    protected $background_image = "images/site-bg.png";
    const REMMITANCE_AMOUNT = 50000;

    public function __construct(
        SettingsModel $settings
    ){
        $this->settings = $settings;
        $this->background_image = $this->settings->getValueFor('background_image');
    }
    public function congratulations(){
        // dd(Session::get('claim_prize.winner')->winning_amount);
        // dd(Session::get('claim_prize'));
        if(null === Session::get('claim_prize')){
            return redirect('/');
        }   
        return view(
            'claim.index',[
                'amount_determiner' => self::REMMITANCE_AMOUNT,
                'background_image' => $this->background_image
            ]
        );
    }
    public function claimPrize(){
        return view(
            'claim.claim-prize',[
                'background_image' => $this->background_image,
                'app_url' => self::MOBILE_APP_URL,
                'og_image' => $this->settings->getValueFor('og_image'),
            ]
        );
    }
    // actual claiming
    public function checkPrize(Request $request){
        
        $input = $request->all();
        // dd($input);
        $error = array();
        $errorDraw = '';
        $claimed = '';

        $validator = Validator::make($input, [
            'reference_no' => 'required|numeric',
            'contact_no' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return back()->withErrors(array('error' => 'The Reference and Contact No. are Required.'));
        }

        $reference_no = $request->input('reference_no');
        $contact_no = $request->input('contact_no');
        if(
            Winners2ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )
                        ->where('claimed',1)
                        ->count() > 0
        ){

            return redirect('/congratulations');
        } elseif(
            Winnerspick2ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )
                        ->where('claimed',1)
                        ->count() > 0
        ){
            
            return redirect(route('claim.success'));
        } elseif(
            Winners1ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )
                        ->where('claimed',1)
                        ->count() > 0
        ){
            
            return redirect(route('claim.success'));
        } elseif(
            Winners3ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )
                        ->where('claimed',1)
                        ->count() > 0
        ){
            
            return redirect(route('claim.success'));
        } elseif(
            
            Winners4ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )
                        ->where('claimed',1)
                        ->count() > 0
        ){
            
            return redirect('/congratulations');
        } elseif(
            
            Winners6ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )
                        ->count() > 0
        ){

            $total_6d_ref_winners = Winners6ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )->count();
            $total_6d_ref_claimed = Winners6ds::where('reference_no', $reference_no)
                        ->where('contact_no', $contact_no )->where('claimed',1)->count();
            if($total_6d_ref_claimed == $total_6d_ref_winners){

                return redirect('/congratulations');
            }
        }

        if(Winners2ds::where('reference_no', $reference_no)->where('claimed',0)->count() > 0) {
            $winCombs = '';
            if($winner2d = Winners2ds::where(['reference_no' => $reference_no, 'contact_no' => $contact_no, 'claimed' => 0])) {
                $total_winning = bcdiv($winner2d->sum('winning_amount'), 1, 2);
                // dd($winner2d);
                foreach($winner2d->get() as $winner){
                    $winCombs.= $winner->winning_combination.' = ₱'.number_format($winner->bet_amount,2, '.',',').' | ';
                }
                $claimed = $winner2d->first()->claimed;
                if($claimed==0) {
                    $draw_time = $winner2d->first()->draw_time;
                    $draw_date = $winner2d->first()->draw_date;
                    Session::put('claim_prize',[
                        'winner'=>substr($winCombs, 0, -2),
                        'draw_time'=>$draw_time,
                        'draw_date'=>$draw_date,
                        'total'=>$total_winning,
                        'reference_no' => $reference_no,
                        'contact_no' => $contact_no,
                        'draw_type' => '2D'
                    ]);
                    
                    return redirect('/congratulations');
                } else {
                    $error['error'] = 'Your prized is already claimed.';
                    return back()
                        ->withErrors($error);
                }
                
            } else {
                $error['error'] = 'Invalid contact number.';
                return back()
                        ->withErrors($error);
            }
        } 

        if(Winners1ds::where('reference_no', $reference_no)->where('claimed',0)->count() > 0) {
            if($winner3d = Winners1ds::where(['reference_no' => $reference_no, 'contact_no' => $contact_no, 'claimed' => 0])) {
                // dd($winner3d);
                $total_winning = bcdiv($winner3d->sum('winning_amount'), 1, 2);
                $winCombs = '';
                foreach($winner3d->get() as $winner){
                    $winCombs.= $winner->winning_combination.' ('.$winner->combination_type.')'.' = ₱'.number_format($winner->bet_amount,2, '.',',').' | ';
                }
                $claimed = $winner3d->first()->claimed;

                if($claimed==0) {
                    $draw_time = $winner3d->first()->draw_time;
                    $draw_date = $winner3d->first()->draw_date;
                    Session::put('claim_prize',[
                        'winner'=>substr($winCombs, 0, -2),
                        'draw_time'=>$draw_time,
                        'draw_date'=>$draw_date,
                        'total'=>$total_winning,
                        'reference_no' => $reference_no,
                        'contact_no' => $contact_no,
                        'draw_type' => 'PICK1'
                    ]);
                    
                    return redirect('/congratulations');
                } else {
                    $error['error'] = 'Your prized is already claimed.';
                    return back()
                        ->withErrors($error);
                }

            } else {
                $error['error'] = 'Invalid contact number.';
                return back()
                        ->withErrors($error);
            }
        }
        if(Winnerspick2ds::where('reference_no', $reference_no)->where('claimed',0)->count() > 0) {
            if($winner3d = Winnerspick2ds::where(['reference_no' => $reference_no, 'contact_no' => $contact_no, 'claimed' => 0])) {
                // dd($winner3d);
                $total_winning = bcdiv($winner3d->sum('winning_amount'), 1, 2);
                $winCombs = '';
                foreach($winner3d->get() as $winner){
                    $winCombs.= $winner->winning_combination.' ('.$winner->combination_type.')'.' = ₱'.number_format($winner->bet_amount,2, '.',',').' | ';
                }
                $claimed = $winner3d->first()->claimed;

                if($claimed==0) {
                    $draw_time = $winner3d->first()->draw_time;
                    $draw_date = $winner3d->first()->draw_date;
                    Session::put('claim_prize',[
                        'winner'=>substr($winCombs, 0, -2),
                        'draw_time'=>$draw_time,
                        'draw_date'=>$draw_date,
                        'total'=>$total_winning,
                        'reference_no' => $reference_no,
                        'contact_no' => $contact_no,
                        'draw_type' => 'PICK2'
                    ]);
                    
                    return redirect('/congratulations');
                } else {
                    $error['error'] = 'Your prized is already claimed.';
                    return back()
                        ->withErrors($error);
                }

            } else {
                $error['error'] = 'Invalid contact number.';
                return back()
                        ->withErrors($error);
            }
        }


        if(Winners3ds::where('reference_no', $reference_no)->where('claimed',0)->count() > 0) {
            if($winner3d = Winners3ds::where(['reference_no' => $reference_no, 'contact_no' => $contact_no, 'claimed' => 0])) {
                // dd($winner3d);
                $total_winning = bcdiv($winner3d->sum('winning_amount'), 1, 2);
                $winCombs = '';
                foreach($winner3d->get() as $winner){
                    $winCombs.= $winner->winning_combination.' = ₱'.number_format($winner->bet_amount,2, '.',',').' | ';
                }
                $claimed = $winner3d->first()->claimed;

                if($claimed==0) {
                    $draw_time = $winner3d->first()->draw_time;
                    $draw_date = $winner3d->first()->draw_date;
                    Session::put('claim_prize',[
                        'winner'=>substr($winCombs, 0, -2),
                        'draw_time'=>$draw_time,
                        'draw_date'=>$draw_date,
                        'total'=>$total_winning,
                        'reference_no' => $reference_no,
                        'contact_no' => $contact_no,
                        'draw_type' => '3D'
                    ]);
                    
                    return redirect('/congratulations');
                } else {
                    $error['error'] = 'Your prized is already claimed.';
                    return back()
                        ->withErrors($error);
                }

            } else {
                $error['error'] = 'Invalid contact number.';
                return back()
                        ->withErrors($error);
            }
        }

        if(Winners4ds::where('reference_no', $reference_no)->where('claimed',0)->count() > 0) {
            if($winner4d = Winners4ds::where(['reference_no' => $reference_no, 'contact_no' => $contact_no, 'claimed' => 0])) {
                $total_winning = bcdiv($winner4d->sum('winning_amount'), 1, 2);
                $winCombs = '';
                foreach($winner4d->get() as $winner){
                    $winCombs.= $winner->winning_combination.' = ₱'.number_format($winner->bet_amount,2, '.',',').' | ';
                }
                $claimed = $winner4d->first()->claimed;
                if($claimed==0) {
                    $draw_time = $winner4d->first()->draw_time;
                    $draw_date = $winner4d->first()->draw_date;
                    Session::put('claim_prize',[
                        'winner'=>substr($winCombs, 0, -2),
                        'draw_time'=>$draw_time,
                        'draw_date'=>$draw_date,
                        'total'=>$total_winning,
                        'reference_no' => $reference_no,
                        'contact_no' => $contact_no,
                        'draw_type' => '4D'
                    ]);
                    
                    return redirect('/congratulations'); 
                } else {
                    $error['error'] = 'Your prized is already claimed.';
                    return back()
                        ->withErrors($error);
                }
            } else {
                $error['error'] = 'Invalid contact number.';
                return back()
                        ->withErrors($error);
            }
        }
        if(Winners6ds::where('reference_no', $reference_no)->where('claimed',0)->count() > 0) {
            if($winner6d = Winners6ds::where(['reference_no' => $reference_no, 'contact_no' => $contact_no, 'claimed' => 0])) {
                $total_winning = bcdiv($winner6d->sum('winning_amount'), 1, 2);
                $winCombs = '';
                foreach($winner6d->get() as $winner){
                    $winCombs.= $winner->winning_combination.' = ₱'.number_format($winner->bet_amount,2, '.',',').' | ';
                }
                $claimed = $winner6d->first()->claimed;
                if($claimed==0) {
                    $draw_time = $winner6d->first()->draw_time;
                    $draw_date = $winner6d->first()->draw_date;
                    Session::put('claim_prize',[
                        'winner'=>substr($winCombs, 0, -2),
                        'draw_time'=>$draw_time,
                        'draw_date'=>$draw_date,
                        'total'=>$total_winning,
                        'reference_no' => $reference_no,
                        'contact_no' => $contact_no,
                        'draw_type' => '6D'
                    ]);
                    
                    return redirect('/congratulations'); 
                } else {
                    $error['error'] = 'Your prized is already claimed.';
                    return back()
                        ->withErrors($error);
                }
            } else {
                $error['error'] = 'Invalid contact number.';
                return back()
                        ->withErrors($error);
            }
        }
    
        $error['error'] = 'Invalid Reference / Mobile Number';
        return back()
                ->withErrors($error);

    }
 
    public function process(Request $request){
        if(null === Session::get('claim_prize')){

            return back()->withErrors(array('error' => 'The Reference and Contact No. are Required.'));
        }
        
        $input = $request->all();
        $validator = Validator::make($input, [
            'payout_remittance' => 'required',
            'account_name' => 'required',
            'receiver_mobile_number' => 'required|numeric',
        ]);

        if($validator->fails()) {

            return back()->withErrors(array('error' => 'The Reference and Contact No. are Required.'));
        }
        $data = Session::get('claim_prize');
        // dd($data);
        $textblast = new TextBlast;
        $claim = $request->all();
        if($data['draw_type'] == '2D') {
            $result_obj = Winners2ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no']);
            
            if($result_obj->first()->claimed == 1) {

                return redirect('/claim-prize/success');
            }else {
                Winners2ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no'])->update([
                    'claimed' => 1,
                    'payout_remittance' => $claim['payout_remittance'],
                    'receiver_name' => $claim['account_name'],
                    'claimed_date' => Carbon::now(),
                    'receiver_mobile_number' => $claim['receiver_mobile_number']
                ]);
                // $textblast->claimSuccess($result_obj->first());
                Session::forget('claim_prize');
                
                return redirect('/claim-prize/success');
            }        
        } else if($data['draw_type'] == '3D') {
            $result_obj = Winners3ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no']);
                                
            if($result_obj->first()->claimed == 1){

                return redirect('/claim-prize/success');
            } else {
                Winners3ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no'])->update([
                    'claimed' => 1,
                    'payout_remittance' => $claim['payout_remittance'],
                    'receiver_name' => $claim['account_name'],
                    'claimed_date' => Carbon::now(),
                    'receiver_mobile_number' => $claim['receiver_mobile_number']
                ]);
                // $textblast->claimSuccess($result_obj->first());
                Session::forget('claim_prize');

                return redirect('/claim-prize/success');
            }
        }else if($data['draw_type'] == 'PICK2') {
            $result_obj = Winnerspick2ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no']);
                                
            if($result_obj->first()->claimed == 1){

                return redirect('/claim-prize/success');
            } else {
                Winnerspick2ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no'])->update([
                    'claimed' => 1,
                    'payout_remittance' => $claim['payout_remittance'],
                    'receiver_name' => $claim['account_name'],
                    'claimed_date' => Carbon::now(),
                    'receiver_mobile_number' => $claim['receiver_mobile_number']
                ]);
                // $textblast->claimSuccess($result_obj->first());
                Session::forget('claim_prize');

                return redirect('/claim-prize/success');
            }
        }else if($data['draw_type'] == 'PICK1') {
            $result_obj = Winners1ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no']);
                                
            if($result_obj->first()->claimed == 1){

                return redirect('/claim-prize/success');
            } else {
                Winners1ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no'])->update([
                    'claimed' => 1,
                    'payout_remittance' => $claim['payout_remittance'],
                    'receiver_name' => $claim['account_name'],
                    'claimed_date' => Carbon::now(),
                    'receiver_mobile_number' => $claim['receiver_mobile_number']
                ]);
                // $textblast->claimSuccess($result_obj->first());
                Session::forget('claim_prize');

                return redirect('/claim-prize/success');
            }
        } else if($data['draw_type'] == '4D') {
            $result_obj = Winners4ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no']);
                                   
            if($result_obj->first()->claimed == 1) {

            } else {
                $tae = Winners4ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no'])->update([
                    'claimed' => 1,
                    'payout_remittance' => $claim['payout_remittance'],
                    'receiver_name' => $claim['account_name'],
                    'claimed_date' => Carbon::now(),
                    'receiver_mobile_number' => $claim['receiver_mobile_number']
                ]);
                // $textblast->claimSuccess($result_obj->first());
                Session::forget('claim_prize');

                return redirect('/claim-prize/success');
            } 
        } else if($data['draw_type'] == '6D') {
            $result_obj = Winners6ds::where('reference_no', $data['reference_no'])
                                      ->where('contact_no', $data['contact_no'])
                                      ->where('claimed',0);
                                   
            if($result_obj->first()->claimed == 1) {

            } else {
                Winners6ds::where('reference_no', $data['reference_no'])
                                ->where('contact_no', $data['contact_no'])
                                ->where('claimed',0)->update([
                    'claimed' => 1,
                    'payout_remittance' => $claim['payout_remittance'],
                    'receiver_name' => $claim['account_name'],
                    'claimed_date' => Carbon::now(),
                    'receiver_mobile_number' => $claim['receiver_mobile_number']
                ]);
                $result_obj = Winners6ds::where('reference_no', $data['reference_no'])
                                      ->where('contact_no', $data['contact_no'])
                                      ->whereNot('payout_remittance','credits');
                // $textblast->claimSuccess($result_obj->first());
                Session::forget('claim_prize');

                return redirect('/claim-prize/success');
            } 
        }
    }
    public function processReferral(Request $request){
        // dd($request->all());
        $input = $request->all();
        #get current balnace of user requesting withdawal
        if($input['account_type'] == 'ma'){
            $currectBalance = MasterAgents::where('user_id', auth()->user()->id)->sum('total');
            $haspending = Withdrawals::where('user_id', auth()->user()->id)->whereNot('process_status', 'success')->count();
        } else {
            $currectBalance = SubAgents::where('user_id', auth()->user()->id)->sum('total');
            $haspending = Withdrawals::where('user_id', auth()->user()->id)->whereNot('process_status', 'success')->count();
        }
        if($input['value_amount'] < 500){

            return back()->with('error','Minimum cashout is ₱500.00.');
        }
        if($currectBalance < $input['value_amount']){

            return back()->with('error','Insufficient funds.');
        }
        if($haspending > 0){

            return back()->with('error','You already have one pending withdrawal request.');
        }

        $validator = Validator::make($input, [
            'payout_remittance' => 'required',
            'account_name' => 'required',
            'receiver_mobile_number' => 'required|numeric',
        ]);

        if($validator->fails()) {
            
            return back()->with('error',$validator->errors());
        }
        $textblast = new TextBlast;
        
        $claim = $input;
        $withdraw = Withdrawals::create([
            'user_id'=>auth()->user()->id,
            'amount'=>$claim['value_amount'],
            'reference_no'=>null,
            'contact_no'=>$claim['receiver_mobile_number'],
            'receipt'=>null,
            'claimed'=>0,
            'upload'=>0,
            'approved'=>0,
            'payout_remittance'=>$claim['payout_remittance'],
            'receiver_name'=>$claim['account_name'],
            'receiver_address'=>null,
            'receiver_mobile_number'=>$claim['receiver_mobile_number'],
            'merchant_order_no'=>null,
            'claimed_date'=>null,
            'process_status'=>'pending',
            'message'=>null,
        ]);

        #find master agent or sub agent
        $masterAgent = MasterAgents::where('user_id',auth()->user()->id)->first();
        if($masterAgent){
            $newBalance = $masterAgent->total - $claim['value_amount'];
            $masterAgent->total = $newBalance;
            $masterAgent->save();
        } 
        $subAgent = SubAgents::where('user_id',auth()->user()->id)->first();
        if($subAgent){
            $newBalance = $subAgent->total - $claim['value_amount'];
            $subAgent->total = $newBalance;
            $subAgent->save();
        }

        return back()->with('success', 'Your request has been submitted.');
    }

    public function success(){
        Session::forget('claim_prize');

        return view(
            'claim.success',[
                'background_image' => $this->background_image,
                'og_image' => $this->settings->getValueFor('og_image')
            ]
        );
    }
    public function successClaimed(){
        Session::forget('claim_prize');

        return view(
            'claim.success',[
                'background_image' => $this->background_image,
                'og_image' => $this->settings->getValueFor('og_image')
            ]
        );
    }

}
