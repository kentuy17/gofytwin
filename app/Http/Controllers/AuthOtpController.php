<?php

namespace App\Http\Controllers;

use App\Models\Credits;
use App\Models\Promos;
use App\Models\SettingsModel;
use App\Models\TextBlast;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthOtpController extends Controller
{
    protected $settings;

    protected $background_image = 'images/site-bg.png';

    const SUCCESSFUL_OTP = 'successful_otp';

    const FREE_CREDITS = 0;

    const MOBILE_APP_URL = 'https://gamefowlmaster.com';

    public function __construct()
    {

        $this->settings = new SettingsModel;
        $this->background_image = $this->settings->getValueFor('background_image');
        $this->ip_address = file_get_contents('https://api.ipify.org');
    }

    public function dls()
    {
        $url = 'http://www.google.co.in/intl/en_com/images/srpr/logo1w.png';
        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        Storage::put($name, $contents);
    }

    // Return View of OTP Login Page
    public function login(Request $request)
    {
        return [
            'status' => $request->session()->get('status'),
            'app_url' => self::MOBILE_APP_URL,
            'landing_page' => true,
            'ref_code' => null,
            'settings' => $this->settings,
            'home_banner' => null !== ($this->settings->getValueFor('banner_image')) ? $this->settings->getValueFor('banner_image') : 'images/homepage-banner.png',
            'background_image' => $this->background_image,
            'og_image' => $this->settings->getValueFor('og_image'),
        ];
    }

    public function check(Request $request)
    {
        $recaptcha_resp = $request->all();
        $token = $recaptcha_resp['token'];
        // return json_encode(array('success' => false, "msg"=>$token));
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => '6LfLkEspAAAAAKCYLnhlpR3pXVpC_UVDIsDtpVZl',
            'response' => $token,
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result);
        // dd($response->score);

        /*
        - google response score is between 0.0 to 1.0
        - if score is 0.5, it's a human
        - if score is 0.0, it's a bot
        - google recommend to use score 0.5 for verify human
        */
        if ($response->success && $response->score >= $this->settings->getValueFor('captcha_threshold')) {
            $serv_resp = json_encode(['success' => true, 'msg' => 'You are not a robot!', 'response' => $response]);

            return $serv_resp;
        } else {
            $serv_resp = json_encode(['success' => false, 'msg' => 'Robot!!!', 'response' => $response]);

            return $serv_resp;
        }
    }

    // Generate OTP
    public function generate(Request $request)
    {
        // Check if User has OTP, if not, generate
        $verificationCode = $this->generateOtp($request);
        if (! $verificationCode) {
            return Redirect::to('/login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        $user = User::find($verificationCode->user_id);
        if ($user->user_attempts > 2) {
            $user->account_status = 'inactive';
            $user->save();

            return json_encode(['success' => false, 'msg' => 'Your account has been disabled for too many attempts. Please contact support below:', 'response' => null]);
        }

        // If Otp has not expired
        if (! $verificationCode->expire_at) {
            $current_attempt = $user->user_attempts;
            $next_attempt = $current_attempt + 1;
            $user->user_attempts = $next_attempt;
            $user->save();

            return Redirect::to('/otp/verification/'.$verificationCode->user_id)
                ->with('msg', 'Check you Phone for your OTP.');
        }

        $text_message = 'Ka-Number! Your OTP Confirmation Code is: {otp}.';

        // seme SMS
        $text = new TextBlast;
        $text->otpSend($request->mobile_no, $text_message, $verificationCode->otp);
        $message = 'Your STL - OTP Login Code will be sent via SMS now. Thank You!'; // .$verificationCode->otp;

        // Return With OTP Your STL verification code is 6086. Please do not share. Happy Betting!

        return Redirect::to('/otp/verification/'.$verificationCode->user_id);

        return json_encode(
            [
                'success' => true,
                'msg' => 'Congratulations! Your account has been created.',
                'redirect_url' => URL::to('/').'/otp/verification/'.$verificationCode->user_id,
                'response' => null,
            ]
        );
        // return redirect()->route('otp.verification', ['user_id' => $verificationCode->user_id])->with('success',  $message);
    }

    public function redirectOtpComponent()
    {
        return Inertia::render('auth/otp', [
            'code' => $verificationCode ?? null,
            'user' => $user ?? null,
            'ip' => $this->ip_address,
        ]);
    }

    public function url_origin($s, $use_forwarded_host = false)
    {
        $ssl = (! empty($s['HTTPS']) && $s['HTTPS'] == 'on');
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')).(($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((! $ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':'.$port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'].$port;

        return $protocol.'://'.$host;
    }

    public function full_url($s, $use_forwarded_host = false)
    {
        return $this->url_origin($s, $use_forwarded_host).$s['REQUEST_URI'];
    }

    public function generateOtp($request)
    {
        // return response()->json([
        //     'requests' => $request->all(),
        //     'status' => 'success',
        //     'message' => 'otp generated'
        // ], 200);

        $base_url = URL::to('/');
        $user = User::where('mobile_no', $request->mobile_no)->first();
        if ($user) {
            if (str_contains($base_url, 'gofytwin') && ! in_array($user->id, $this->allowed_access)) {

                return redirect(self::FAKE_URL);
            }
            if ($user->account_status != 'active' || $user->user_attempts >= 3) {

                return false;
            }
        }
        $location = null;
        if (! $location) {
            $city = null;
        } else {
            $city = $location->city;
        }

        $site_check_url = config('app.debug') ? 'gofytwin' : '127.0.0.1';
        if (! $user) {
            if (str_contains($base_url, $site_check_url)) {
                return redirect(self::FAKE_URL);
            }

            // $absolute_url = $this->full_url( $_SERVER );
            $user = User::create([
                'name' => $request->mobile_no,
                'password' => $request->mobile_no,
                'mobile_no' => $request->mobile_no,
                'email' => $request->mobile_no.'@gmail.com',
                'location' => $city,
                'percentage' => 0,
                'userlevel' => 0,
                'is_referral' => 0,
                'user_attempts' => 1,
                'is_new' => 1,
                'website_url' => URL::to('/'),
                'website_register' => URL::to('/') ? URL::to('/') : $this->full_url($_SERVER),
                'account_status' => 'active',
                'credits' => 0,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ]);

            $ref = 0;
            $ref = 0;
            $user->is_referral = $ref;
            $user->save();
        }

        // User Does not Have Any Existing OTP
        $verificationCode = VerificationCode::where('user_id', $user->id)->latest()->first();

        $now = Carbon::now();

        // user has OPT and not yet expired
        if ($verificationCode && $now->isBefore($verificationCode->expire_at)) {
            $verificationCode->expire_at = false;

            return $verificationCode;
        }

        // Create a New OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => rand(1234, 9999),
            'expire_at' => Carbon::now()->addMinutes(5),
        ]);
    }

    public function verification($user_id)
    {
        if (Auth::check()) {

            return redirect('/');
        }
        $user = User::find($user_id);
        $mobile = $user->mobile_no;

        $verificationCode = VerificationCode::where('user_id', $user->id)->latest()->first();
        $now = Carbon::now();
        // user has OPT and not yet expired
        if ($verificationCode && $now->isBefore($verificationCode->expire_at)) {

            $is_expired = false;
        } else {

            $is_expired = true;
        }

        return Inertia::render('auth/otp', [
            'code' => $verificationCode,
            'user' => $user,
            'ip' => $this->ip_address,
        ]);
    }

    public function loginWithOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect('/');
        }

        // Validation
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required',
        ]);

        $user = User::whereId($request->user_id)->first();
        $user->user_attempts += 1;
        $user->save();

        // Validation Logic
        $verificationCode = VerificationCode::where('user_id', $request->user_id)->where('otp', $request->otp)->first();

        $now = Carbon::now();
        if (! $verificationCode) {
            throw ValidationException::withMessages([
                'success' => false,
                'msg' => 'Confirmation Code is INCORRECT.',
                'response' => null,
            ]);
        }

        if ($verificationCode && $now->isAfter($verificationCode->expire_at)) {
            // throw ValidationException::withMessages([
            //     'success' => false,
            //     'msg' => 'Your OTP has expired',
            //     'response' => null,
            // ]);
            return Redirect::to('/login')
                ->with('error', 'Your OTP has expired.');
        }

        if (! $user) {
            return redirect()->route('otp.login')->with('error', 'Invalid user!');
        }

        $verificationCode->update([
            'expire_at' => Carbon::now(),
        ]);

        if ($user->is_new == 1) {
            $user->is_new = 0;
            $user->save();
        } elseif (URL::to('/') == self::MOBILE_APP_URL) {
            // first, check if user has already availed +50 credits
            $has_availed_promo = Promos::where('user_id', $request->user_id)
                ->where('promo_type', self::PROMO_DOWNLOAD_APP)
                ->first();
            if (! $has_availed_promo) {
                $current_amount = $credits->amount;
                $credits->amount = $current_amount + self::FREE_CREDITS;
                $credits->save();
            }
        }

        // Congratulations!!!
        Auth::login($user);

        return Inertia::render('dashboard', [
            'commonData' => [
                'code' => $verificationCode,
                'user' => Auth::user(),
                'ip' => $this->ip_address,
            ],
        ]);
    }
}
