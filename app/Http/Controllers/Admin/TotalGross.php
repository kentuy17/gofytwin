<?php

namespace App\Http\Controllers\Admin;

use App\Models\Fight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TotalGross extends Controller
{
    const SUCCESS_STATUS = 'success';

    public $admin_accounts = [
        '15368',
        '39132',
        '85172',
        '85171',
        '68116',
        '52992',
        '68416',
        '68403',
        '62204',
        '68421',
        '68847',
        '15659',
        '356',
        '16546',
        '40556',
        '53018',
    ];

    
 
 
    public function total_gross(Request $request){
        if(auth()->user()->user_level < self::CSR){

            return redirect('home');
        }
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        // dd($from);
        $fights = Fight::whereBetween('created_at', [$from." 00:00:00", $from." 23:59:59"])->orderBy('id', 'DESC')->get();
        $total_sales = 0;

        return view(
            'admin.reports.gross',[
                'fights' => $fights,
                'total_sales' => $total_sales,
                'admin_accounts' => $this->admin_accounts,
                'type' => '3D',
                'from' => $from,
                'to' => $to,
                'title' => '3D',
            ]
        );
    }
}
