<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Callbacks extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'msg',
        'merchant_slug',
        'merchant_order_num',
        'action',
        'order',
        'user_id',
    ];
}
