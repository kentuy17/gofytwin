<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topups extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'merchant_order_num',
        'action',
        'order',
        'amount',
        'gateway',
        'merchant_order_remark',
        'status',
        'sign',
        'ma_id',
        'sa_id',
        'ma_amount',
        'sa_amount',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
