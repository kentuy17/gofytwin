<?php

namespace App\Console\Commands;

use App\Events\BetEvent;
use App\Models\EventGame;
use App\Models\Fight;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Str;

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
        //
        // $securedBet = collect([
        //     'uuid' => Str::uuid(),
        //     'side' => 'M',
        //     'amount' => 500,
        // ]);

        // event(new BetEvent($securedBet));

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
        $prev = Fight::find(1);
        $new = Fight::create([
             'fight_no' => $prev->id + 1,
             'user_id' => 15,
             'status' => 'WAITING',
             'event_id' =>$event->id,
             'time_start' => Carbon::now()->toTimeString()
         ]);
         Redis::set('fight', $new);

        // SET ALL USER_LEVEL to 1
        // $new = User::where('user_level', 0)
           // ->update([
           //     'user_level' => 1
           // ]);


        $this->info(json_encode($new));

    }
}
