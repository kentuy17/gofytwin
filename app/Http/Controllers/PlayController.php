<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Buka;
use App\Models\User;
use App\Jobs\TextUsers;
use App\Models\Bets2ds;
use App\Models\Bets3ds;
use App\Models\Bets4ds;
use App\Models\Bets6ds;
use App\Models\Cashins;
use App\Models\Credits;
use App\Jobs\AddCredits;
use App\Jobs\JaiWinners;
use App\Models\PotMoney;
use App\Models\Receipts;
use App\Jobs\CartCleaner;
use App\Jobs\RemoveUsers;
use App\Models\TextBlast;
use App\Jobs\BetsEmail2pm;
use App\Jobs\CleanCredits;
use App\Jobs\GetNoCiUsers;
use App\Models\Winners2ds;
use App\Models\Winners3ds;
use App\Models\Winners4ds;
use App\Models\JaiSchedule;
use App\Jobs\BetsEmail3d3pm;
use App\Models\CheckBalance;
use App\Models\TimeSchedule;
use Illuminate\Http\Request;
use App\Models\SettingsModel;
use App\Jobs\ProcessWinners6d;
use App\Models\WinningCombination;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SaveToBettorsGridsTable;
use App\Models\DailyActiveBettorGrid;
use App\Models\LuckypayPaymentGateway;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SaveCashinsToCashinGridsTable;
use App\Http\Controllers\API\BaseController as BaseController;

class PlayController extends BaseController
{
    protected $settings;
    protected $background_image = "images/site-bg.png";
    protected $rumble= "RUMBLE";
    protected $straight = "STRAIGHT";
    protected $winners = [];
 

    public function __construct(
        SettingsModel $settings
    ){
        $this->settings = $settings;
        $this->background_image = $this->settings->getValueFor('background_image');
    }
     
     
    
