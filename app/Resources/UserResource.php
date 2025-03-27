<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'mobile_no' => $this->mobile_no,
            // load events when loaded
            // 'events' => EventResource::collection($this->whenLoaded('events')),
            'user_level' => $this->user_level,
            'location' => $this->location,
            'percentage' => $this->percentage,
            'account_status' => $this->account_status,
            'ip' => $this->ip,
            'topup' => $this->whenLoaded('topup', function () {
                
                return new TopupResource($this->event);
            }),
            'bets2ds' => $this->whenLoaded('bets2ds', function () {
                
                return new Bets2dResource($this->event);
            }),
            'bets3ds' => $this->whenLoaded('bet32ds', function () {
                
                return new Bets3dResource($this->event);
            }),
            'bets4ds' => $this->whenLoaded('bets4ds', function () {
                
                return new Bets4dResource($this->event);
            }),
            'bets6ds' => $this->whenLoaded('bets6ds', function () {
                
                return new Bets6dResource($this->event);
            }),
            'user_attepts' => $this->user_attepts,
            'is_referral' => $this->is_referral,
            'website_url' => $this->website_url,
            'is_new' => $this->is_new,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        
    }
}
