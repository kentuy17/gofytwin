<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'fight_id' => $this->fight_id,
            'fight_no' => $this->fight_no,
            'amount' => $this->amount,
            'side' => $this->side,
            'status' => $this->status,
            'win_amount' => $this->win_amount,
            'agent_commission' => $this->agent_commission,
        ];
        
    }
}
