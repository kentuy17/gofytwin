<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsModel extends Model
{
    use HasFactory;

    protected $table = 'settings';

    public function getValueFor($field)
    {

        if ($this::where('field', $field)->first()) {
            return $this::where('field', $field)->first()->value;
        }

        return false;
    }

    public function canPlay($drawTime, $play_code = '2')
    {

        switch ($drawTime) {
            case '2PM':
                if ($play_code == '2') {
                    $date = date_create($this->getValueFor('twoD_2pm_draw_cutoff'));
                } else {
                    $date = date_create($this->getValueFor('threeD_2pm_draw_cutoff'));
                }
                $cutoffTime = date_format($date, 'H.i');
                if (date('H.i') < $cutoffTime) {
                    return true;
                } else {
                    return false;
                }
                break;
            case '5PM':
                if ($play_code == '2') {
                    $date = date_create($this->getValueFor('twoD_5pm_draw_cutoff'));
                } else {
                    $date = date_create($this->getValueFor('threeD_5pm_draw_cutoff'));
                }
                $cutoffTime = date_format($date, 'H.i');
                if (date('H.i') < $cutoffTime) {
                    return true;
                } else {
                    return false;
                }
                break;
            case '9PM':
                if ($play_code == '2') {
                    $date = date_create($this->getValueFor('twoD_9pm_draw_cutoff'));
                } elseif ($play_code == '3') {
                    $date = date_create($this->getValueFor('threeD_9pm_draw_cutoff'));
                } else {
                    $date = date_create($this->getValueFor('fourD_close_time'));
                }
                $cutoffTime = date_format($date, 'H.i');
                if (date('H.i') < $cutoffTime) {
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }
    }
}
