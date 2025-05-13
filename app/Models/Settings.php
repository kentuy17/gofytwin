<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'field',
        'value',
    ];

    static function get($field)
    {
        $item = self::where('field', $field)->first();
        return $item->value;
    }


}
