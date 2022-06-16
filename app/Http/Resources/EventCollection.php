<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'eventDurationInMinutes' => $event->event_duration_minutes,
                'cleaningDurationInMinutes' => $event->cleaning_duration_minutes,
                'advanceBookingAllowedInDays' => $event->advance_booking_days,
                'minimumMinutesBeforeStart' => $event->min_minutes_before_start,
                'maxBookingsPerSlot' => $event->max_bookings_per_slot,
                'timeslots' => $event->timeslots->map(function ($timeslot) use ($event) {
                    return [
                        'id' => $timeslot->id,
                        'startsAt' => $timeslot->starts_at,
                        'endsAt' => $timeslot->ends_at,
                        'totalBookings' => $timeslot->total_confirmed_bookings,
                        'availableQuantityLeft' => $event->max_bookings_per_slot - $timeslot->total_confirmed_bookings,
                        'bookings' => $timeslot->bookings->map(function ($booking) {
                            return [
                                'id' => $booking->id,
                                'firstName' => $booking->first_name,
                                'lastName' => $booking->last_name,
                                'emailAddress' => $booking->email_address,
                            ];
                        })
                    ];
                }),
            ];
        });
    }
}
