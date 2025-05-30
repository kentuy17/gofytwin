<?php

namespace App\Models;

use App\Models\User;
use App\Models\Bets3ds;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawals extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'reference_no',
        'contact_no',
        'receipt',
        'claimed',
        'upload',
        'approved',
        'payout_remittance',
        'receiver_name',
        'receiver_address',
        'receiver_mobile_number',
        'merchant_order_no',
        'claimed_date',
        'process_status',
        'message',
    ];

    // Relationship to Bets
    public function user(){

        return $this->belongsTo(User::class);
    }
}

