<?php

namespace App\Http\Controllers;

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

        // $bets = Bet::where('user_id', Auth::user()->id)->get();

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
                'show_video' => Settings::get('show_video'),
                'video_banner' => Settings::get('video_banner'),
            ]
        ]);
    }

    public function adminConfig()
    {
        $settings = Settings::whereIn('field', [
            'show_video',
            'video_banner',
            'dummy_bet_amount',
        ])->get();

        return Inertia::render('admin/config', [
            'user' => Auth::user(),
            'commonData' => $this->getCurrentData(),
            'settings' => $settings,
        ]);
    }

    public function updateConfig(Request $request)
    {
        $configs = [
            "show_video",
            // "video_banner",
            "dummy_bet_amount",
        ];

        foreach ($configs as $conf) {
            $item = Settings::where('field', $conf)->first();
            $item->value = $request->{$conf};
            $item->save();
        }

        if($request->hasFile('video_banner')) {
            $storage = Storage::path('images');
            if (!is_dir($storage)) {
                mkdir( $storage,0775, true);
            } else {
                chmod($storage, 775);
            }

            $request->validate([
                'video_banner' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = time().'.'.$request->file('video_banner')->extension();
            $request->video_banner->move(public_path('images'), $imageName);

            $banner = Settings::where('field', 'video_banner')->first();
            $banner->value = "/images/" . $imageName;
            $banner->save();
        }

        return Redirect::back()->with('success', 'File uploaded successfully');
    }

    public function toggleVideo(Request $request)
    {
        $update = Settings::where('field', 'show_video')
            ->update(['value' => $request->show_video]);

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
        $user = User::find(1);
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
