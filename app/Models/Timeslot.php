<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    use HasFactory;

    public $guarded = [];

    public $timestamps = false;

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
