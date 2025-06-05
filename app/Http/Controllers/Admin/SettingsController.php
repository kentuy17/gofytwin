<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Credits;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    
    public function index(){
        if(auth()->user()->userlevel < self::ACCOUNTING){

            return redirect()->back()->with('error', 'Access restricted.');
        }
        $settings = new Settings;
        // dd($settings->where('field','twoD_winning_amount')->first()->value);
        return view(
            'admin.settings.index',[
                'settings' => $settings
            ]
        );
    }

    public function tipsterSettings(){
        if(auth()->user()->userlevel < self::ACCOUNTING){
            
            return redirect()->back()->with('error', 'Access restricted.');
        }
        $settings = Settings::all();

        return view(
            'admin.settings.tipster',[
                'settings' => $settings
            ]
        );
    }

    public function twodSettings(){
        if(auth()->user()->userlevel < self::ACCOUNTING){
            
            return redirect()->back()->with('error', 'Access restricted.');
        }
        $settings = new Settings;

        return view(
            'admin.settings.2d',[
                'settings' => $settings
            ]
        );
    }

    public function store(Request $req){
        if(auth()->user()->userlevel < self::ACCOUNTING){
            
            // return redirect()->back()->with('error', 'Access restricted.');
            return back()
            ->with('error','You are not allowed!');
        }
        $req->validate([
            'logo_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
            'header_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
            'banner_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
            'login_banner_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
            'cashin_banner' => 'mimes:jpg,jpeg,png,gif|max:2048',
            'background_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
            '2d_tipster_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
            '3d_tipster_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
            '4d_tipster_image' => 'mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        
        $settings = new Settings;
        if($req->file('logo_image')) {
            $settings = Settings::where('field','logo_image')->get();
            if(count($settings) > 0){
                $file = $req->file('logo_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','logo_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('logo_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "logo_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
            
        }
        if($req->file('header_image')) {
            $settings = Settings::where('field','header_image')->get();
            if(count($settings) > 0){
                $file = $req->file('header_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','header_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('header_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "header_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
            
        }
        if($req->file('banner_image')) {
            $settings = Settings::where('field','banner_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('banner_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','banner_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('banner_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "banner_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }

        if($req->file('cashin_banner')) {
            $settings = Settings::where('field','cashin_banner')->get();
            if(count($settings) > 0) {
                $file = $req->file('cashin_banner');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','cashin_banner')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('cashin_banner');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "cashin_banner";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('login_banner_image')) {
            $settings = Settings::where('field','login_banner_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('login_banner_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','login_banner_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('login_banner_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "login_banner_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('background_image')) {
            $settings = Settings::where('field','background_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('background_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','background_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('background_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "background_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('og_image')) {
            $settings = Settings::where('field','og_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('og_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','og_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('og_image');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "og_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('twod_tipster_image')) {
            $settings = Settings::where('field','twod_tipster_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('twod_tipster_image');
                $fileName = "Play2DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);

                Settings::where('field','twod_tipster_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('twod_tipster_image');
                $fileName = "Play2DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);
                $settings->field = "twod_tipster_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('threed_tipster_image')) {
            $settings = Settings::where('field','threed_tipster_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('threed_tipster_image');
                $fileName = "Play3DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);

                Settings::where('field','threed_tipster_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('threed_tipster_image');
                $fileName = "Play3DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);
                $settings->field = "threed_tipster_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('fourd_tipster_image')) {
            $settings = Settings::where('field','fourd_tipster_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('fourd_tipster_image');
                $fileName = "Play4DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);

                Settings::where('field','fourd_tipster_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('fourd_tipster_image');
                $fileName = "Play4DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);
                $settings->field = "fourd_tipster_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('sixd_tipster_image')) {
            $settings = Settings::where('field','sixd_tipster_image')->get();
            if(count($settings) > 0) {
                $file = $req->file('sixd_tipster_image');
                $fileName = "Play6DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);

                Settings::where('field','sixd_tipster_image')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('sixd_tipster_image');
                $fileName = "Play6DTipster".time().'.'.$file->extension();
                $file->move(public_path('images'), $fileName);
                $settings->field = "sixd_tipster_image";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('barcode_2d')) {
            $settings = Settings::where('field','barcode_2d')->get();
            if(count($settings) > 0) {
                $file = $req->file('barcode_2d');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','barcode_2d')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('barcode_2d');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "barcode_2d";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        
        if($req->file('barcode_3d')) {
            $settings = Settings::where('field','barcode_3d')->get();
            if(count($settings) > 0) {
                $file = $req->file('barcode_3d');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','barcode_3d')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('barcode_3d');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "barcode_3d";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->file('barcode_4d')) {
            $settings = Settings::where('field','barcode_4d')->get();
            if(count($settings) > 0) {
                $file = $req->file('barcode_4d');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);

                Settings::where('field','barcode_4d')->update([
                   'value' => 'images/'.$fileName
                ]);
            } else {
                $settings = new Settings;
                $file = $req->file('barcode_4d');
                $fileName = time().str_replace(' ','',$file->getClientOriginalName());
                $file->move(public_path('images'), $fileName);
                $settings->field = "barcode_4d";
                $settings->value = 'images/'.$fileName;
                $settings->save();
            }
        }
        if($req->input('twoD_winning_amount')) {
            $settings = Settings::where('field','twoD_winning_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_winning_amount')->update([
                    'value' => intval($req->input('twoD_winning_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_winning_amount";
                $settings->value = intval($req->input('twoD_winning_amount'));
                $settings->save();
            }
        }

        if($req->input('threeD_winning_amount')) {
            $settings = Settings::where('field','threeD_winning_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_winning_amount')->update([
                    'value' => intval($req->input('threeD_winning_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_winning_amount";
                $settings->value = intval($req->input('threeD_winning_amount'));
                $settings->save();
            }
        }
        
        if($req->input('fourD_winning_amount')) {
            $settings = Settings::where('field','fourD_winning_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourD_winning_amount')->update([
                    'value' => intval($req->input('fourD_winning_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourD_winning_amount";
                $settings->value = intval($req->input('fourD_winning_amount'));
                $settings->save();
            }
        }
        if($req->input('jackpot_4d')) {
            $settings = Settings::where('field','jackpot_4d')->get();
            if(count($settings) > 0) {
                Settings::where('field','jackpot_4d')->update([
                    'value' => intval($req->input('jackpot_4d'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "jackpot_4d";
                $settings->value = intval($req->input('jackpot_4d'));
                $settings->save();
            }
        }

        if($req->input('fourd_monday_draw')) {
            $settings = Settings::where('field','fourd_monday_draw')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourd_monday_draw')->update([
                    'value' => $req->input('fourd_monday_draw')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourd_monday_draw";
                $settings->value = $req->input('fourd_monday_draw');
                $settings->save();
            }
        }

        if($req->input('fourd_wednesday_draw')) {
            $settings = Settings::where('field','fourd_wednesday_draw')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourd_wednesday_draw')->update([
                    'value' => $req->input('fourd_wednesday_draw')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourd_wednesday_draw";
                $settings->value = $req->input('fourd_wednesday_draw');
                $settings->save();
            }
        }

        if($req->input('fourd_friday_draw')) {
            $settings = Settings::where('field','fourd_friday_draw')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourd_friday_draw')->update([
                    'value' => $req->input('fourd_friday_draw')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourd_friday_draw";
                $settings->value = $req->input('fourd_friday_draw');
                $settings->save();
            }
        }

        if($req->input('twoD_min_bet')) {
            $settings = Settings::where('field','twoD_min_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_min_bet')->update([
                    'value' => intval($req->input('twoD_min_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_min_bet";
                $settings->value = intval($req->input('twoD_min_bet'));
                $settings->save();
            }
        }

        if($req->input('twoD_max_bet')) {
            $settings = Settings::where('field','twoD_max_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_max_bet')->update([
                    'value' => intval($req->input('twoD_max_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_max_bet";
                $settings->value = intval($req->input('twoD_max_bet'));
                $settings->save();
            }
        }

        if($req->input('gcash_gateway')) {
            $settings = Settings::where('field','gcash_gateway')->get();
            if(count($settings) > 0) {
                Settings::where('field','gcash_gateway')->update([
                    'value' => $req->input('gcash_gateway')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "gcash_gateway";
                $settings->value = $req->input('gcash_gateway');
                $settings->save();
            }
        }
        if($req->input('paymaya_gateway')) {
            $settings = Settings::where('field','paymaya_gateway')->get();
            if(count($settings) > 0) {
                Settings::where('field','paymaya_gateway')->update([
                    'value' => $req->input('paymaya_gateway')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "paymaya_gateway";
                $settings->value = $req->input('paymaya_gateway');
                $settings->save();
            }
        }
        if($req->input('captcha_threshold')) {
            $settings = Settings::where('field','captcha_threshold')->get();
            if(count($settings) > 0) {
                Settings::where('field','captcha_threshold')->update([
                    'value' => $req->input('captcha_threshold')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "captcha_threshold";
                $settings->value = $req->input('captcha_threshold');
                $settings->save();
            }
        }
        if($req->input('sms_api_key')) {
            $settings = Settings::where('field','sms_api_key')->get();
            if(count($settings) > 0) {
                Settings::where('field','sms_api_key')->update([
                    'value' => $req->input('sms_api_key')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sms_api_key";
                $settings->value = $req->input('sms_api_key');
                $settings->save();
            }
        }
        if($req->input('sms_sender')) {
            $settings = Settings::where('field','sms_sender')->get();
            if(count($settings) > 0) {
                Settings::where('field','sms_sender')->update([
                    'value' => $req->input('sms_sender')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sms_sender";
                $settings->value = $req->input('sms_sender');
                $settings->save();
            }
        }
        if($req->input('text_blast_api_key')) {
            $settings = Settings::where('field','text_blast_api_key')->get();
            if(count($settings) > 0) {
                Settings::where('field','text_blast_api_key')->update([
                    'value' => $req->input('text_blast_api_key')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "text_blast_api_key";
                $settings->value = $req->input('text_blast_api_key');
                $settings->save();
            }
        }
        if($req->input('text_blast_sender')) {
            $settings = Settings::where('field','text_blast_sender')->get();
            if(count($settings) > 0) {
                Settings::where('field','text_blast_sender')->update([
                    'value' => $req->input('text_blast_sender')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "text_blast_sender";
                $settings->value = $req->input('text_blast_sender');
                $settings->save();
            }
        }
        if($req->input('sms_message_endpoint')) {
            $settings = Settings::where('field','sms_message_endpoint')->get();
            if(count($settings) > 0) {
                Settings::where('field','sms_message_endpoint')->update([
                    'value' => $req->input('sms_message_endpoint')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sms_message_endpoint";
                $settings->value = $req->input('sms_message_endpoint');
                $settings->save();
            }
        }
        if($req->input('otp_endpoint')) {
            $settings = Settings::where('field','otp_endpoint')->get();
            if(count($settings) > 0) {
                Settings::where('field','otp_endpoint')->update([
                    'value' => $req->input('otp_endpoint')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "otp_endpoint";
                $settings->value = $req->input('otp_endpoint');
                $settings->save();
            }
        }
        if($req->input('account_endpoint')) {
            $settings = Settings::where('field','account_endpoint')->get();
            if(count($settings) > 0) {
                Settings::where('field','account_endpoint')->update([
                    'value' => $req->input('account_endpoint')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "account_endpoint";
                $settings->value = $req->input('account_endpoint');
                $settings->save();
            }
        }
        if($req->input('custom_date')) {
            $settings = Settings::where('field','custom_date')->get();
            if(count($settings) > 0) {
                Settings::where('field','custom_date')->update([
                    'value' => $req->input('custom_date')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "custom_date";
                $settings->value = $req->input('custom_date');
                $settings->save();
            }
        }
        if($req->input('messenger_link')) {
            $settings = Settings::where('field','messenger_link')->get();
            if(count($settings) > 0) {
                Settings::where('field','messenger_link')->update([
                    'value' => $req->input('messenger_link')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "messenger_link";
                $settings->value = $req->input('messenger_link');
                $settings->save();
            }
        }
        if($req->input('telegram_link')) {
            $settings = Settings::where('field','telegram_link')->get();
            if(count($settings) > 0) {
                Settings::where('field','telegram_link')->update([
                    'value' => $req->input('telegram_link')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "telegram_link";
                $settings->value = $req->input('telegram_link');
                $settings->save();
            }
        }


        if($req->input('is_maintenance')) {
            $settings = Settings::where('field','is_maintenance')->get();
            if(count($settings) > 0) {
                Settings::where('field','is_maintenance')->update([
                    'value' => $req->input('is_maintenance')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "is_maintenance";
                $settings->value = $req->input('is_maintenance');
                $settings->save();
            }
        }

        #PG
        if($req->input('pg_gcash_easy_pay_1')) {
            $settings = Settings::where('field','pg_gcash_easy_pay_1')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_easy_pay_1')->update([
                    'value' => $req->input('pg_gcash_easy_pay_1')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_easy_pay_1";
                $settings->value = $req->input('pg_gcash_easy_pay_1');
                $settings->save();
            }
        }

        if($req->input('pg_gcash_easy_pay_2')) {
            $settings = Settings::where('field','pg_gcash_easy_pay_2')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_easy_pay_2')->update([
                    'value' => $req->input('pg_gcash_easy_pay_2')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_easy_pay_2";
                $settings->value = $req->input('pg_gcash_easy_pay_2');
                $settings->save();
            }
        }

        if($req->input('pg_gcash_redirect')) {
            $settings = Settings::where('field','pg_gcash_redirect')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_redirect')->update([
                    'value' => $req->input('pg_gcash_redirect')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_redirect";
                $settings->value = $req->input('pg_gcash_redirect');
                $settings->save();
            }
        }
        if($req->input('pg_gcash_bayad')) {
            $settings = Settings::where('field','pg_gcash_bayad')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_bayad')->update([
                    'value' => $req->input('pg_gcash_bayad')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_bayad";
                $settings->value = $req->input('pg_gcash_bayad');
                $settings->save();
            }
        }

        if($req->input('pg_gcash_redirect_min')) {
            $settings = Settings::where('field','pg_gcash_redirect_min')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_redirect_min')->update([
                    'value' => $req->input('pg_gcash_redirect_min')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_redirect_min";
                $settings->value = $req->input('pg_gcash_redirect_min');
                $settings->save();
            }
        }

        if($req->input('pg_gcash_p2p_qr_pay_1')) {
            $settings = Settings::where('field','pg_gcash_p2p_qr_pay_1')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_p2p_qr_pay_1')->update([
                    'value' => $req->input('pg_gcash_p2p_qr_pay_1')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_p2p_qr_pay_1";
                $settings->value = $req->input('pg_gcash_p2p_qr_pay_1');
                $settings->save();
            }
        }
        if($req->input('pg_gcash_p2p_qr_pay_1_min')) {
            $settings = Settings::where('field','pg_gcash_p2p_qr_pay_1_min')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_p2p_qr_pay_1_min')->update([
                    'value' => $req->input('pg_gcash_p2p_qr_pay_1_min')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_p2p_qr_pay_1_min";
                $settings->value = $req->input('pg_gcash_p2p_qr_pay_1_min');
                $settings->save();
            }
        }
        if($req->input('pg_gcash_qr_pay_1')) {
            $settings = Settings::where('field','pg_gcash_qr_pay_1')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_gcash_qr_pay_1')->update([
                    'value' => $req->input('pg_gcash_qr_pay_1')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_gcash_qr_pay_1";
                $settings->value = $req->input('pg_gcash_qr_pay_1');
                $settings->save();
            }
        }
        if($req->input('pg_paymaya_2')) {
            $settings = Settings::where('field','pg_paymaya_2')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_paymaya_2')->update([
                    'value' => $req->input('pg_paymaya_2')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_paymaya_2";
                $settings->value = $req->input('pg_paymaya_2');
                $settings->save();
            }
        }

        if($req->input('pg_paymaya_1')) {
            $settings = Settings::where('field','pg_paymaya_1')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_paymaya_1')->update([
                    'value' => $req->input('pg_paymaya_1')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_paymaya_1";
                $settings->value = $req->input('pg_paymaya_1');
                $settings->save();
            }
        }
        if($req->input('pg_manual')) {
            $settings = Settings::where('field','pg_manual')->get();
            if(count($settings) > 0) {
                Settings::where('field','pg_manual')->update([
                    'value' => $req->input('pg_manual')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "pg_manual";
                $settings->value = $req->input('pg_manual');
                $settings->save();
            }
        }



        if($req->input('maintenance_text')) {
            $settings = Settings::where('field','maintenance_text')->get();
            if(count($settings) > 0) {
                Settings::where('field','maintenance_text')->update([
                    'value' => $req->input('maintenance_text')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "maintenance_text";
                $settings->value = intval($req->input('maintenance_text'));
                $settings->save();
            }
        }

        if($req->input('threeD_min_bet')) {
            $settings = Settings::where('field','threeD_min_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_min_bet')->update([
                    'value' => intval($req->input('threeD_min_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_min_bet";
                $settings->value = intval($req->input('threeD_min_bet'));
                $settings->save();
            }
        }

        if($req->input('threeD_max_bet')) {
            $settings = Settings::where('field','threeD_max_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_max_bet')->update([
                    'value' => intval($req->input('threeD_max_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_max_bet";
                $settings->value = intval($req->input('threeD_max_bet'));
                $settings->save();
            }
        }

        if($req->input('fourD_min_bet')) {
            $settings = Settings::where('field','fourD_min_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourD_min_bet')->update([
                    'value' => intval($req->input('fourD_min_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourD_min_bet";
                $settings->value = intval($req->input('fourD_min_bet'));
                $settings->save();
            }
        }

        if($req->input('fourD_max_bet')) {
            $settings = Settings::where('field','fourD_max_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourD_max_bet')->update([
                    'value' => intval($req->input('fourD_max_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourD_max_bet";
                $settings->value = intval($req->input('fourD_max_bet'));
                $settings->save();
            }
        }

        if($req->input('fourD_last_three_win_amount')) {
            $settings = Settings::where('field','fourD_last_three_win_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourD_last_three_win_amount')->update([
                    'value' => intval($req->input('fourD_last_three_win_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourD_last_three_win_amount";
                $settings->value = intval($req->input('fourD_last_three_win_amount'));
                $settings->save();
            }
        }

        if($req->input('fourD_last_two_win_amount')) {
            $settings = Settings::where('field','fourD_last_two_win_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourD_last_two_win_amount')->update([
                    'value' => intval($req->input('fourD_last_two_win_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourD_last_two_win_amount";
                $settings->value = intval($req->input('fourD_last_two_win_amount'));
                $settings->save();
            }
        }

        if($req->input('twoD_open_time')) {
            $settings = Settings::where('field','twoD_open_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_open_time')->update([
                    'value' => $req->input('twoD_open_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_open_time";
                $settings->value = $req->input('twoD_open_time');
                $settings->save();
            }
        }

        if($req->input('twoD_close_time')) {
            $settings = Settings::where('field','twoD_close_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_close_time')->update([
                    'value' => $req->input('twoD_close_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_close_time";
                $settings->value = $req->input('twoD_close_time');
                $settings->save();
            }
        }

        if($req->input('twoD_2pm_draw_cutoff')) {
            $settings = Settings::where('field','twoD_2pm_draw_cutoff')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_2pm_draw_cutoff')->update([
                    'value' => $req->input('twoD_2pm_draw_cutoff')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_2pm_draw_cutoff";
                $settings->value = $req->input('twoD_2pm_draw_cutoff');
                $settings->save();
            }
        }
        if($req->input('twoD_5pm_draw_cutoff')) {
            $settings = Settings::where('field','twoD_5pm_draw_cutoff')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_5pm_draw_cutoff')->update([
                    'value' => $req->input('twoD_5pm_draw_cutoff')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_5pm_draw_cutoff";
                $settings->value = $req->input('twoD_5pm_draw_cutoff');
                $settings->save();
            }
        }
        if($req->input('twoD_9pm_draw_cutoff')) {
            $settings = Settings::where('field','twoD_9pm_draw_cutoff')->get();
            if(count($settings) > 0) {
                Settings::where('field','twoD_9pm_draw_cutoff')->update([
                    'value' => $req->input('twoD_9pm_draw_cutoff')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "twoD_9pm_draw_cutoff";
                $settings->value = $req->input('twoD_9pm_draw_cutoff');
                $settings->save();
            }
        }

        // six digits
        if($req->input('sixD_open_time')) {
            $settings = Settings::where('field','sixD_open_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','sixD_open_time')->update([
                    'value' => $req->input('sixD_open_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sixD_open_time";
                $settings->value = $req->input('sixD_open_time');
                $settings->save();
            }
        }
        if($req->input('sixD_close_time')) {
            $settings = Settings::where('field','sixD_close_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','sixD_close_time')->update([
                    'value' => $req->input('sixD_close_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sixD_close_time";
                $settings->value = $req->input('sixD_close_time');
                $settings->save();
            }
        }

        if($req->input('sixD_max_bet')) {
            $settings = Settings::where('field','sixD_max_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','sixD_max_bet')->update([
                    'value' => intval($req->input('sixD_max_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sixD_max_bet";
                $settings->value = intval($req->input('sixD_max_bet'));
                $settings->save();
            }
        }
        if($req->input('sixD_min_bet')) {
            $settings = Settings::where('field','sixD_min_bet')->get();
            if(count($settings) > 0) {
                Settings::where('field','sixD_min_bet')->update([
                    'value' => intval($req->input('sixD_min_bet'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sixD_min_bet";
                $settings->value = intval($req->input('sixD_min_bet'));
                $settings->save();
            }
        }
        if($req->input('jai_profitability')) {
            $settings = Settings::where('field','jai_profitability')->get();
            if(count($settings) > 0) {
                Settings::where('field','jai_profitability')->update([
                    'value' => intval($req->input('jai_profitability'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "jai_profitability";
                $settings->value = intval($req->input('jai_profitability'));
                $settings->save();
            }
        }
        if($req->input('jai_win_rate')) {
            $settings = Settings::where('field','jai_win_rate')->get();
            if(count($settings) > 0) {
                Settings::where('field','jai_win_rate')->update([
                    'value' => intval($req->input('jai_win_rate'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "jai_win_rate";
                $settings->value = intval($req->input('jai_win_rate'));
                $settings->save();
            }
        }
        if($req->input('jai_winning_amount')) {
            $settings = Settings::where('field','jai_winning_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','jai_winning_amount')->update([
                    'value' => intval($req->input('jai_winning_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "jai_winning_amount";
                $settings->value = intval($req->input('jai_winning_amount'));
                $settings->save();
            }
        }
        if($req->input('sixD_winning_amount')) {
            $settings = Settings::where('field','sixD_winning_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','sixD_winning_amount')->update([
                    'value' => intval($req->input('sixD_winning_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sixD_winning_amount";
                $settings->value = intval($req->input('sixD_winning_amount'));
                $settings->save();
            }
        }
        if($req->input('sixD_5_win_amount')) {
            $settings = Settings::where('field','sixD_5_win_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','sixD_5_win_amount')->update([
                    'value' => intval($req->input('sixD_5_win_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sixD_5_win_amount";
                $settings->value = intval($req->input('sixD_5_win_amount'));
                $settings->save();
            }
        }
        if($req->input('sixD_4_win_amount')) {
            $settings = Settings::where('field','sixD_4_win_amount')->get();
            if(count($settings) > 0) {
                Settings::where('field','sixD_4_win_amount')->update([
                    'value' => intval($req->input('sixD_4_win_amount'))
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "sixD_4_win_amount";
                $settings->value = intval($req->input('sixD_4_win_amount'));
                $settings->save();
            }
        }






        // @@@@@@@@@@@
        if($req->input('threeD_2pm_draw_cutoff')) {
            $settings = Settings::where('field','threeD_2pm_draw_cutoff')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_2pm_draw_cutoff')->update([
                    'value' => $req->input('threeD_2pm_draw_cutoff')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_2pm_draw_cutoff";
                $settings->value = $req->input('threeD_2pm_draw_cutoff');
                $settings->save();
            }
        }
        if($req->input('threeD_5pm_draw_cutoff')) {
            $settings = Settings::where('field','threeD_5pm_draw_cutoff')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_5pm_draw_cutoff')->update([
                    'value' => $req->input('threeD_5pm_draw_cutoff')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_5pm_draw_cutoff";
                $settings->value = $req->input('threeD_5pm_draw_cutoff');
                $settings->save();
            }
        }
        if($req->input('threeD_9pm_draw_cutoff')) {
            $settings = Settings::where('field','threeD_9pm_draw_cutoff')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_9pm_draw_cutoff')->update([
                    'value' => $req->input('threeD_9pm_draw_cutoff')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_9pm_draw_cutoff";
                $settings->value = $req->input('threeD_9pm_draw_cutoff');
                $settings->save();
            }
        }

        // @@@@@@@@@@@

        if($req->input('threeD_open_time')) {
            $settings = Settings::where('field','threeD_open_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_open_time')->update([
                    'value' => $req->input('threeD_open_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_open_time";
                $settings->value = $req->input('threeD_open_time');
                $settings->save();
            }
        }

        if($req->input('threeD_close_time')) {
            $settings = Settings::where('field','threeD_close_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','threeD_close_time')->update([
                    'value' => $req->input('threeD_close_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "threeD_close_time";
                $settings->value = $req->input('threeD_close_time');
                $settings->save();
            }
        }

        if($req->input('fourD_open_time')) {
            $settings = Settings::where('field','fourD_open_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourD_open_time')->update([
                    'value' => $req->input('fourD_open_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourD_open_time";
                $settings->value = $req->input('fourD_open_time');
                $settings->save();
            }
        }

        if($req->input('fourD_close_time')) {
            $settings = Settings::where('field','fourD_close_time')->get();
            if(count($settings) > 0) {
                Settings::where('field','fourD_close_time')->update([
                    'value' => $req->input('fourD_close_time')
                ]);
            } else {
                $settings = new Settings;
                $settings->field = "fourD_close_time";
                $settings->value = $req->input('fourD_close_time');
                $settings->save();
            }
        }

        return back()
            ->with('success','Settings successfully save!');
   }
}
