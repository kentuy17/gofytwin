<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Bet;
use App\Models\Topups;
use Illuminate\Http\Request;
use App\Resources\BetResource;
use App\Resources\UserResource;
use App\Http\Controllers\Controller;

class BetsController extends Controller
{
    const SUPER_ADMIN = 5000;
    const ACCOUNTING = 2000;
    const OPERATIONS = 1000;
    const CSR = 50;
    const BETTORS = 0;
  
    
   
    public function bets(Request $request){
        if(auth()->user()->user_level < self::ACCOUNTING){
            
            return redirect('home');
        }
        $q = $request->q;
        $users = Bet::query()
        ->where(function($query) use ($q) {
                $query->where('id', 'LIKE', '%'.$q.'%')
                    ->orWhere('fight_id', 'LIKE', '%'.$q.'%')
                    ->orWhere('fight_no', 'LIKE', '%'.$q.'%')
                    ->orWhere('user_id', 'LIKE', '%'.$q.'%')
                    ->orWhere('side', 'LIKE', '%'.$q.'%')
                    ->orWhere('status', 'LIKE', '%'.$q.'%');
            })->orderBy('id','desc')->paginate($request->per_page ?? 50);
        $userss = BetResource::collection($users);

        return view(
            'admin.bets.index',[
                'title' => 'Bets',
                'users' => $userss,
                'q' => $q,
                'topup_model' => new Topups,
                'super_admin' => self::SUPER_ADMIN,
            ]
        );
    }
}
