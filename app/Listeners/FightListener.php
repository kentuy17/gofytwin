<?php

namespace App\Listeners;

use App\Events\FightEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FightListener implements ShouldQueue
{
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
        Log::info(json_encode($event));

        // if($event->currentFight->status === 'CLOSED') {
        //     // update bets and bet_histories
        // }
    }
}
