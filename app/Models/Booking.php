<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $fillable = [
        'email_address',
        'first_name',
        'last_name',
        'timeslot_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
