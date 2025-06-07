<?php

namespace App\Listeners;

use App\Events\FightEvent;
use App\Jobs\FireDummyBets;
use App\Models\Settings;
use Illuminate\Support\Facades\Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class InitiateDummyBets implements ShouldQueue
{
    // use InteractsWithQueue;

    public $connection = 'database';

    public $queue = 'listeners';
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FightEvent $event): void
    {
        // php artisan queue:work database --queue=listeners
        if ($event->currentFight->status === 'OPEN') {
            // dispatch(new FireDummyBets($event->currentFight));
            $limit = Settings::get('dummy_bet_amount');
            $exec_time = Settings::get('dummy_exec_time');
            FireDummyBets::dispatch($event->currentFight, $limit, $exec_time);
        }
    }
}
