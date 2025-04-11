<?php

namespace App\Http\Middleware;

use App\Models\Bet;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Inertia\Middleware;
use App\Http\Controllers\FightController;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $global = [];

        if(Auth::check()) {
            $fight = json_decode(Redis::get('fight'));
            $activeBets = Bet::where('fight_id', $fight->id)->get();
            $playerActiveBets = $activeBets->where('user_id', Auth::user()->id);
            $fightController = new FightController();

            $global = [
                'fight' => $fight,
                'player' => [
                    'meron' => $playerActiveBets->where('side','M')->sum('amount'),
                    'wala' => $playerActiveBets->where('side','W')->sum('amount'),
                ],
                'total' => [
                    'meron' => $activeBets->where('side','M')->sum('amount'),
                    'wala' => $activeBets->where('side','W')->sum('amount'),
                ],
                'trend' => $fightController->getFightTrends($request),
            ];
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'session' => $request->session()->all(),
            ],
            'globals' => $global,
            'user' => $request->user(),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
