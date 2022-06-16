<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $dates = [
        'date'
    ];

    public static function isHoliDay(Carbon $dateTime)
    {
        return self::whereDate('date', $dateTime->toDateString())
            ->exists();
    }
}
