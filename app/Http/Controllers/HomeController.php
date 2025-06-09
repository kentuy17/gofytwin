<?php

namespace App\Http\Controllers;

use App\Events\LastCallEvent;
use App\Models\Bet;
use App\Models\EventGame;
use App\Models\Fight;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    private $fight;
    private $event;

    public function __construct()
    {
        $this->fight = json_decode(Redis::get('fight'));
        $this->event = EventGame::where('status', 'ACTIVE')
        ->orderBy('id', 'DESC')
        ->first();

    }

    public function index()
    {
        $session = request()->session()->get('status');
        if (!$session) {
            Inertia::clearHistory();
        }

        $bets = Bet::where('user_id', Auth::user()->id)->get();
        $userCredits = User::find(Auth::user()->id)->credits;

        return Inertia::render('poll/dashboard', [
            'user' => Auth::user(),
            'commonData' => $this->getCurrentData(),
            'player' => [
                'bets' => [
                    'meron' => $this->getUserTotalBets('M', $this->fight->id),
                    'wala' => $this->getUserTotalBets('W', $this->fight->id),
                ],
                'credits' => Auth::user()->credits,
            ],
            'settings' => [
                'user_credits' => $userCredits,
                'show_video' => Settings::get('show_video'),
                'pg_gcash' => Settings::get('pg_gcash'),
                'pg_maya' => Settings::get('pg_maya'),
                'video_banner' => Settings::get('video_banner'),
                'no_credits_banner' => Settings::get('no_credits_banner'),
            ]
        ]);
    }

    public function adminConfig()
    {
        if(auth()->user()->user_level < self::SUPER_ADMIN){

            return Redirect::back()->with('error', 'You are not allowed!.');
        }
        $settings = Settings::whereIn('field', [
            'show_video',
            'pg_maya',
            'pg_gcash',
            'video_banner',
            'dummy_bet_amount',
            'min_bet',
            'max_bet',
            'no_credits_banner',
            'dummy_exec_time',
        ])->get();

        return Inertia::render('admin/config', [
            'user' => Auth::user(),
            'commonData' => $this->getCurrentData(),
            'settings' => $settings,
        ]);
    }

    public function updateConfig(Request $request)
    {
        if(auth()->user()->user_level < self::SUPER_ADMIN){

            return Redirect::back()->with('error', 'You are not allowed!.');
        }
        $configs = [
            "show_video",
            "pg_gcash",
            "pg_maya",
            // "video_banner",
            "dummy_bet_amount",
            'min_bet',
            'max_bet',
            // 'no_credits_banner',
            'dummy_exec_time',
        ];

        foreach ($configs as $conf) {
            $item = Settings::where('field', $conf)->first();

            if($item) {
                $item->value = $request->{$conf};
                $item->save();
            } else {
                Settings::create([
                    'field' => $conf,
                    'value' => $request->{$conf}
                ]);
            }
        }

        $upload_configs = [
            'no_credits_banner',
            'video_banner'
        ];

        foreach ($upload_configs as $config) {
            if($request->hasFile($config)) {
                $storage = Storage::path('images');
                if (!is_dir($storage)) {
                    mkdir( $storage,0775, true);
                } else {
                    chmod($storage, 775);
                }

                $request->validate([
                    $config => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                $imageName = time().'.'.$request->file($config)->extension();
                $request->{$config}->move(public_path('images'), $imageName);

                $banner = Settings::where('field', $config)->first();
                $banner->value = "/images/" . $imageName;
                $banner->save();
            }
        }

        return Redirect::back()->with('success', 'File uploaded successfully');
    }

    public function toggleVideo(Request $request)
    {
        Settings::where('field', 'show_video')
            ->update(['value' => $request->show_video]);

        $event_req = new Request([
            'type' => 'show_video',
            'value' => $request->show_video
        ]);

        event(new LastCallEvent($event_req));

        return Redirect::back()->with('success', 'Updated successfully.');
    }

    public function getUserTotalBets($side, $fight_id)
    {
        return Bet::where('fight_id', $fight_id)
            ->where('user_id', Auth::user()->id)
            ->where('side', $side)
            ->sum('amount');
    }

    public function getSideBetsPerFight($side, $fight_id)
    {
        return Bet::where('fight_id', $fight_id)
            ->where('side', $side)
            ->sum('amount');
    }

    public function getCurrentData()
    {
        $fight = Redis::get('fight');
        if (!$fight) {
            $event = EventGame::where('status', 'ACTIVE')
                ->orderBy('id', 'DESC')
                ->first();

            $fight = Fight::where('event_id', $event->id)
                ->orderBy('id', 'DESC')
                ->first();

            Redis::set('fight', json_encode($fight));
        }

        $fight = json_decode(Redis::get('fight'));
        $meron = $this->getSideBetsPerFight('M', $fight->id);
        $wala = $this->getSideBetsPerFight('W', $fight->id);
        $lastCall = Redis::get('lastCall');

        // dd($fight);

        $data = [
            'meron' => $meron,
            'wala' => $wala,
            'fight_no' => $fight->fight_no,
            'status' => $fight->status,
            'lastCall' => $lastCall,
        ];

        return $data;
    }

    public function temporaryLogin()
    {
        $user = User::find(18);
        Auth::login($user);
    }

    public function temporaryLogout()
    {
        Auth::logout();
    }

    public function live_feed()
    {
        return view(
            'live-feed',
            [
                'user_phone' => 'wewewqew',
                'landing_page' => true,
            ]
        );
    }
}
