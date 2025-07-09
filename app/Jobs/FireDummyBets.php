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
    private $exec_time;

    /**
     * Create a new job instance.
     */
    public function __construct(Fight $currentFight, $dummy_limit, $exec_time)
    {
        $this->currentFight = $currentFight;
        $this->dummy_limit = $dummy_limit;
        $this->exec_time = $exec_time;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $betController = new BetController();
        $limit = $this->dummy_limit;
        $option = ['M' => 0, 'W' => 0];

        $choices = array('M', 'W');
        $amount = range(100, 900);
        $dummy_bets = [];

        while ($option['M'] <= $limit || $option['W'] <= $limit) {
            $side = $choices[array_rand($choices)];
            $money = $amount[array_rand($amount)];

            if ($option[$side] > $limit) continue;

            if ((($option['M'] - $option['W']) > 1000) && $side == 'M') {
                $side = 'W';
            }

            if ((($option['W'] - $option['M']) > 1000) && $side == 'W') {
                $side = 'M';
            }

            if ($option['M'] > $limit && $option['W'] > $limit) break;

            $dummy_bets[] = [
                'fight_id' => $this->currentFight->id,
                'fight_no' => $this->currentFight->fight_no,
                'uuid' => Str::uuid(),
                'side' => $side,
                'amount' => $money,
                'status' => 'F',
                'total' => $option[$side],
            ];

            // Log::channel('test')->info(json_encode($bet));
            $option[$side] += $money;
        }

        $delay = ($this->exec_time * 1000000) / count($dummy_bets);
        // Log::channel('test')->info($delay);
        foreach ($dummy_bets as $bet) {
            $betController->dummyBets($bet);
            // Log::channel('test')->info(json_encode($bet));
            usleep(floor($delay));
        }
    }
}
