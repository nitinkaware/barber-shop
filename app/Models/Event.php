<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    public function timeslots()
    {
        return $this->hasMany(Timeslot::class);
    }

    /**
     * Create a new timeslot and booking for this event.
     * 
     * @return Boolean
     * @throws Exception
     */
    public function createBooking(array $attributes, Carbon $startAt)
    {
        try {
            DB::beginTransaction();

            $timeslot = Timeslot::firstOrCreate([
                'event_id' => $this->id,
                'starts_at' => $attributes['starts_at'],
                'ends_at' => $startAt->addMinutes($this->event_duration_minutes)
            ]);
    
            Booking::create($attributes + [
                'timeslot_id' => $timeslot->id,
            ]);
    
            $timeslot->increment('total_confirmed_bookings');

            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
