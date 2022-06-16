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

    /**
     * Check if the given date is a holiday.
     * 
     * @return Boolean
     */
    public static function isHoliday(Carbon $dateTime)
    {
        return self::whereDate('date', $dateTime->toDateString())
            ->exists();
    }
}
