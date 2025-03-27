<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'merchant_order_num' => $this->merchant_order_num,
            'action' => $this->action,
            'order' => $this->order,
            'amount' => $this->amount,
            'gateway' => $this->gateway,
            'merchant_order_remark' => $this->merchant_order_remark,
            'status' => $this->status,
            'sign' => $this->sign,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ma_id' => $this->ma_id,
            'sa_id' => $this->sa_id,
            'ma_amount' => $this->ma_amount,
            'sa_amount' => $this->sa_amount,
        ];
        
    }
}
