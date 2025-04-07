<?php

namespace App\Console\Commands;

use App\Events\BetEvent;
use Illuminate\Console\Command;
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
        $securedBet = collect([
            'uuid' => Str::uuid(),
            'side' => 'M',
            'amount' => 500,
        ]);

        event(new BetEvent($securedBet));


    }
}
