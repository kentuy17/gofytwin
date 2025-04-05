<?php


namespace App\Http\Controllers\API;


use App\Models\User;
use App\Models\Carts;
use App\Models\Bets2ds;
use App\Models\Bets3ds;
use App\Models\Bets4ds;
use App\Models\Cart2ds;
use App\Models\Cart3ds;
use App\Models\Cart4ds;
use Illuminate\Http\Request;
use App\Models\HotNumbers2ds;
use App\Models\HotNumbers3ds;
use App\Models\HotNumbers4ds;
use App\Models\SettingsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    public $csr = 0;
    public $operations = 1000;
    public $accounting = 2000;
    public $super_admin = 5000;


    protected $twoD_jackpot;
    protected $min_bet_2ds;
    protected $max_bet_2ds;

    protected $threeD_jackpot;
    protected $max_bet_3ds;
    protected $min_bet_3ds;

    protected $min_bet_4ds;
    protected $max_bet_4ds;

    protected $jackpot_4d;
    protected $last_three_4d;
    protected $last_two_4d;

    protected $settings;

    protected $background_image = "images/site-bg.png";

    public function __construct(
        SettingsModel $settings
    ){
        $this->settings = $settings;
        $this->twoD_jackpot = $this->settings->getValueFor('twoD_winning_amount');
        $this->min_bet_2ds = $this->settings->getValueFor('twoD_min_bet');
        $this->max_bet_2ds = $this->settings->getValueFor('twoD_max_bet');

        $this->threeD_jackpot = $this->settings->getValueFor('threeD_winning_amount');
        $this->max_bet_3ds = $this->settings->getValueFor('threeD_max_bet');
        $this->min_bet_3ds = $this->settings->getValueFor('threeD_min_bet');

        $this->max_bet_4ds = $this->settings->getValueFor('fourD_max_bet');
        $this->min_bet_4ds = $this->settings->getValueFor('fourD_min_bet');

        $this->jackpot_4d = $this->settings->getValueFor('jackpot_4d');
        $this->last_three_4d = $this->settings->getValueFor('last_three_4d');
        $this->last_two_4d = $this->settings->getValueFor('last_two_4d');
        $this->background_image = $this->settings->getValueFor('background_image');
    }
    public function checkMaintenance(){
        $maintenance = $this->settings->getValueFor('is_maintenance');
        if($maintenance == 'yes'){

            return view('maintenance',[
                'maintenance_text' => $this->settings->getValueFor('maintenance_text'),
                'settings' => $this->settings,
                'home_banner' => null !== ($this->settings->getValueFor('banner_image')) ? $this->settings->getValueFor('banner_image') : 'images/homepage-banner.png',
                'background_image' => $this->background_image
            ]);
        }

        return false;
    }
    public function threeDigitRumbleWinner($input){
        $results = DB::select("select 
                                id, d1, d2, d3,
                                (
                                    (d1 IN (".$input['d1'].", ".$input['d2'].", ".$input['d3'].")) ++ 
                                    (d2 IN (".$input['d1'].", ".$input['d2'].", ".$input['d3'].")) ++ 
                                    (d3 IN (".$input['d1'].", ".$input['d2'].", ".$input['d3']."))
                                ) AS main 
                                FROM bets 
                                HAVING main = 3");
                                dd($results);
    }
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }

    public function getIp($url = 'http://ip-api.com/json/'){
        $url .= $_SERVER["REMOTE_ADDR"];
        if($_SERVER["REMOTE_ADDR"] == '127.0.0.1'){
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);
        
        return $data;

    }

    protected function checkAndVerify($data){
        $return = [
            'errors' => false,
            'error_message' => ''
        ];
        $bets = Carts::where('contact_no', $data['contact_no'])
                        ->where('draw_date',$data['draw_date'])
                        ->where('is_paid',$data['is_paid'])
                        ->where('ip_address',$data['ip_address']);
        if($bets->count() >= 10){
            $return = [
                'errors' => true,
                'error_message' => 'Maximum of 10 bets per transaction only.'
            ];

            return $return;
        }

        return $return;
    }
    protected function getJackpotThreeDigits(){
        return $this->threeD_jackpot;
    }

    protected function getJackpotTwoDigits(){
        return $this->twoD_jackpot;
    }
    protected function getMaxBetTwoDigits(){

        return $this->max_bet_2ds;
    }
    protected function getMinBetTwoDigits(){

        return $this->min_bet_2ds;
    }
    protected function getMaxBetThreeDigits(){

        return $this->max_bet_3ds;
    }
    protected function getMinBetThreeDigits(){

        return $this->min_bet_3ds;
    }
    protected function get4dJackpot(){
        // $total = $this->jackpot_4d / $this->min_bet_4ds;
        // $winningAmount = ceil($total * $betAmount);
        
        return $this->jackpot_4d;
    }
    protected function get4dLastThree($betAmount){

        return $this->last_three_4d;
    }
    protected function get4dLastTwo($betAmount){

        return $this->last_two_4d;
    }

   

    protected function getThreeDigitsRumble($winningComb){

        return count(array_unique(str_split($winningComb)));
    }

    protected function getTwoDigitsRumbleWinningAmount($betAmount){
        $jackpot = $this->getJackpotTwoDigits();
        $perPesoValue = $jackpot / $this->getMinBetTwoDigits() / 2;

        $winningAmount =  ceil($perPesoValue * $betAmount);

        return $winningAmount;
    }

    protected function getTwoDigitsWinningAmount($betAmount){
        $jackpot = $this->getJackpotTwoDigits();
        $perPesoValue = $jackpot / $this->getMinBetTwoDigits();

         $winningAmount =  ceil($perPesoValue * $betAmount);

         return $winningAmount;
    }
    protected function getTthreeDigitsWinningAmount($betAmount){
        $jackpot = $this->getJackpotThreeDigits();
        $perPesoValue = $jackpot / $this->getMinBetThreeDigits();
        $winningAmount =  $perPesoValue * $betAmount;

        return $winningAmount;
    }
    
    protected function getThreeDigitsRumbleThreeWinningAmount($betAmount){
        $jackpot = $this->getJackpotThreeDigits();
        $perPesoValue = $jackpot / $this->getMinBetThreeDigits() / 6;
        $winningAmount =  ceil($perPesoValue * $betAmount);

        return $winningAmount;
    }

    protected function getThreeDigitsRumbleTwoWinningAmount($betAmount){
        $jackpot = $this->getJackpotThreeDigits();
        $perPesoValue = $jackpot / $this->getMinBetThreeDigits() / 3;
        $winningAmount =  ceil($perPesoValue * $betAmount);

        return $winningAmount;
    }
    public function getImage($imageUrl){

        $data = array('url'=>$imageUrl);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://hcti.io/v1/image");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($ch, CURLOPT_POST, 1);
        // Retrieve your user_id and api_key from https://htmlcsstoimage.com/dashboard
        curl_setopt($ch, CURLOPT_USERPWD, "68249d5c-059b-4e1c-8326-83b6c5b30263" . ":" . "577e6c9c-210a-4ea3-8a59-df962b522b96");

        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        $res = json_decode($result,true);

        return $res['url'];
    }
    public function dl($url){
        $file_name = basename($url);
        
        if (file_put_contents($file_name, file_get_contents($url)))
        {
            return $file_name;
        }
        
    }

    public function checkbet(Request $request){
        if(!$request->all()){
            return redirect('/');
        }
        if(null === Session::get('three_play.row_position')){
            $row_position = 0;
        } else {
            $row_position = Session::get('three_play.row_position');
        }
        
        $result = false;
        $container = [];
        if($request->order == 'RUMBLE'){
            
            $all_number_good = true;
            $original_combination = $request->combination;
            $combinations = $this->permutations3($request->combination);
            $divider = count($combinations);
            if($request->row_entries + $divider >30){
                $this->errors = "Only 30 items are allowed.";
                return $this->sendError($this->errors);
            }
            $original_amount = $request->amount;
            $request->amount = ceil($original_amount / $divider);
            foreach($combinations as $number_combination){
                $six_digit_random_number = random_int(100000, 999999);
                $request->combination = $number_combination;
                if($firstComb = $this->filterCombination($request)){
                    array_push($container,$firstComb);
                } else {
                    $all_number_good = false;
                    array_push($container,[
                        'time'=>$request->draw_time,
                        'row'=>$six_digit_random_number,
                        'comb'=>$number_combination,
                        'bet'=> 0,
                        'contact'=>$request->contact_no,
                        'order'=>'STRAIGHT',
                        'row_position'=>$row_position+1
                    ]);
                }
            }
            if($all_number_good !== false){
                
                $container = [];
                array_push($container,[
                    'time'=>$request->draw_time,
                    'row'=>random_int(100000, 999999),
                    'comb'=>$original_combination,
                    'bet'=> $original_amount,
                    'contact'=>$request->contact_no,
                    'order'=>$request->order,
                    'row_position'=>$row_position+1
                ]);
            }
            $result = $container;
            // dd($result);
        } else {
            $check = $this->filterCombination($request);
            if(!$check){

                return $this->sendError($this->errors);
            }
            array_push($container,$check);
            $result = $container;
        }
        if(!$result){

            return $this->sendError($this->errors);
        }
        // dd($result);
        foreach($result as $res){
            Session::put('three_play.row_position', $row_position+1);
            Session::put('three_play.number_bets.'.$res['row'].'.'.$res['order'].'.'.$res['contact'].'.'.$res['comb'], $res['bet']);
        }
        return $this->sendResponse($result, $this->errors);
    }
    
    public function getUserBalance(){
        $balance = User::find(auth()->user()->id); 
        $balance = $balance->credits->amount;

        return $balance;
    }
    public function cartsTotalBetsTotalAmount4d($request){
        $hot_number_limit = HotNumbers4ds::where('combination',$request->combination)
                                            ->where('draw_time',$request->draw_time)
                                            ->first()
                                            ->amount;
        $cart_total_for_combination = Cart4ds::where('combination',$request->combination)
                                            ->where('draw_time',$request->draw_time)
                                            ->where('draw_date',$request->draw_date)->sum('amount');
        $bets_total_for_combination = Bets4ds::where('d1',$request->combination[0])
                                            ->where('d2',$request->combination[1])
                                            ->where('d3',$request->combination[2])
                                            ->where('d4',$request->combination[3])
                                            ->where('draw_time',$request->draw_time)
                                            ->where('draw_date',$request->draw_date)->sum('amount');   
        $total = $request->amount + $cart_total_for_combination + $bets_total_for_combination;         
        
        if($total > $hot_number_limit){

            return false;
        }

        return true;
    }
    public function cartsTotalBetsTotalAmount3d($request){
        $hot_number_limit = HotNumbers3ds::where('combination',$request->combination)
                                            ->where('draw_time',$request->draw_time)
                                            ->first()
                                            ->amount;
        $cart_total_for_combination = Cart3ds::where('combination',$request->combination)
                                            ->where('draw_time',$request->draw_time)
                                            ->where('draw_date',$request->draw_date)->sum('amount');
        $bets_total_for_combination = Bets3ds::where('d1',$request->combination[0])
                                            ->where('d2',$request->combination[1])
                                            ->where('d3',$request->combination[2])
                                            ->where('draw_time',$request->draw_time)
                                            ->where('draw_date',$request->draw_date)->sum('amount');   
        $total = $request->amount + $cart_total_for_combination + $bets_total_for_combination;         
        
        if($total > $hot_number_limit){

            return false;
        }

        return true;
    }
    public function cartsTotalBetsTotalAmount2d($request){
        $hot_number_limit = HotNumbers2ds::where('d1',$request->d1)
                                            ->where('d2',$request->d2)
                                            ->where('draw_time',$request->draw_time)
                                            ->first()
                                            ->amount;
        $cart_total_for_combination = Cart2ds::where('d1',$request->d1)
                                            ->where('d2',$request->d2)
                                            ->where('draw_time',$request->draw_time)
                                            ->where('draw_date',$request->draw_date)->sum('amount');
        $bets_total_for_combination = Bets2ds::where('d1',$request->d1)
                                            ->where('d2',$request->d2)
                                            ->where('draw_time',$request->draw_time)
                                            ->where('draw_date',$request->draw_date)->sum('amount');   
        $total = $request->amount + $cart_total_for_combination + $bets_total_for_combination;         
        
        if($total > $hot_number_limit){

            return false;
        }

        return true;
    }
    public function canBet($drawTime = '2PM'){

    }
    public function permutation4dNew($combination){
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
}