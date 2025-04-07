<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Fight;
use DateTimeInterface;

class BetHistory extends Model
{
    use HasFactory;
    protected $table = 'bet_histories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'bet_id',
        'user_id',
        'fight_id',
        'fight_no',
        'status',
        'side',
        'percent',
        'win_amount',
        'points_before_bet',
        'amount',
        'points_after_bet',
        'current_points',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:M-d H:i:s',
        'updated_at' => 'datetime:M-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('M-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function fight()
    {
        return $this->belongsTo(Fight::class, 'fight_id', 'id');
    }

    public function createBetHistory(array $params)
    {
        return $this->create($params);
    }

    public function updateBetHistory(int $id, array $params) : int
    {
        return $this->where('id', $id)->update($params);
    }

    public function getBetHistoryByUserId(int $user_id) : array
    {
        return $this->where('user_id', $user_id)->get()->toArray();
    }

}
