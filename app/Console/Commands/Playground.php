<?php

namespace App\Console\Commands;

use App\Events\BetEvent;
use App\Events\FightEvent;
use App\Events\ResultEvent;
use App\Models\Bet;
use App\Models\BetHistory;
use App\Models\EventGame;
use App\Models\Fight;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Str;
use Illuminate\Support\Facades\Log;

class Playground extends Command
{
    /**M
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $bet = Bet::orderBy('id', 'desc')->first();

        //
        // $securedBet = collect([
        //     'uuid' => Str::uuid(),
        //     'side' => 'M',
        //     'amount' => 500,
        // ]);

        // event(new BetEvent($bet));

        // MANUAL CREATE FIGHT
        $event = EventGame::where('status', 'ACTIVE')->orderBy('id', 'desc')->first();
        // $current = json_decode(Redis::get('fight'));
        // $fight = Fight::create([
        //     'fight_no' => $current->fight_no + 1,
        //     'user_id' => 15,
        //     // 'status' => 'OPEN',
        //     'event_id' =>$event->id,
        //     'time_start' => Carbon::now()->toTimeString()
        // ]);

        // MANUAL CLOSE FIGHT
        $currentFight = Fight::where('event_id', $event->id)
            ->orderBy('id', 'desc')->first();
        // $new = Fight::create([
        //      'fight_no' => $prev->id + 1,
        //      'user_id' => 15,
        //      'status' => 'WAITING',
        //      'event_id' =>$event->id,
        //      'time_start' => Carbon::now()->toTimeString()
        //  ]);
         Redis::set('fight', $currentFight);
        // event(new FightEvent($currentFight));
        // FightEvent::dispatch($currentFight);
        // Log::channel('test')->info(json_encode($currentFight, true));

        // SET ALL USER_LEVEL to 1
        // $new = User::where('user_level', 0)
           // ->update([
           //     'user_level' => 1
           // ]);

        $bet = BetHistory::orderBy('id', 'desc')->first();
        ResultEvent::dispatch($bet);
        // $fight = json_decode(Redis::get('fight'));


        $this->info(json_encode($bet));

    }
}
