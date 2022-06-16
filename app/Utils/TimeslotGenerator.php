<?php

namespace App\Utils;

use App\Models\Event;
use App\Models\EventBreak;
use App\Models\ShopTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeslotGenerator
{
    protected $event;

    protected $bookingDay;

    public function __construct(Event $event, $bookingDay)
    {
        $this->event = $event;

        $this->bookingDay = $bookingDay;
    }

    /**
     * Generate a list of timeslots for the given event.
     * 
     * @return Collection
     */
    public function getSlots()
    {
        $day = $this->bookingDay->toDateString();
        
        $timeslotKey = "{$day}-{$this->event->id}";

        // If we have already generated the timeslots for this day, just return them.
        if(cache()->has($timeslotKey)) {
            return cache()->get($timeslotKey);
        }

        $shopTimes = ShopTime::where('day', $this->bookingDay->format('l'))->firstOrFail();

        // Since we've stored the break times in the database as a string, 
        // we need to convert them to an Carbon object.
        $breakTimes = EventBreak::all()->map(function($break) {
            return (object) [
                "title" => $break->title,
                "starts_at" => Carbon::parse("{$this->bookingDay->toDateString()} {$break->starts_at}:00"),
                "ends_at" => Carbon::parse("{$this->bookingDay->toDateString()} {$break->ends_at}:00"),
            ];
        });

        $opensAt = Carbon::parse("{$this->bookingDay->toDateString()} {$shopTimes->opens_at}:00");

        $closesAt = Carbon::parse("{$this->bookingDay->toDateString()} {$shopTimes->closes_at}:00");

        $timeslots = [];
        
        while ($opensAt <= $closesAt) {

            // If the current time is during a break, skip it.
            $breakStartsAt = $breakTimes->first(function($break) use ($opensAt) {
                return $opensAt->gte($break->starts_at) && $opensAt->lt($break->ends_at);
            });

            if($breakStartsAt) {
                $opensAt = $breakStartsAt->ends_at;

                continue;
            }

            $timeslots[] = $opensAt->toDateTimeString();
            
            $opensAt->addMinutes(
                $this->event->event_duration_minutes + $this->event->cleaning_duration_minutes
            );

            if($opensAt >= $closesAt) {
                break;
            }
        }

        // Cache the timeslots for this day so we don't have to generate them again.
        cache()->put($timeslotKey, collect($timeslots), Carbon::now()->addDay());

        return collect($timeslots);
    }
}
