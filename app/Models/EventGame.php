<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class EventGame extends Model
{
    use HasFactory;
    protected $table = 'event_games';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'event_date',
        'time_start',
        'current_fight',
        'limit_fight',
        'status',
        'added_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:M-d H:i:s',
        'updated_at' => 'datetime:M-d H:i:s',
    ];

    protected $hidden = [
        'added_by',
        'updated_by'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('M-d H:i:s');
    }

    public function fights()
    {
        $this->hasMany(Fight::class, 'event_id', 'id');
    }

    public function next()
    {
        return $this->where('id', '>', $this->id)->orderBy('id', 'asc')->first();
    }

    public  function previous()
    {
        return $this->where('id', '<', $this->id)->orderBy('id', 'desc')->first();
    }

    public function active()
    {
        $check = $this->where('status', 'ACTIVE')->orderBy('id', 'desc')->first();
        if(!$check) {
            return $this->create([
                'name' => 'EVENT GAMEFOWL MASTER',
                'event_date' => Carbon::now(),
                'time_start' => '08:00:00',
                'status' => 'ACTIVE',
                'added_by' => 1,
            ]);
        }

        return $check;
    }
}
