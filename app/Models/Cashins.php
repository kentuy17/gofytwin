<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cashins extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gateway',
        'amount',
        'sign',
        'merchant_order_no',
        'status',
    ];
    public function user()
    {
        
        return $this->belongsTo('App\Models\User');
    }
}
