<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bet;
use App\Models\DerbyEvent;
use DateTimeInterface;

class Fight extends Model
{
    use HasFactory;
    protected $table = 'fights';
    protected $fillable = [
        'fight_no',
        'user_id',
        'game_winner',
        'status', // 'WAITING','OPEN','CLOSED','DONE'
        'created_at',
        'updated_at',
        'event_id'
    ];

    protected $hidden = [
        'user_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('m-d-y H:i');
    }

    public function bet()
    {
        return $this->hasMany(Bet::class, 'fight_id');
    }

    public function bethistory()
    {
        return $this->hasMany(BetHistory::class, 'fight_id');
    }

    public function event()
    {
        return $this->belongsTo(DerbyEvent::class, 'event_id');
    }
}
