<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'user_id',
        'mobile_no',
        'msg',
        'amount',
        'action',
        'gateway',
        'event_type',
        'status',
        'sign',
        'date',
        'data',
    ];
}
