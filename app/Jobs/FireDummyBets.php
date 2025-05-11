<?php

namespace App\Jobs;

use App\Http\Controllers\BetController;
// use App\Models\Bet;
use App\Models\Fight;
// use App\Models\Settings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Str;

class FireDummyBets implements ShouldQueue
{
    use Queueable;
    private $currentFight;
    private $dummy_limit;

    /**
     * Create a new job instance.
     */
    public function __construct(Fight $currentFight, $dummy_limit)
    {
        $this->currentFight = $currentFight;
        $this->dummy_limit = $dummy_limit;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $betController = new BetController();
        $limit = $this->dummy_limit;
        $option = ['M' => 0, 'W' => 0];

        $choices = array('M', 'W');
        $amount = range(100,1500);

        Log::channel('test')->info($limit);

        while($option['M'] <= $limit || $option['W'] <= $limit) {
            $side = $choices[array_rand($choices)];
            $money = $amount[array_rand($amount)];
            if($option[$side] > $limit) continue;
            if($option['M'] > $limit && $option['W'] > $limit) break;

            $bet = [
                'fight_id' => $this->currentFight->id,
                'fight_no' => $this->currentFight->fight_no,
                'uuid' => Str::uuid(),
                'side' => $side,
                'amount' => $money,
                'status' => 'F',
                'total' => $option[$side],
            ];

            // Log::channel('test')->info(json_encode($bet));
            $betController->dummyBets($bet);
            $option[$side] += $money;

            usleep(rand(100,500) * 1000);
        }


    }
}
