<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashinGrids extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashin_id',
        'user_id',
        'mobile_no',
        'merchant_order_no',
        'gateway',
        'amount',
        'sign',
        'status',
    ];
}
