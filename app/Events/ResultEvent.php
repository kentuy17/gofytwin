<?php

namespace App\Events;

use App\Models\Bet;
use App\Models\BetHistory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResultEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bet;

    /**
     * Create a new event instance.
     */
    public function __construct(BetHistory $bet)
    {
        $this->bet = $bet;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    // public function broadcastOn(): array
    // {
    //     return ['result-channel'];
    // }

    // public function broadcastAs()
    // {
    //     return 'result-event';
    // }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('result.'.$this->bet->user_id),
        ];
    }

    // public function broadcastAs()
    // {
    //     return 'result-event';
    // }
}
