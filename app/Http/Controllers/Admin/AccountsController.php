<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Topups;
use App\Models\Credits;
use App\Models\UserCis;
use App\Models\SubAgents;
use App\Models\MasterAgents;
use Illuminate\Http\Request;
use App\Resources\UserResource;
use App\Models\AgentReportGrids;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountsController extends Controller
{
    const SUPER_ADMIN = 5000;
    const ACCOUNTING = 2000;
    const OPERATIONS = 1000;
    const CSR = 50;
    const BETTORS = 0;
    public function user_attempts(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $q = $request->q;
        // $users = User::where('user_level', 0)->where(function($query) use ($q) {
        //     $query->where('id', 'LIKE', '%'.$q.'%')
        //         ->orWhere('mobile_no', 'LIKE', '%'.$q.'%')
        //         ->orWhere('ip', 'LIKE', '%'.$q.'%');
        // })->orderBy('id','desc')->paginate(50);
        $users = User::where('user_level', 0)->where('user_attepts',3)->whereNot('account_status','spam')->orderBy('id','desc')->paginate(100);

        return view(
            'admin.users.attempts',[
                'users' => $users,
                'q' => $q,
                'topup_model' => new Topups,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    
  
    public function cashin_today(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // dd($to);
        
        $users = DB::select("SELECT distinct cashins.user_id, 
                    cashins.status as ci_status,
                    users.mobile_no as mobile_no, 
                    cashins.created_at  as created_at 
                FROM
                    cashins  
                 JOIN users ON users.id=cashins.user_id 

                WHERE  cashins.created_at >= '".$from." 00:00:00' AND cashins.created_at <= '".$to." 23:59:59' AND cashins.status='success' GROUP BY users.mobile_no,cashins.created_at,cashins.user_id,ci_status ORDER BY created_at desc");

        return view(
            'admin.users.cashin-today',[
                'users' => $users,
                'topup_model' => new Topups,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    public function verification(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $q = $request->q;

        $from = Carbon::createFromDate(date('Y-m-d'));
        if($request->q){
            
            $user = User::select('id')->where('mobile_no', $q)->first();
            $users = VerificationCode::where('user_id', $user->id)->whereBetween('created_at', [$from->format('Y-m-d')." 00:00:00", $from->format('Y-m-d')." 23:59:59"])->orderBy('id','desc')->paginate(100);

        } else {
            $users = VerificationCode::whereBetween('created_at', [$from->format('Y-m-d')." 00:00:00", $from->format('Y-m-d')." 23:59:59"])->orderBy('id','desc')->paginate(100);
        }
        

        return view(
            'admin.users.verify',[
                'users' => $users,
                'q' => $q,
                'topup_model' => new Topups,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    public function inactive(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $q = $request->q;
        // $users = User::where('user_level', 0)->where(function($query) use ($q) {
        //     $query->where('id', 'LIKE', '%'.$q.'%')
        //         ->orWhere('mobile_no', 'LIKE', '%'.$q.'%')
        //         ->orWhere('ip', 'LIKE', '%'.$q.'%');
        // })->orderBy('id','desc')->paginate(50);
        $users = User::where('user_level', 0)->where('account_status','inactive')->orderBy('id','desc')->paginate(100);

        return view(
            'admin.users.spam',[
                'users' => $users,
                'q' => $q,
                'topup_model' => new Topups,
                'title' => 'Inactive',
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    public function spam(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $q = $request->q;
        // $users = User::where('user_level', 0)->where(function($query) use ($q) {
        //     $query->where('id', 'LIKE', '%'.$q.'%')
        //         ->orWhere('mobile_no', 'LIKE', '%'.$q.'%')
        //         ->orWhere('ip', 'LIKE', '%'.$q.'%');
        // })->orderBy('id','desc')->paginate(50);
        $users = User::where('user_level', 0)->where('account_status','spam')->orderBy('id','desc')->paginate(100);

        return view(
            'admin.users.spam',[
                'users' => $users,
                'q' => $q,
                'topup_model' => new Topups,
                'title' => 'Spam',
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    public function user_cis(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $q = $request->q;
        // $users = User::where('user_level', 0)->where(function($query) use ($q) {
        //     $query->where('id', 'LIKE', '%'.$q.'%')
        //         ->orWhere('mobile_no', 'LIKE', '%'.$q.'%')
        //         ->orWhere('ip', 'LIKE', '%'.$q.'%');
        // })->orderBy('id','desc')->paginate(50);
        $users = UserCis::all();

        return view(
            'admin.users.cis',[
                'users' => $users,
                'q' => $q,
                'topup_model' => new Topups,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    public function users(Request $request){
        // if(auth()->user()->user_level < self::ACCOUNTING){
            
        //     return redirect('home');
        // }
        $q = $request->q;
        $users = User::query()
        ->where('user_level', 0)->where(function($query) use ($q) {
                $query->where('id', 'LIKE', '%'.$q.'%')
                    ->orWhere('mobile_no', 'LIKE', '%'.$q.'%')
                    ->orWhere('ip', 'LIKE', '%'.$q.'%');
            })->orderBy('id','desc')->paginate($request->per_page ?? 50);
        $userss = UserResource::collection($users);

        return view(
            'admin.users.index',[
                'users' => $userss,
                'q' => $q,
                'topup_model' => new Topups,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    public function user_info(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $q = $request->q;
        $users = User::query()
        ->where('mobile_no', $q)->orderBy('id','desc')->paginate($request->per_page ?? 50);
        $userss = UserResource::collection($users);

        return view(
            'admin.users.user-info',[
                'users' => $userss,
                'q' => $q,
                'topup_model' => new Topups,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
    public function agents(){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        if(!in_array(auth()->user()->id,$this->allowed)){
            
            return redirect('home');
        }
        $users = User::where('user_level', 0)->whereNot('percentage',0);
        $total_sales = $users->count();

        return view(
            'admin.users.agents',[
                'users' => $users->get(),
                'topup_model' => new Topups,
                'total_sales' => $total_sales,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );

    }
    public function editUser(Request $request){
        $id = $request->id;
        $ref_no = isset($request->ref_no)?$request->ref_no:null;
        if(auth()->user()->user_level < 5000){
            
            return redirect('home');
        }
        $user = User::find($id);
        $total_ci = Topups::where('user_id',$user->id)->where('status','success')->sum('amount');
        $total_pick2 = Pick2ds::where('user_id',$user->id)->sum('amount');
        $total_bets1ds = Bets1ds::where('user_id',$user->id)->sum('amount');
        $total_bets2ds = Bets2ds::where('user_id',$user->id)->sum('amount');
        $total_bets3ds = Bets3ds::where('user_id',$user->id)->sum('amount');
        $total_bets4ds = Bets4ds::where('user_id',$user->id)->sum('amount');
        $total_bets6ds = Bets6ds::where('user_id',$user->id)->sum('amount');
        $total_sales = $total_bets2ds + $total_bets3ds + $total_bets4ds + $total_bets6ds + $total_pick2 + $total_bets1ds;
        $number_of_ips = User::where('ip',$user->ip)->count();
        if($post = $_POST) {
            $user->name = $post['name'];
            $user->email = $post['mobile_no'];
            $user->percentage = $post['percentage'];
            $user->account_status = $post['account_status'];
            $user->user_attepts = $post['user_attepts'];
            $user->three_d_bet_limit = $post['three_d_bet_limit'];
            $user->two_d_bet_limit = $post['two_d_bet_limit'];

            $creds = Credits::where('user_id', $id)->first();
            $creds->amount = $post['credits'];
            $creds->save();

            
            if(isset($post['password'])){
                if(Auth::user()->account_type=='super_admin'){
                    $user->password = Hash::make($post['password']);
                }
            }
            $user->mobile_no = $post['mobile_no'];
            
            // $user->active = $post['active'];
            $saved = $user->save();
            $this->record(self::UPDATE_USER,$user);
            if($saved){
                return back()->with('success','Record successfully updated.');
            } else {
                return back()->withErrors($saved);
            }
            
            
        }

        return view(
            'admin.users.edit',[
                'user' => $user,
                'total_ci' => $total_ci,
                'total_sales' => $total_sales,
                'ref_no' => $ref_no,
                'number_of_ips' => $number_of_ips,
            ]
        );
    }
    public function viewUser(Request $request){
        $id = $request->id;
        $ref_no = isset($request->ref_no)?$request->ref_no:null;
        $user = User::find($id);
        $total_ci = Topups::where('user_id',$user->id)->where('status','success')->sum('amount');
        $total_bets2ds = Bets2ds::where('user_id',$user->id)->sum('amount');
        $total_bets3ds = Bets3ds::where('user_id',$user->id)->sum('amount');
        $total_bets4ds = Bets4ds::where('user_id',$user->id)->sum('amount');
        $total_bets6ds = Bets6ds::where('user_id',$user->id)->sum('amount');
        $total_sales = $total_bets2ds + $total_bets3ds + $total_bets4ds + $total_bets6ds;
        $number_of_ips = User::where('ip',$user->ip)->count();
        

        return view(
            'admin.users.view',[
                'user' => $user,
                'total_ci' => $total_ci,
                'total_sales' => $total_sales,
                'ref_no' => $ref_no,
                'number_of_ips' => $number_of_ips,
            ]
        );
    }
    public function viewUserRole(Request $request){
        $id = $request->id;
        $ref_no = isset($request->ref_no)?$request->ref_no:null;
        if(auth()->user()->user_level < 5000){
            
            return redirect('home');
        }
        $user = User::find($id);
        $total_ci = Topups::where('user_id',$user->id)->where('status','success')->sum('amount');
        $total_bets2ds = Bets2ds::where('user_id',$user->id)->sum('amount');
        $total_bets3ds = Bets3ds::where('user_id',$user->id)->sum('amount');
        $total_bets4ds = Bets4ds::where('user_id',$user->id)->sum('amount');
        $total_bets6ds = Bets6ds::where('user_id',$user->id)->sum('amount');
        $total_sales = $total_bets2ds + $total_bets3ds + $total_bets4ds + $total_bets6ds;
        $number_of_ips = User::where('ip',$user->ip)->count();


        $info = MasterAgents::where('user_id',$id)->first();
        if($info){
            $role = "Master Agent";
        } else {
            $info = SubAgents::where('user_id',$id)->first();
            $role = "Sub Agent";
        } 
        
     

        return view(
            'admin.agents.role-view',[
                'info' => $info,
                'role' => $role,
                'user' => $user,
                'total_ci' => $total_ci,
                'total_sales' => $total_sales,
                'ref_no' => $ref_no,
                'number_of_ips' => $number_of_ips,
            ]
        );
    }
    public function addMasterAgent(Request $request){
        if(auth()->user()->user_level < 5000){
            
            return redirect('home');
        }
        $user = User::find($request->id);
        if(!$user){
            
            return redirect(route('statistics'))->withError('No user was found.');
        }
        if($user->percentage > 0){
            
            return redirect(route('statistics'))->withError('User is already an agent.');
        }
        $masterAgentObj = new MasterAgents;
        $ma = $masterAgentObj->where('user_id',$request->id)->first();
        if($ma){
            
            return redirect(route('master.agent.edit',$request->id));
        }

        if($post = $_POST) {
            $findCode = MasterAgents::where('referral_code', $post['referral_code'])->first();
            if($findCode){
            
                return back()->with('error',$post['referral_code'].' is lready Taken.');
            }
            $masterAgentObj->user_id = $user->id;
            $masterAgentObj->referral_code = $post['referral_code'];
            $masterAgentObj->account_number = $user->mobile_no;
            $masterAgentObj->percentage = $post['percentage'];
            $masterAgentObj->updated_by = auth()->user()->id;
            if($masterAgentObj->save()){
                $this->record(self::ASSIGN_AS_MASTER_AGENT,$masterAgentObj);

                return redirect(route('master.agent.edit',$user->id))->with('success',$user->id.' has now been added as Master Agent.');
            } else {

                return back()->withErrors($masterAgentObj->save());
            }
            
        }

        return view(
            'admin.agents.ma',[
                'user' => $user,
                'title' => 'Add Master Agent',
            ]
        );
    }
    public function editMasterAgent(Request $request){
        // dd($request->id);
        if(auth()->user()->user_level < 5000){
            
            return redirect('home');
        }
        $user = User::find($request->id);
        if(!$user){
            
            return redirect(route('statistics'))->withError('No user was found.');
        }
        
        $masterAgentObj = new MasterAgents;
        $ma = $masterAgentObj->where('user_id',$request->id)->first();
       
        if($post = $_POST) {
            $agentOj = new AgentReportGrids;
            $ma_grid = $agentOj->where('ma_id', $ma->user_id)->update(['ma_share'=>$post['percentage']]);

            $findCode = MasterAgents::where('referral_code', $post['referral_code'])->first();
            if($findCode && $request->id != $findCode->user_id){
            
                return back()->with('error',$post['referral_code'].' is lready Taken.');
            }
            // $ma_grid->ma_share = $post['percentage'];
            // $ma_grid->save();
            $ma->referral_code = $post['referral_code'];
            $ma->percentage = $post['percentage'];
            $ma->updated_by = auth()->user()->id;
            if($ma->save()){
                $this->record(self::EDIT_MASTER_AGENT,$ma);

                return redirect(route('master.agent.edit',$user->id))->with('Record updated.');
            } else {

                return back()->withErrors($ma->save());
            }
            
        }

        return view(
            'admin.agents.ma-edit',[
                'user' => $ma,
                'title' => 'Edit Master Agent',
            ]
        );
    }
    public function editSubAgent(Request $request){
        // dd($request->id);
        if(auth()->user()->user_level < 5000){
            
            return redirect('home');
        }
        $user = User::find($request->id);
        if(!$user){
            
            return redirect(route('statistics'))->withError('No user was found.');
        }
        $masterAgentObj = new MasterAgents;
        $masterAgentsList = $masterAgentObj->all();
        
        $subAgentObj = new SubAgents;
        $sa = $subAgentObj->where('user_id',$request->id)->first();
        $ma = $sa->master_agent_id;
        $ma_percentage = $masterAgentObj->where('user_id', $ma)->first()->percentage;

        if($post = $_POST) {
            $agentOj = new AgentReportGrids;
            // $sa_grid = $agentOj->where('sa_id', $sa->user_id);
            $final_ma_share = $ma_percentage - $post['percentage'];
            $ma_grid = $agentOj->where('sa_id', $sa->user_id)->update([
                                                                        'sa_share'=>$post['percentage'],
                                                                        'ma_share'=>$final_ma_share
                                                                    ]);

            $findCodeMa = MasterAgents::where('referral_code', $post['referral_code'])->first();
            if($findCodeMa){
            
                return back()->with('error',$post['referral_code'].' is lready Taken.');
            }
            $findCodeSa = SubAgents::where('referral_code', $post['referral_code'])->first();

            if($findCodeSa && $request->id != $findCodeSa->user_id){
            
                return back()->with('error',$post['referral_code'].' is lready Taken.');
            }
            // $sa_grid->sa_share = $post['percentage'];
            // $sa_grid->save();
            $sa->master_agent_id = $post['master_agent_id'];
            $sa->referral_code = $post['referral_code'];
            $sa->percentage = $post['percentage'];
            $sa->updated_by = auth()->user()->id;
            if($sa->save()){
                $this->record(self::EDIT_SUB_AGENT,$sa);

                return redirect(route('sub.agent.edit', $user->id))->with('success','Record updated.');
            } else {

                return back()->withErrors($sa->save());
            }
            
        }

        return view(
            'admin.agents.sa-edit',[
                'user' => $sa,
                'master_agent' => $masterAgentsList,
                'title' => 'Edit Sub Agent',
            ]
        );
    }
    public function saveSubAgent($id){
        if(auth()->user()->user_level < 50){
            
            return redirect('home');
        }
        $user = User::find($id);

        if($post = $_POST) {
            $user->name = $post['name'];
            $user->email = $post['mobile_no'];
            $user->percentage = $post['percentage'];
            $user->account_status = $post['account_status'];
            $user->user_attepts = $post['user_attepts'];

            $creds = Credits::where('user_id', $id)->first();
            $creds->amount = $post['credits'];
            $creds->save();

        
            if(isset($post['password'])){
                if(Auth::user()->account_type=='super_admin'){
                    $user->password = Hash::make($post['password']);
                }
            }
            $user->mobile_no = $post['mobile_no'];
            
            // $user->active = $post['active'];
            $saved = $user->save();
            if($saved){


                return back()->with('success','Record successfully updated.');
            } else {

                return back()->withErrors($saved);
            }
            
        }

        return view(
            'admin.users.edit',[
                'user' => $user,
            ]
        );
    }
    public function addSubAgent(Request $request){
        if(auth()->user()->user_level < 5000){
            
            return redirect('home');
        }
        $user = User::find($request->id);
        if(!$user){
            
            return redirect(route('statistics'))->withError('No user was found.');
        }
        if($user->percentage > 0){
            
            return redirect(route('statistics'))->withError('User is already an agent.');
        }
        $masterAgentObj = new MasterAgents;
        $masterAgentsList = $masterAgentObj->all();
        $ma = $masterAgentObj->where('user_id',$request->id)->first();
        if($ma){
            
            return redirect(route('statistics'))->withError('User is already a Master Agent.');
        }

        $subAgentObj = new SubAgents;
        $sa = $subAgentObj->where('user_id', $request->id)->first();
        if($sa){
            return redirect(route('sub.agent.edit',$request->id));
        }

        if($post = $_POST) {
            $findCodeMa = MasterAgents::where('referral_code', $post['referral_code'])->first();
            if($findCodeMa){
            
                return back()->with('error',$post['referral_code'].' is lready Taken.');
            }
            $findCodeSa = SubAgents::where('referral_code', $post['referral_code'])->first();

            if($findCodeSa){
            
                return back()->with('error',$post['referral_code'].' is lready Taken.');
            }
            $subAgentObj = new SubAgents;
            $subAgentObj->user_id = $user->id;
            $subAgentObj->referral_code = $post['referral_code'];
            $subAgentObj->account_number = $user->mobile_no;
            $subAgentObj->master_agent_id = $post['master_agent_id'];
            $subAgentObj->percentage = $post['percentage'];
            $subAgentObj->commision = 0;
            $subAgentObj->updated_by = auth()->user()->id;
            if($subAgentObj->save()){
                $this->record(self::ASSIGN_AS_SUB_AGENT,$subAgentObj);
                
                return redirect(route('master.agent.edit',$user->id))->with('success',$user->id.' has now been added as Master Agent.');
            } else {

                return back()->withErrors($subAgentObj->save());
            }
            
        }

        return view(
            'admin.agents.sa',[
                'user' => $user,
                'master_agent' => $masterAgentsList,
                'title' => 'Add Sub Agent',
            ]
        );
    }

    public function masterAgents(){
        
        if(auth()->user()->user_level < self::OPERATIONS){
            
            return redirect('home');
        }
        
        if(!in_array(auth()->user()->id,$this->allowed)){

            return redirect('home');
        }

        $users = MasterAgents::all();
        $total_sales = $users->count();
        

        return view(
            'admin.users.master-agents',[
                'users' => $users,
                'topup_model' => new Topups,
                'agentReportsObj' => new AgentReportGrids,
                'saObj' => new SubAgents,
                'maObj' => new MasterAgents,
                'userObj' => new User,
                'total_sales' => $total_sales,
                'label' => 'Master Agents',
                'agent_type' => 'master_agent',
                'super_admin' => self::SUPER_ADMIN,
            ]
        );

    }
    public function masterAgentsInfo(Request $request){
        if(auth()->user()->user_level < self::OPERATIONS){
            
            return redirect('home');
        }
        if(!in_array(auth()->user()->id,$this->allowed)){
            
            return redirect('home');
        }
        
        // $users = AgentReportGrids::where('ma_id',$request->id)->get([
        //                                                             'user_id',
        //                                                             'account_number',
        //                                                             'account_number',
        //                                                             'referral_code'
        //                                                         ]);
        $users = DB::select('SELECT agent_report_grids.*,
                                users.mobile_no as master_mobile, 
                                users.percentage, 
                                topups.topup_amount, 
                                cashins.ci_amount, 
                                users.website_url 
                            FROM
                                agent_report_grids 
                            LEFT JOIN users ON users.id=agent_report_grids.user_id 
                            -- LEFT JOIN sub_agents ON agent_report_grids.user_id=sub_agents.user_id 
                            LEFT JOIN (select SUM(amount) AS topup_amount,user_id from topups GROUP BY topups.user_id HAVING SUM(amount)) topups ON users.id=topups.user_id 
                            -- LEFT JOIN (select SUM(amount) AS bet2_amount,user_id from bets2ds GROUP BY user_id HAVING SUM(amount)) bets2 ON users.id=bets2.user_id 
                            -- LEFT JOIN (select SUM(amount) AS bet4_amount,user_id from bets4ds GROUP BY user_id HAVING SUM(amount)) bets4 ON users.id=bets4.user_id 
                            -- LEFT JOIN (select SUM(amount) AS bet6_amount,user_id from bets6ds GROUP BY user_id HAVING SUM(amount)) bets6 ON users.id=bets6.user_id 
                            LEFT JOIN (select SUM(amount) AS ci_amount,user_id from cashins where status="success" GROUP BY user_id HAVING SUM(amount)) cashins ON users.id=cashins.user_id 


                            WHERE users.website_url IS NOT NULL AND agent_report_grids.ma_id="'.$request->id.'" ');
                            // WHERE users.website_url IS NOT NULL AND agent_report_grids.ma_id="'.$request->id.'" ');



        // $users = DB::select('select t.*, users.website_url 
        // from agent_report_grids t
        // inner join users on users.id=t.user_id where t.ma_id='.$request->id.' AND users.website_url IS NOT NULL');
        $total_sales = 0;
        $record = Topups::where('ma_id',$request->id)
                               ->where('sa_id',0)
                               ->where('status','success');
        $total_cashins = $record->sum('amount');

        return view(
            'admin.users.master-agents-downlines',[
                'users' =>  $users,
                'topup_model' => new Topups,
                'agentReportsObj' => new AgentReportGrids,
                'maObj' => MasterAgents::where('user_id',$request->id)->first(),
                'total_sales' => $total_sales,
                'total_cashins' => $total_cashins,
                'label' => 'Master Agents Downlines',
                'agent_type' => 'master_agent',
                'super_admin' => self::SUPER_ADMIN,
            ]
        );

    }
    public function subAgentsInfo(Request $request){
        if(auth()->user()->user_level < self::OPERATIONS){
            
            return redirect('home');
        }
        if(!in_array(auth()->user()->id,$this->allowed)){
            
            return redirect('home');
        }
        
        // $users = AgentReportGrids::where('sa_id',$request->id);
        $users = DB::select('SELECT agent_report_grids.*,
                                users.mobile_no as master_mobile, 
                                users.percentage, 
                                -- sub_agents.total as sub_total_commission, 
                                topups.topup_amount, 
                                -- bets2.bet2_amount, 
                                bets3ds.b3_amount, 
                                bets2ds.b2_amount, 
                                bets4ds.b4_amount, 
                                bets6ds.b6_amount, 
                                -- bets4.bet4_amount, 
                                -- bets6.bet6_amount, 
                                cashins.ci_amount, 
                                users.website_url 
                            FROM
                                agent_report_grids 
                            LEFT JOIN users ON users.id=agent_report_grids.user_id 
                            LEFT JOIN (select SUM(amount) AS topup_amount,user_id from topups GROUP BY topups.user_id HAVING SUM(amount)) topups ON users.id=topups.user_id 
                            LEFT JOIN (select SUM(amount) AS b3_amount,user_id from bets3ds GROUP BY bets3ds.user_id HAVING SUM(amount)) bets3ds ON users.id=bets3ds.user_id 
                            LEFT JOIN (select SUM(amount) AS b2_amount,user_id from bets2ds GROUP BY bets2ds.user_id HAVING SUM(amount)) bets2ds ON users.id=bets2ds.user_id 
                            LEFT JOIN (select SUM(amount) AS b4_amount,user_id from bets4ds GROUP BY bets4ds.user_id HAVING SUM(amount)) bets4ds ON users.id=bets4ds.user_id 
                            LEFT JOIN (select SUM(amount) AS b6_amount,user_id from bets6ds GROUP BY bets6ds.user_id HAVING SUM(amount)) bets6ds ON users.id=bets6ds.user_id 
                            LEFT JOIN (select SUM(amount) AS ci_amount,user_id from cashins where status="success" GROUP BY user_id HAVING SUM(amount)) cashins ON users.id=cashins.user_id 


                            WHERE users.website_url IS NOT NULL AND agent_report_grids.sa_id="'.$request->id.'" ');



        $subAgentDetails = SubAgents::where('user_id',$request->id)->first();

        

        return view(
            'admin.users.sub-agents-downlines',[
                'users' => $users,
                'topup_model' => new Topups,
                'sa_details' => $subAgentDetails,
                'label' => 'Sub Agents Downlines',
                'agent_type' => 'master_agent',
                'super_admin' => self::SUPER_ADMIN,
            ]
        );

    }
    public function subAgents(){
        if(auth()->user()->user_level < self::OPERATIONS){
            
            return redirect('home');
        }
        if(!in_array(auth()->user()->id,$this->allowed)){
            
            return redirect('home');
        }
        $users = SubAgents::all();
        // $users = DB::select('SELECT agent_report_grids.*,
        //                         users.mobile_no as master_mobile, 
        //                         users.percentage, 
        //                         -- sub_agents.total as sub_total_commission, 
        //                         topups.topup_amount, 
        //                         -- bets2.bet2_amount, 
        //                         bets3ds.b3_amount, 
        //                         bets2ds.b2_amount, 
        //                         bets4ds.b4_amount, 
        //                         bets6ds.b6_amount, 
        //                         -- bets4.bet4_amount, 
        //                         -- bets6.bet6_amount, 
        //                         cashins.ci_amount, 
        //                         users.website_url 
        //                     FROM
        //                         agent_report_grids 
        //                     LEFT JOIN users ON users.id=agent_report_grids.user_id 
        //                     LEFT JOIN (select SUM(amount) AS topup_amount,user_id from topups GROUP BY topups.user_id HAVING SUM(amount)) topups ON users.id=topups.user_id 
        //                     LEFT JOIN (select SUM(amount) AS b3_amount,user_id from bets3ds GROUP BY bets3ds.user_id HAVING SUM(amount)) bets3ds ON users.id=bets3ds.user_id 
        //                     LEFT JOIN (select SUM(amount) AS b2_amount,user_id from bets2ds GROUP BY bets2ds.user_id HAVING SUM(amount)) bets2ds ON users.id=bets2ds.user_id 
        //                     LEFT JOIN (select SUM(amount) AS b4_amount,user_id from bets4ds GROUP BY bets4ds.user_id HAVING SUM(amount)) bets4ds ON users.id=bets4ds.user_id 
        //                     LEFT JOIN (select SUM(amount) AS b6_amount,user_id from bets6ds GROUP BY bets6ds.user_id HAVING SUM(amount)) bets6ds ON users.id=bets6ds.user_id 
        //                     LEFT JOIN (select SUM(amount) AS ci_amount,user_id from cashins where status="success" GROUP BY user_id HAVING SUM(amount)) cashins ON users.id=cashins.user_id 


        //                     WHERE users.website_url IS NOT NULL AND agent_report_grids.sa_id="'.$request->id.'" ');
        $total_sales = $users->count();

        return view(
            'admin.users.sub-agents',[
                'users' => $users,
                'topup_model' => new Topups,
                'agentReportsObj' => new AgentReportGrids,
                'saObj' => new SubAgents,
                'maObj' => new MasterAgents,
                'userObj' => new User,
                'total_sales' => $total_sales,
                'label' => 'Sub Agents',
                'agent_type' => 'sub_agent',
                'super_admin' => self::SUPER_ADMIN,
            ]
        );

    }
}
