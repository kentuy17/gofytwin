<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\BetsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CashinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $total_bets =   Bets2ds::where('user_id',$this->user_id)->sum('amount') +
        //                 Bets3ds::where('user_id',$this->user_id)->sum('amount') +
        //                 Bets4ds::where('user_id',$this->user_id)->sum('amount') +
        //                 Bets6ds::where('user_id',$this->user_id)->sum('amount');

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'merchant_order_no' => $this->merchant_order_no,
            'mobile_no' => $this->mobile_no,
            'gateway' => $this->gateway,
            'amount' => $this->amount,
            'sign' => $this->sign,
            'status' => $this->status,
            'created_at' =>date('M d, Y h:iA',strtotime($this->created_at)),
            // 'total_bets' => $total_bets,
            // 'bets' => BetsResource::collection($this->whenLoaded('bets3ds')),
            // 'racers' => RacerResource::collection($this->whenLoaded('racers')),
        ];
    }
}