    public function superTestTest(Request $request){
        // $mobile = User::find(83940);
        // if($mobile){
        //     dd('found');
        // } else {
        //     dd('not');
        // }
        exit;
        $jai_minutes_drawtime  = $settings->getValueFor('jai_minutes_drawtime');

        $now = Carbon::now();
        $nowHour = $now->format('H');
        $now->hour($nowHour);
        $now->minute($jai_minutes_drawtime);
        

        $time = Carbon::now();
        $draw_times = JaiSchedule::all('schedule');
        $all_sched = [];
        foreach($draw_times as $draw_time){
            $all_sched[] = $draw_time->schedule;
        }

        if ($time->format('h:iA') == $now->format('h:iA') && in_array(Carbon::now()->addHour()->format('hA'),$all_sched)) {
            JaiWinners::dispatch(Carbon::now()->addHour()->format('hA'),'jai');
        }
        dd('qqqqqqqqqqqqqqq');
        exit;


        $balance = new CheckBalance;
            $bal = $balance->getBalance($request); 
            dd($bal);
        exit;
        $twoDigitscutoff2PM  = $settings->getValueFor('twoD_2pm_draw_cutoff');
        $twoDigitscutoff5PM  = $settings->getValueFor('twoD_5pm_draw_cutoff');
        $twoDigitscutoff9PM  = $settings->getValueFor('twoD_9pm_draw_cutoff');
        
        $threeDigitscutoff2PM  = $settings->getValueFor('threeD_2pm_draw_cutoff');
        $threeDigitscutoff5PM  = $settings->getValueFor('threeD_5pm_draw_cutoff');
        $threeDigitscutoff9PM  = $settings->getValueFor('twoD_9pm_draw_cutoff');
        
        $fourDigitscutoff2PM  = $settings->getValueFor('fourD_2pm_draw_cutoff');
        $fourDigitscutoff5PM  = $settings->getValueFor('fourD_5pm_draw_cutoff');
        $fourDigitscutoff9PM  = $settings->getValueFor('fourD_9pm_draw_cutoff');
        dd($threeDigitscutoff9PM);
        $settings  = new SettingsModel();
        $cutoff2PM  = $settings->getValueFor('twoD_2pm_draw_cutoff');
        dd($cutoff2PM);
        exit;
        // dd($this->settings->getValueFor('twoD_2pm_draw_cutoff'));
        $cutoff = $this->settings->getValueFor('threeD_2pm_draw_cutoff');
        $now = Carbon::now();
        if($now->isAfter($cutoff)){
            echo "Expires!!";
        } else {
            echo "GOOD";
        }
        // e
    }
    public function justTest(){
        JaiWinners::dispatch(Carbon::now()->addHour()->format('hA'),'jai');
        exit;
        $winningCombinaiton = '471424';
        $draw_date = '2025-02-11';
        ProcessWinners6d::dispatch($winningCombinaiton,$draw_date);
        // SaveToBettorsGridsTable::dispatch();
        exit;
        $settings  = new SettingsModel();
        $jai_minutes_drawtime  = $settings->getValueFor('jai_minutes_drawtime');

        $now = Carbon::now();
        $nowHour = $now->format('H');
        $now->hour($nowHour);
        $now->minute($jai_minutes_drawtime);
        
        $drawTime = Carbon::now()->addHour();

        $time = Carbon::now();
        $draw_times = JaiSchedule::all('schedule');
        $all_sched = [];
        foreach($draw_times as $draw_time){
            $all_sched[] = $draw_time->schedule;
        }
        // 
        echo "base:".$time->format('h:iA')."<br>";
        echo "current:".$now->format('h:iA')."<br>";
        echo "formatted:".$drawTime->format('hA')."<br>";
        // dd($all_sched);
        // if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$this->drawTimes)) {
        if ($time->format('h:iA') == $now->format('h:iA') && in_array($drawTime->format('hA'),$all_sched)) {
            JaiWinners::dispatch(Carbon::now()->addHour()->format('hA'),'jai');
        }
        BetsEmail2pm::dispatch('2PM','bets3');
        dd($all_sched);
        dd(date("h:i A"));
        $res = [];
        while (count($res) < 3) {
            $min = 1;
            $max = 10;
            $d1 = rand($min,$max);
            
            if (!in_array($d1, $res)){
                array_push($res,$d1);
            }
        }
        dd($res[0]);
        exit;
        SaveCashinsToCashinGridsTable::dispatch();
        exit;
        $settings  = new SettingsModel();
        $jai_minutes_drawtime  = $settings->getValueFor('jai_minutes_drawtime');

        $now = Carbon::now();
        $nowHour = $now->format('H');
        $now->hour($nowHour);
        $now->minute($jai_minutes_drawtime);
        

        $time = Carbon::now();
        $draw_times = JaiSchedule::all('schedule');
        $all_sched = [];
        foreach($draw_times as $draw_time){
            $all_sched[] = $draw_time->schedule;
        }
        // dd(Carbon::now()->addHour()->format('hA'));

        // if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$this->drawTimes)) {
        if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$all_sched)) {
            JaiWinners::dispatch(Carbon::now()->addHour()->format('hA'),'jai');
        }
        exit;
        $sched = new TimeSchedule();
        $tae = $sched->getSchedules();
        dd($tae);

        exit;
        $jai_minutes_drawtime  = $this->settings->getValueFor('jai_minutes_drawtime');

        $now = Carbon::now();
        $nowHour = $now->format('H');
        $now->hour($nowHour);
        $now->minute($jai_minutes_drawtime);
        

        $time = Carbon::now();
        $draw_times = JaiSchedule::all('schedule');
        $all_sched = [];
        foreach($draw_times as $draw_time){
            $all_sched[] = $draw_time->schedule;
        }

        // if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$this->drawTimes)) {
        if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$all_sched)) {
            JaiWinners::dispatch($now->format('hA'),'jai');
        }
       

        $jai_minutes_drawtime  = $this->settings->getValueFor('jai_minutes_drawtime');

        $now = Carbon::now();
        $nowHour = $now->format('H');
        $now->hour($nowHour);
        $now->minute($jai_minutes_drawtime);
        

        $time = Carbon::now();
        $draw_times = JaiSchedule::all('schedule');
        $all_sched = [];
        foreach($draw_times as $draw_time){
            $all_sched[] = $draw_time->schedule;
        }

        // dd($now->format('hA'));

        $winComb = new WinningCombination;
        $result = $winComb->prediction($now->format('hA'))->content();

        exit;


        // if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$this->drawTimes)) {
        if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$all_sched)) {
            JaiWinners::dispatch($now->format('hA'),'jai');
        }
        echo "eixt";
        exit;
        // =================================
        $settings  = new SettingsModel();
        $jai_minutes_drawtime  = $settings->getValueFor('jai_minutes_drawtime');
        $now = Carbon::now();
        $nowHour = $now->format('H');
        $now->hour($nowHour)->addHour();
        $now->minute($jai_minutes_drawtime);
        

        $time = Carbon::now();
        $draw_times = JaiSchedule::all('schedule');
        $all_sched = [];
        dd($now->format('hA'));
        foreach($draw_times as $draw_time){
            $all_sched[] = $draw_time->schedule;
        }

        // if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$this->drawTimes)) {
        if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$all_sched)) {
            JaiWinners::dispatch($now->format('hA'),'jai');
        }

        exit;





        $winComb = new WinningCombination;
        $result = $winComb->prediction()->content();
        dd(json_decode($result,true));
        exit;

        # Create anchor time and another date time to be compared
        $anchorTime = Carbon::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:00:00"));
        $currentTime = Carbon::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        # count difference in minutes
        $timeDiff = $anchorTime->diff($currentTime);
        $minuteDiff = 60 - $timeDiff->i;
        $secondDiff = 60 - $timeDiff->s;
        if($minuteDiff < 5){
            dd('who timer');
        }
        dd($minuteDiff);
      
        exit;
        $now = Carbon::now();
        $settings  = new SettingsModel();
        $jai_minutes_drawtime  = $settings->getValueFor('jai_minutes_drawtime');
        $nowHour = $now->format('H');
        $now->hour($nowHour);
        $now->minute($jai_minutes_drawtime);
        dd($now->format('H'));
        

        $time = Carbon::now();
        $draw_times = JaiSchedule::all('schedule');
        $all_sched = [];
        foreach($draw_times as $draw_time){
            $all_sched[] = $draw_time->schedule;
        }

        // dd(in_array($time->format('hA'),$this->drawTimes));
        // if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$this->drawTimes)) {
        if ($time->format('h:iA') == $now->format('h:iA') && in_array($time->format('hA'),$all_sched)) {
            JaiWinners::dispatch($now->format('HA'),'jai');
            echo "yes";
        } else {
            echo "not time yet";
        }

        exit;





        $winComb = new WinningCombination;
        $result = $winComb->prediction()->content();

        dd(json_decode($result,true));

    }
    public function testingationHits(){
        $pg1 = 100;
        $pg3 = 25;
        $hits = 50;
        $cow = $pg1 + $pg3;
        $callback_amount = 25; // ampunt nga gi tapada ni adz

        $deducted_cow = $pg1 + $pg3 - $cow;
        $agent_commision = 50;
        $commision_withdrawal = 25;
        $actual_commision = $agent_commision - $commision_withdrawal;
        $alp = $cow - $actual_commision - $hits + $callback_amount; // @todo, add 




        echo "PG1 = ".$pg1."<br>";
        echo "PG2 = ".$pg3."<br>";
        echo "COW = ".$cow."<br>";
        echo "Actual Commision = ".$actual_commision."<br>";
        echo "Actual Hits = ".$hits."<br>";
        echo "ALP = ".$alp."<br>";
        exit;

    }
    public function testingation2(){
        $pg1 = 100;
        $pg3 = 75;
        $cow = $pg1 + $pg3;
        $agent_commision = 50;
        $commision_withdrawal = 25;
        $actual_commision = $agent_commision - $commision_withdrawal;
        $alp = $cow - $actual_commision;

        echo "PG1 = ".$pg1."<br>";
        echo "PG2 = ".$pg3."<br>";
        echo "COW = ".$cow."<br>";
        echo "Actual Commision = ".$actual_commision."<br>";
        echo "ALP = ".$alp."<br>";

        // if(date('H:i') == '13:06'){
        //     dd('iyot gerri mae dio');
        // }
        exit;
        dd(date('H:i'));
        BetsEmail3d3pm::dispatch();
        exit;
        // Set the content-type
        //url("images/imageslogo-1.png")
        header('Content-Type: image/png');
        // predifined Variables
        $font_size = 20;
        $angle = 0;
        // The text to draw
        $text = 'Small Town Luck';
        // Replace path by your own font path
        $font = 'arial.ttf';

        // Create the image
        $image = imagecreatetruecolor(640, 610);

        // Get bounderies
        $width = imagesx($image);
        $height = imagesy($image);
        // Get center coordinates of image
        $centerX = $width / 2;
        $centerY = $height * .05;
        // Get size of text
        list($left, $bottom, $right, , , $top) = imageftbbox($font_size, $angle, $font, $text);
        
        // Determine offset of text
        $left_offset = ($right - $left) / 2;
        $top_offset = ($bottom - $top) / 2;
        // dd($top_offset);
        // Generate coordinates
        $x = $centerX - $left_offset;
        $y = $centerY + $top_offset;

        // $black = imagecolorallocate($image, 225, 150, 123);

        // imagettftext($image, $font_size, $angle, $x, $y, $black, $font, $text);
        // imagettftext($image, $font_size, $angle, $x, $y+(25), $black, $font, $text);
        // imagettftext($image, $font_size, $angle, $x, $y+(50), $black, $font, $text);

        // Set the background color of image 
        $background_color = imagecolorallocate($image,  255, 255, 255); 
        $black = imagecolorallocate($image, 0, 0, 0);
            
        // Fill background with above selected color 
        imagefill($image, 0, 0, $background_color); 
        

        // Add the text
 
        /**
         * $image
         * $size
         * $angle
         * $x
         * $y    
         * $color    
         * $font_filename    
         * $text    
         * $options    
        **/
        imagettftext($image, $font_size, $angle, $x, $y, $black, $font, $text);
        imagettftext($image, 10, $angle, $x+10, $y+(20), $black, $font, 'Philippine Online Sweepstakes');
        imagettftext($image, $font_size, $angle, $x, $y+(50), $black, $font, 'Official Receipt');
        imagettftext($image, 10, $angle, $x + 50, $y+(65), $black, $font, 'Official Receipt');

        imagejpeg($image);
        imagedestroy($image);
        exit;

        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 399, 29, $black);

        // The text to draw
        $text = 'Testing';
        // Replace path by your own font path
        $font = 'verdana.ttf';

        // Add the text

        $bbox = imagettfbbox(20, 0, $font, $text);

        $x = $bbox[1] + (imagesx($im) / 2) - ($bbox[4]);
        $y = $bbox[3] + (imagesy($im) / 2) - ($bbox[5]);

        // imagerectangle($im, 0, 0, $x, $y, $white);
        imagettftext($im, 20, 0, 0, 20, $white, $font, $text);

        // Using imagepng() results in clearer text compared with imagejpeg()
        imagejpeg($im);
        imagedestroy($im);
        exit;


        /***
         * 
         * imagecolorallocate
         */
        $cashins = DB::table('cashins')->where('status','success')->select('user_id')->distinct()->get();
    }
    public function cashinButDidNotWon(){
        // $cashins = Cashins::where('status','success')->select('id','user_id')->distinct();
        //2023-12-26 05:54:44
        $from = '2023-12-26 00:00:00';
        $to = '2024-2-10 23:59:59';
        $cashins = DB::table('cashins')->where('status','success')
        ->whereBetween('created_at', [$from, $to])->select('user_id')->distinct()->get();

        // $cashins->chunk(10, function (Collection $bets) {
            foreach ($cashins as $bet) {
                // echo $bet->user_id."<br>";
                $totaller = Cashins::where('status','success')->where('user_id',$bet->user_id)->sum('amount');
                $is_won = 0;
                #check 2d winner
                $two = Winners2ds::where('user_id',$bet->user_id);
                if($two->count() > 0){
                    $is_won++;
                }
                
                #check 3d winner
                $three = Winners3ds::where('user_id',$bet->user_id);
                if($three->count() > 0){
                    $is_won++;
                }
                #check 4d winner
                $four = Winners4ds::where('user_id',$bet->user_id);
                if($four->count() > 0){
                    $is_won++;
                }
                if($is_won == 0){
                    $amount = 0;
                    $additional_credits = 0;
                    if($totaller >= 10 && $totaller <= 3999){
                        $additional_credits = 20;
                    } elseif($totaller >= 4000 && $totaller <= 7999){
                        $additional_credits = 50;
                     }elseif($totaller >= 8000){
                        $additional_credits = 100;
                    }
                    $current_credit = Credits::where('user_id',$bet->user_id)->first();
                    $old_credit = $current_credit->amount;
                    if($current_credit){
                        $amount = $current_credit->amount + $additional_credits;
                        $current_credit->amount = $amount;
                        $current_credit->save();
                    }
                    

                    
                    echo 'User ID: '.$bet->user_id.' | Mobile: '.User::find($bet->user_id)->mobile_no.' Total CI: '.$totaller.' To add: '.$additional_credits.
                         ' Old Credit: '.$old_credit.' New credit: '.$amount."<br>";//." = ".$totaller."<br>";
                } 
            }
        // });
    }
    public function dls(){
        $url = "http://www.google.co.in/intl/en_com/images/srpr/logo1w.png";
        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        Storage::put($name, $contents); 
    }
    public function deleteUsers(){
        RemoveUsers::dispatch(); 
        
        return json_encode(array('success' => false, "msg"=>"Robot!!!"));
    }

    public function checkBalance(){
        $all_users = User::all()->take(10);
        foreach($all_users as $all_user){
            echo $all_user->id.'<br>';
        }
    }
    public function add20Credits(){
        $tae = AddCredits::dispatch();

        return 'Processed complete';
        exit;
    }
    public function cleanCredits(){
        $tae = CleanCredits::dispatch();

        return 'Processed complete';
        exit;
    }
    public function welcome(){
        // Session::flush();
        Session::forget('two_play');
        Session::forget('three_play');
        Session::forget('four_play');
        Session::forget('claim_prize');

        if(!Auth::check()){
            return redirect(route('otp.login'));
        }

        if($this->checkMaintenance()){

            return $this->checkMaintenance();
        }

        $balance = User::find(Auth::user()->id); 
        $balance = $balance->credits->amount;
        $pot_money = PotMoney::first();
        return view(
            'welcome',[
                'landing_page' => true,
                'pot_money' => $pot_money->amount,
                'balance' => $balance,
                'user_id' => Auth::user()->id,
                'settings' => $this->settings,
                'home_banner' => null !== ($this->settings->getValueFor('banner_image')) ? $this->settings->getValueFor('banner_image') : 'images/homepage-banner.png',
                'background_image' => $this->background_image
            ]
        );
    }
    
    public function newTaste(Request $request){
        //set current timestamp
        $today = time();
        //calculate the number of days since Sunday
        $dow = date('w', $today);
        // echo $dow;exit;

        #this is for if the starting day is Sunday
        // $offset = $dow - 1;

        //but we start at suday so we do not need "-1"
        $offset = $dow;
        if ($offset < 0) {
            $offset = 6;
        }
        //calculate timestamp for Suday and Saturday
        $monday = $today - ($offset * 86400);
        $tuesday = $monday + (1 * 86400);
        $wednesday = $monday + (2 * 86400);
        $thursday = $monday + (3 * 86400);
        $friday = $monday + (4 * 86400);
        $saturday = $monday + (5 * 86400);
        $sunday = $monday + (6 * 86400);
        //print dates for Suday and Saturday in the current week
        print date("Y-m-d", $monday) . "= Suday<br>";
        print date("Y-m-d", $tuesday) . "= Monday<br>";
        print date("Y-m-d", $wednesday) . "= Tuesday<br>";
        print date("Y-m-d", $thursday) . "= Wednesday<br>";
        print date("Y-m-d", $friday) . "= Thursday<br>";
        print date("Y-m-d", $saturday) . "= Friday<br>";
        print date("Y-m-d", $sunday) . "= Saturday<br>";
    }
    public function taste3(Request $request){
        $mobile = '09659086255';
        $out = '63'.substr($mobile, 1);
        dd($out);
        exit;
        $azar = new Buka;
        // $res = $azar->sendSingleMessage('09659086255','My Love Gerri Mae Dio');
        $res = $azar->sendRequest();
        // $responseData = json_decode($res->body(), true);
        // dd($responseData);
        // $pg = new FspgPaymentGateway();
        $pg = new LuckypayPaymentGateway();
        $pg->processTest();

    }
    public function taste(Request $request){
        GetNoCiUsers::dispatch();
        return 'Finished';
        exit;
        $total_bets_two = Bets6ds::where('user_id', 2)->sum('amount');
        dd($total_bets_two);

        $cntr = 1;
        $from = isset($request->from) ? $request->from : date('Y-m-d');
        $to = isset($request->to) ? $request->to : date('Y-m-d');
        $cashins = Cashins::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->where('status','success')->get();
        
        foreach($cashins as $cashin){
            if($cntr < 207 && $cntr != 128){
                $current = Credits::where('user_id',$cashin->user_id)->first();
                $current_credits = $current->amount;
                $ci = $current_credits + $cashin->amount;
                $current->amount = $ci;
                $current->save();
            }
            $cntr++;
        }


        exit;
        echo substr(md5(rand()), 0, 10); exit;
        $cutoff = $this->settings->getValueFor('threeD_2pm_draw_cutoff');
        $now = Carbon::now();
        $sample_data = Bets3ds::find(142525);
        $draw_time = new Carbon($sample_data->draw_time);
        if($now->isAfter($cutoff)){
            echo "Expires!!";
        } else {
            echo "GOOD";
        }
        // echo $draw_time->format('H:i:s');
        exit;
        dd(Carbon::now());
    }
    public function happyHolidaysText(){
        TextUsers::dispatch(); 
        
        return json_encode(array('success' => false, "msg"=>"Robot!!!"));
    }
    public function reference($play,$reference){
        switch ($play) {
            case 2:
                $bet = Bets2ds::where('ref_no',$reference);
                $comb = '';
                foreach ($bet->get() as $betRecord):
                    $comb.=$betRecord->d1.'-'.$betRecord->d2.' ('.strtoupper($betRecord->combination_type[0]).') = P'.$betRecord->amount.', ';
                endforeach;
                $total = $bet->sum('amount');
                $singleBet = $bet->first();
                $date=date_create($singleBet->draw_date);
                $dateFormatted =  date_format($date,"M d, Y");
                return view(
                        'plays.2d.download',[
                            'contact_no' =>  $singleBet->contact_no,
                            'draw_date' =>  $dateFormatted,
                            'draw_time' =>  $singleBet->draw_time,
                            'play' =>  'PLAY 2D',
                            'ref_no' =>  $reference,
                            'total' =>  number_format($total, 2, '.', ''),
                            'bets' =>  $comb
                        ]
                    );
              break;
            case 3:
                $bet = Bets3ds::where('ref_no',$reference);
                $comb = '';
                // foreach ($bet->get() as $betRecord):
                //     $comb.=$betRecord->d1.'-'.$betRecord->d2.'-'.$betRecord->d3.' ('.strtoupper($betRecord->combination_type[0]).') = P'.$betRecord->amount.', ';
                // endforeach;
                $total = $bet->sum('amount');
                $singleBet = $bet->first();
                $date=date_create($singleBet->draw_date);

                $dateFormatted =  date_format($date,"M d, Y H:i:s");
                $receipt = Receipts::where('ref_no',$reference)->first();
                if($receipt->is_notified == ''){
                    $textblast = new TextBlast;
                    $textblast->semaphore($singleBet->contact_no, number_format($receipt->amount, 2, '.', ''), $reference, 'PLAY 3D', $singleBet->draw_time, $dateFormatted);
                    $receipt->is_notified = true;
                    $receipt->save();
                }
                $comb = json_decode($receipt->data,true);
                // dd($comb);
                return view(
                        'plays.3d.download',[
                            'contact_no' =>  $singleBet->contact_no,
                            'draw_date' =>  $dateFormatted,
                            'draw_time' =>  $singleBet->draw_time,
                            'play' =>  'PLAY 3D',
                            'ref_no' =>  $reference,
                            'total' =>  number_format($receipt->amount, 2, '.', ''),
                            'bets' =>  $comb,
                            'background_image' => $this->background_image
                        ]
                    );
              break;
            case 4:
                $bet = Bets4ds::where('ref_no',$reference);
                $comb = '';
                foreach ($bet->get() as $betRecord):
                    $comb.=$betRecord->d1.'-'.$betRecord->d2.'-'.$betRecord->d3.'-'.$betRecord->d4.' ('.strtoupper($betRecord->combination_type[0]).') = P'.$betRecord->amount.', ';
                endforeach;
                $total = $bet->sum('amount');
                $singleBet = $bet->first();
                $bets = $bet->get();
                $date=date_create($singleBet->draw_date);
                    $dateFormatted =  date_format($date,"M d, Y");
                return view(
                        'plays.4d.download',[
                            'contact_no' =>  $singleBet->contact_no,
                            'draw_date' =>  $dateFormatted,
                            'draw_time' =>  $singleBet->draw_time,
                            'play' =>  'PLAY 4D',
                            'ref_no' =>  $reference,
                            'total' =>  number_format($total, 2, '.', ''),
                            'bets' =>  $comb
                        ]
                    );
              break;
            default:
        }
    }
    public function permutation4d($combination){
        $chars = str_split($combination); // 2121
        sort($chars);

        $result = [];

        foreach ($chars as $char1) {
        $chars2 = $chars;
        unset($chars2[array_search($char1, $chars, true)]);
        foreach ($chars2 as $char2) {
            $chars3 = $chars2;
            unset($chars3[array_search($char2, $chars2, true)]);
            foreach ($chars3 as $char3) {
            $chars4 = $chars3;
            unset($chars4[array_search($char3, $chars3, true)]);
            foreach ($chars4 as $char4) {
                $result[] = $char1 . $char2 . $char3 . $char4;
            }
            }
        }
        }

        return array_values(array_unique($result));
    }
    function pc_permute($items, $perms = array( )) {
        if (empty($items)) {
            $return = array($perms);
        }  else {
            $return = array();
            for ($i = count($items) - 1; $i >= 0; --$i) {
                 $newitems = $items;
                 $newperms = $perms;
             list($foo) = array_splice($newitems, $i, 1);
                 array_unshift($newperms, $foo);
                 $teeee = $this->pc_permute($newitems, $newperms);
                 dd($teeee);
                 $return = array_merge($return, $this->pc_permute($newitems, $newperms));
             }
        }
        return $return;
    }
    public function permutation6d($combination){
        $chars = str_split($combination); // 2121
        sort($chars);

        $result = [];

        foreach ($chars as $char1) {
            $chars2 = $chars;
            unset($chars2[array_search($char1, $chars, true)]);
            foreach ($chars2 as $char2) {
                $chars3 = $chars2;
                unset($chars3[array_search($char2, $chars2, true)]);
                foreach ($chars3 as $char3) {
                    $chars4 = $chars3;
                    unset($chars4[array_search($char3, $chars3, true)]);
                    foreach ($chars4 as $char4) {
                        $chars5 = $chars4;
                        unset($chars5[array_search($char4, $chars4, true)]);
                        foreach ($chars5 as $char5) {
                            $chars6 = $chars5;
                            unset($chars6[array_search($char5, $chars5, true)]);
                            foreach ($chars6 as $char6) {
                                $result[] = $char1 . $char2 . $char3 . $char4 . $char5 . $char6;
                            }
                        }
                    }
                }
            }
        }

        return array_values(array_unique($result));
    }
    public function test4(){
        dd($this->permutation6d('123456')); 
       exit;
        $comb = '123456';
         // echo "Amount is: ".$bet_cost[count($result)].'<br>';
         $tae = $this->permutation4d($comb);
        //  echo "Amount to pay is: P".$bet_cost[count($tae)].'<br>';
         echo count($tae);
         dd($tae);exit;
    }
    public function test3(){
        $data = '1775940313-6-3';
        $exp = explode("-", $data);
            switch ($exp[2]) {
                case "2":
                    $winner = Winners2ds::where('merchant_order_no',$data)->first();
                    break;
                case "3":
                    $winner = Winners3ds::where('merchant_order_no',$data)->first();
                    break;
                case "4":
                    $winner = Winners4ds::where('merchant_order_no',$data)->first();
                    break;
                default:
                    
                }
                if($winner){
                    echo "it works";
                } else {
                    echo "failes";
                }exit;
            $winner->process_status = 'success';
            $winner->save();
        exit;
        $tae = [];
        $winning_nos = '1234';
        $winning_nos = str_split($winning_nos);
        Bets4ds::where('draw_time', '9PM')
                    ->where('draw_date', date('Y-m-d'))
                    ->chunkById(100, function (Collection $bets) use ($winning_nos, $tae) {
                        foreach ($bets as $bet) {
                            // calculate amount
                            if(
                                $bet->d1 == $winning_nos[0] && 
                                $bet->d2 == $winning_nos[1] && 
                                $bet->d3 == $winning_nos[2] && 
                                $bet->d4 == $winning_nos[3] && 
                                $bet->amount != 0 && 
                                $bet->combination_type == $this->straight
                            ){
                                // straight
                                $winningAmount = $this->getTthreeDigitsWinningAmount($bet->amount);
                                Winners4ds::create([
                                    'bet_id' => $bet->id,
                                    'winning_combination' => $bet->d1.$bet->d2.$bet->d3,
                                    'winning_amount' => $winningAmount,
                                    'bet_amount' => $bet->amount,
                                    'original_bet_amount' => $bet->original_bet_amount,
                                    'reference_no' => $bet->ref_no,
                                    'contact_no' => $bet->contact_no,
                                    'receipt' => $bet->receipt,
                                    'claimed' => $bet->claimed,
                                    'draw_time' => $bet->draw_time,
                                    'draw_date' => $bet->draw_date,
                                    'upload' => $bet->upload,
                                    'combination_type' => $bet->combination_type,
                                    'approved' => $bet->approved,
                                ]);
                                $textblast = new TextBlast;
                                $textblast->semaphoreWinners(
                                                    $bet->contact_no, //contact no.
                                                    number_format($winningAmount, 2, '.',','),  //winning amount
                                                    $bet->ref_no, //ref no.
                                                    "Play 3D", // play 
                                                    $bet->draw_time,
                                                    date('M d, Y h:iA'),
                                                    $bet->d1.$bet->d2.$bet->d3
                                            );
                            } else{
                                if(
                                    in_array($bet->d1, $winning_nos) && 
                                    in_array($bet->d2, $winning_nos) && 
                                    in_array($bet->d3, $winning_nos) && 
                                    in_array($bet->d4, $winning_nos) && 
                                    $bet->combination_type == $this->rumble && 
                                    $bet->d1 == $winning_nos[0] && 
                                    $bet->d2 == $winning_nos[1] && 
                                    $bet->amount != 0 && 
                                    $bet->d3 == $winning_nos[2] && 
                                    $bet->d4 == $winning_nos[3] 
                                ){
                                    // rumble
                                    // $arrs = ['2','2','1'];
                                    // echo count(array_unique($arrs));
                                    // exit;
                                    $digitsRumble = $this->getThreeDigitsRumble($bet->d1.$bet->d2.$bet->d3);
                                    if($digitsRumble == 3){
                                        // 3 digits
                                        $winningAmount = $this->getThreeDigitsRumbleThreeWinningAmount($bet->amount);
                                    } else {
                                        
                                        $winningAmount = $this->getThreeDigitsRumbleTwoWinningAmount($bet->amount);
                                    }
                                    Winners4ds::create([
                                        'bet_id' => $bet->id,
                                        'winning_combination' => $bet->d1.$bet->d2.$bet->d3,
                                        'winning_amount' => $winningAmount,
                                        'bet_amount' => $bet->amount,
                                        'original_bet_amount' => $bet->original_bet_amount,
                                        'reference_no' => $bet->ref_no,
                                        'contact_no' => $bet->contact_no,
                                        'receipt' => $bet->receipt,
                                        'combination_type' => $bet->combination_type,
                                        'claimed' => $bet->claimed,
                                        'draw_date' => $bet->draw_date,
                                        'draw_time' => $bet->draw_time,
                                        'upload' => $bet->upload,
                                        'approved' => $bet->approved,
                                    ]);
                                }
                            }
                        }
                    });












        exit;
        $bet_cost = [
            '24'=>240,
            '12'=>120,
            '6'=>60,
            '4'=>40,
            ];
        $comb = '1233';
        


        // echo "Amount is: ".$bet_cost[count($result)].'<br>';
        $tae = $this->permutation4d($comb);
        echo "Amount to pay is: P".$bet_cost[count($tae)].'<br>';
        echo count($tae);
        dd($tae);exit;


        $winning_nos = '5679';


        dd($this->permutations4($winning_nos));

        $already_won = [];
        $jackpot_counter = 1;

        // jackpot
        $jackpot_winners = Bets4ds::where('d1', $winning_nos[0])
                                    ->where('d2', $winning_nos[1])
                                    ->where('d3', $winning_nos[2])
                                    ->where('d4', $winning_nos[3])
                                    ->where('draw_time', '2PM')
                                    ->where('draw_date', date('Y-m-d'));
        $jackpot_no_of_winners = $jackpot_winners->count();
        if($jackpot_no_of_winners > 0){
            $jackpot_win_amount = $this->get4dJackpot() / $jackpot_no_of_winners;

            foreach($jackpot_winners->get() as $jackpot_winner){
                if($jackpot_no_of_winners == 1){
                    $jackpot_win_cnt = 'solo';
                } else {
                    $jackpot_win_cnt = $jackpot_counter.'/'.$jackpot_no_of_winners;
                }
                $already_won[]=$jackpot_winner->contact_no;
                Winners4ds::create([
                    'bet_id' => $jackpot_winner->id,
                    'winning_combination' => $jackpot_winner->d1.$jackpot_winner->d2.$jackpot_winner->d3.$jackpot_winner->d4,
                    'winning_amount' => $jackpot_win_amount,
                    'bet_amount' => $jackpot_winner->amount,
                    'reference_no' => $jackpot_winner->ref_no,
                    'contact_no' => $jackpot_winner->contact_no,
                    'receipt' => $jackpot_winner->receipt,
                    'claimed' => $jackpot_winner->claimed,
                    'draw_time' => $jackpot_winner->draw_time,
                    'draw_date' => $jackpot_winner->draw_date,
                    'upload' => $jackpot_winner->upload,
                    'combination_type' => $jackpot_winner->combination_type,
                    'approved' => $jackpot_winner->approved,
                ]);
                $textblast = new TextBlast;
                $textblast->semaphore4dJackpotWinners(
                                    $jackpot_winner->contact_no, //contact no.
                                    number_format($jackpot_win_amount, 2, '.',','),  //winning amount
                                    $jackpot_winner->ref_no, //ref no.
                                    "Play 3D", // play 
                                    $jackpot_winner->draw_time,
                                    date('M d, Y h:iA'),
                                    $jackpot_winner->d1.$jackpot_winner->d2.$jackpot_winner->d3.$jackpot_winner->d4,
                                    $jackpot_win_cnt
                                );
                $jackpot_counter++;
            }
        }

        //last three
        $last_three_counter = 1;
        $last_three_winners = Bets4ds::where('d2', $winning_nos[1])
                                    ->where('d3', $winning_nos[2])
                                    ->where('d4', $winning_nos[3])
                                    ->where('draw_time', '2PM')
                                    ->where('draw_date', date('Y-m-d'));
        $jackpot_no_of_winners = $last_three_winners->count();
        
        $jackpot_win_amount = $this->get4dLastThree() / $jackpot_no_of_winners;

        foreach($last_three_winners->get() as $jackpot_winner){
            if($jackpot_no_of_winners == 1){
                $jackpot_win_cnt = 'solo';
            } else {
                $jackpot_win_cnt = $last_three_counter.'/'.$jackpot_no_of_winners;
            }
            if(!in_array($jackpot_winner->contact_no,$already_won)){
                $already_won[]=$jackpot_winner->contact_no;
                Winners4ds::create([
                    'bet_id' => $jackpot_winner->id,
                    'winning_combination' => $jackpot_winner->d2.$jackpot_winner->d3.$jackpot_winner->d4,
                    'winning_amount' => $jackpot_win_amount,
                    'bet_amount' => $jackpot_winner->amount,
                    'reference_no' => $jackpot_winner->ref_no,
                    'contact_no' => $jackpot_winner->contact_no,
                    'receipt' => $jackpot_winner->receipt,
                    'claimed' => $jackpot_winner->claimed,
                    'draw_time' => $jackpot_winner->draw_time,
                    'draw_date' => $jackpot_winner->draw_date,
                    'upload' => $jackpot_winner->upload,
                    'combination_type' => $jackpot_winner->combination_type,
                    'approved' => $jackpot_winner->approved,
                ]);
                $textblast = new TextBlast;
                $textblast->semaphoreLastthreeWinners(
                                    $jackpot_winner->contact_no, //contact no.
                                    number_format($jackpot_win_amount, 2, '.',','),  //winning amount
                                    $jackpot_winner->ref_no, //ref no.
                                    "Play 3D", // play 
                                    $jackpot_winner->draw_time,
                                    date('M d, Y h:iA'),
                                    $jackpot_winner->d1.$jackpot_winner->d2.$jackpot_winner->d3.$jackpot_winner->d4,
                                    $jackpot_win_cnt
                                );
            }
            $last_three_counter++;
        }

        //last two
        $last_two_counter = 1;
        $last_two_winners = Bets4ds::where('d3', $winning_nos[2])
                                    ->where('d4', $winning_nos[3])
                                    ->where('draw_time', '2PM')
                                    ->where('draw_date', date('Y-m-d'));
        $jackpot_no_of_winners = $last_two_winners->count();
        $jackpot_win_amount = $this->get4dLastTwo() / $jackpot_no_of_winners;

        foreach($last_two_winners->get() as $jackpot_winner){
            if($jackpot_no_of_winners == 1){
                $jackpot_win_cnt = 'solo';
            } else {
                $jackpot_win_cnt = $last_two_counter.'/'.$jackpot_no_of_winners;
            }
            if(!in_array($jackpot_winner->contact_no,$already_won)){
                $already_won[]=$jackpot_winner->contact_no;
                Winners4ds::create([
                    'bet_id' => $jackpot_winner->id,
                    'winning_combination' => $jackpot_winner->d3.$jackpot_winner->d4,
                    'winning_amount' => $jackpot_win_amount,
                    'bet_amount' => $jackpot_winner->amount,
                    'reference_no' => $jackpot_winner->ref_no,
                    'contact_no' => $jackpot_winner->contact_no,
                    'receipt' => $jackpot_winner->receipt,
                    'claimed' => $jackpot_winner->claimed,
                    'draw_time' => $jackpot_winner->draw_time,
                    'draw_date' => $jackpot_winner->draw_date,
                    'upload' => $jackpot_winner->upload,
                    'combination_type' => $jackpot_winner->combination_type,
                    'approved' => $jackpot_winner->approved,
                ]);
                $textblast = new TextBlast;
                $textblast->semaphoreLastwoWinners(
                                    $jackpot_winner->contact_no, //contact no.
                                    number_format($jackpot_win_amount, 2, '.',','),  //winning amount
                                    $jackpot_winner->ref_no, //ref no.
                                    "Play 3D", // play 
                                    $jackpot_winner->draw_time,
                                    date('M d, Y h:iA'),
                                    $jackpot_winner->d3.$jackpot_winner->d4,
                                    $jackpot_win_cnt
                                );
            }
            $last_two_counter++;
        }

        exit;


        $tae = CartCleaner::dispatch();
        exit;
        echo date('M d, Y h:iA'); exit;
        dd(route('playstl'));
        $ch = curl_init();
        $parameters = array(
            'apikey' => 'c3bd3c9de7c0aad3ba2e094b484f6745', //Your API KEY
            'number' => '09659086255',
            'message' => 'Mga Gagmay mog Notch',
            'sendername' => 'STL'
        );
        curl_setopt( $ch, CURLOPT_URL,'https://api.semaphore.co/api/v4/messages' );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close ($ch);

        //Show the server response
        echo $output;
    }
    public function test(){
        dd(url('/'));
        $result_obj = Winners3ds::where('reference_no', '000025')
                                ->where('contact_no', '09650986255');
        if($result_obj->first()->claimed == 1) {

            echo "utin2x";
            // return redirect('/claim-prize/success');
        }else {
            Winners3ds::where('reference_no', '000025')
                                ->where('contact_no', '09650986255')->update([
                'claimed' => 1,
                'payout_remittance' => 'vhog vhlaht',
                'receiver_name' => 'ryn',
                'receiver_mobile_number' => '090909090909'
            ]);
  
            echo "bilat ni gerri mae dio baho maling tilaan";
        }        
 
        dd($result_obj->first()->payout_remittance);
        $mobile = '09263007530';
        $comb = '123';
        $order = 'STRAIGHT';
        $total = 0;
        dd(Session::get('three_play'));
        exit;
         
    }
    public function saveCiToTable(){
        $taes = DB::select('SELECT 
        cashins.user_id,
        users.mobile_no, 
        users.percentage, 
        users.account_status, 
           
        SUM(amount) AS total_ci
    FROM
        cashins 
        LEFT JOIN users ON users.id=cashins.user_id 
         WHERE status="success"
    GROUP BY user_id 
    HAVING SUM(amount) ORDER BY total_ci desc');
        foreach($taes as $tae){
            echo $tae->user_id.'='.$tae->total_ci; exit;
        }   

        // SaveUsersToCashinTable::dispatch();
        return "Finished";
        
    }
    public function test2(){
        $winning_nos = str_split('7890');
        // $winning_nos = str_split($this->win_no);
        $potMoney = PotMoney::first()->amount;
        $all_bet = Bets4ds::where('draw_time', '9PM')
                ->where('draw_date', date('Y-m-d'))
                ->where('combination_type', $this->straight)
                ->where('d1', $winning_nos[0])
                ->where('d2', $winning_nos[1])
                ->where('d3', $winning_nos[2])
                ->where('d4', $winning_nos[3]);
        $winning_amount = $potMoney / $all_bet->count();
        //jackpot
        $all_bet->chunkById(200, function (Collection $bets) use ($winning_amount) {
                    foreach ($bets as $bet) {
                        // straight
                        // $winningAmount = $this->getTthreeDigitsWinningAmount($bet->amount);
                        $this->winners[] = $bet->contact_no;
                        Winners4ds::create([
                            'bet_id' => $bet->id,
                            'winning_combination' => $bet->d1.$bet->d2.$bet->d3.$bet->d4,
                            'winning_amount' => $winning_amount,
                            'bet_amount' => $bet->amount,
                            'original_bet_amount' => $bet->original_bet_amount,
                            'reference_no' => $bet->ref_no,
                            'contact_no' => $bet->contact_no,
                            'receipt' => $bet->receipt,
                            'claimed' => $bet->claimed,
                            'draw_time' => $bet->draw_time,
                            'draw_date' => $bet->draw_date,
                            'upload' => $bet->upload,
                            'combination_type' => $bet->combination_type,
                            'approved' => $bet->approved,
                        ]);
                        $textblast = new TextBlast;
                        $textblast->semaphoreWinners(
                                            $bet->contact_no, //contact no.
                                            number_format($winning_amount, 2, '.',','),  //winning amount
                                            $bet->ref_no, //ref no.
                                            "Play 3D", // play 
                                            $bet->draw_time,
                                            date('M d, Y h:iA'),
                                            $bet->d1.$bet->d2.$bet->d3.$bet->d3
                                    );
                    }
                });


        //last three
        $all_bet = Bets4ds::where('draw_time', '9PM')
                        ->where('draw_date', date('Y-m-d'))
                        ->where('combination_type', $this->straight)
                        ->where('d2', $winning_nos[1])
                        ->where('d3', $winning_nos[2])
                        ->where('d4', $winning_nos[3]);
        $winning_amount = 1000;
        $all_bet->chunkById(200, function (Collection $bets) use ($winning_amount) {
                    foreach ($bets as $bet) {
                        if(!in_array($bet->contact_no, $this->winners)){
                            $this->winners[] = $bet->contact_no;
                            Winners4ds::create([
                                'bet_id' => $bet->id,
                                'winning_combination' => $bet->d1.$bet->d2.$bet->d3.$bet->d4,
                                'winning_amount' => $winning_amount,
                                'bet_amount' => $bet->amount,
                                'original_bet_amount' => $bet->original_bet_amount,
                                'reference_no' => $bet->ref_no,
                                'contact_no' => $bet->contact_no,
                                'receipt' => $bet->receipt,
                                'claimed' => $bet->claimed,
                                'draw_time' => $bet->draw_time,
                                'draw_date' => $bet->draw_date,
                                'upload' => $bet->upload,
                                'combination_type' => $bet->combination_type,
                                'approved' => $bet->approved,
                            ]);
                            $textblast = new TextBlast;
                            $textblast->semaphoreWinners(
                                                $bet->contact_no, //contact no.
                                                number_format($winning_amount, 2, '.',','),  //winning amount
                                                $bet->ref_no, //ref no.
                                                "Play 3D", // play 
                                                $bet->draw_time,
                                                date('M d, Y h:iA'),
                                                $bet->d1.$bet->d2.$bet->d3.$bet->d3
                                        );
                        }
                    }
                });


        //last two
        $all_bet = Bets4ds::where('draw_time', '9PM')
                        ->where('draw_date', date('Y-m-d'))
                        ->where('combination_type', $this->straight)
                        ->where('d3', $winning_nos[2])
                        ->where('d4', $winning_nos[3]);
        $winning_amount = 300;
        
        $all_bet->chunkById(200, function (Collection $bets) use ($winning_amount) {
                    foreach ($bets as $bet) {
                        if(!in_array($bet->contact_no,$this->winners)){
                            $this->winners[] = $bet->contact_no;
                            Winners4ds::create([
                                'bet_id' => $bet->id,
                                'winning_combination' => $bet->d1.$bet->d2.$bet->d3.$bet->d4,
                                'winning_amount' => $winning_amount,
                                'bet_amount' => $bet->amount,
                                'original_bet_amount' => $bet->original_bet_amount,
                                'reference_no' => $bet->ref_no,
                                'contact_no' => $bet->contact_no,
                                'receipt' => $bet->receipt,
                                'claimed' => $bet->claimed,
                                'draw_time' => $bet->draw_time,
                                'draw_date' => $bet->draw_date,
                                'upload' => $bet->upload,
                                'combination_type' => $bet->combination_type,
                                'approved' => $bet->approved,
                            ]);
                            $textblast = new TextBlast;
                            $textblast->semaphoreWinners(
                                                $bet->contact_no, //contact no.
                                                number_format($winning_amount, 2, '.',','),  //winning amount
                                                $bet->ref_no, //ref no.
                                                "Play 3D", // play 
                                                $bet->draw_time,
                                                date('M d, Y h:iA'),
                                                $bet->d1.$bet->d2.$bet->d3.$bet->d3
                                        );
                        }
                    }
                });

        
        
        
        
        
        
        
        exit;


        $request = new Request;
        $comb = '1444';
        $tae = $this->permutation4d($comb);
        // echo "Amount to pay is: P".$bet_cost[count($tae)].'<br>';
        echo count($tae);
        dd($tae);exit;
        exit;
    }
    public function permutations3($input) {
        $digit = str_split($input, 1);
        $output = [];
        for ($i=0; $i<3; $i++) {
          for ($j=0; $j<3; $j++) {
            if ($i==$j) continue;
            for ($k=0; $k<3; $k++) {
              if ($i==$k || $j==$k) continue;
              $output[] = $digit[$i] . $digit[$j] . $digit[$k];
            }
          }
        }
        
        return array_unique($output);
    }
    public function permutations4($val){
        $chars = str_split($val);
        sort($chars);
        $result = [];
        foreach ($chars as $char1) {
            $chars2 = array_diff($chars, [$char1]);
            foreach ($chars2 as $char2) {
                $chars3 = array_diff($chars2, [$char2]);
                foreach ($chars3 as $char3) {
                    $chars4 = array_diff($chars3, [$char3]);
                    foreach ($chars4 as $char4) {
                        $result[] = $char1 . $char2 . $char3 . $char4;
                    }
                }
            }
        }
        return array_unique($result);
    }
    
    
    
}
