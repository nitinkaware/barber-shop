<?php

namespace App\Http\Requests;

use App\Models\Holiday;
use App\Utils\TimeslotGenerator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class EventBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'starts_at' => ['bail', 'required', 'date', 'after:now', $this->validateMinMinutesBeforeStarts(), $this->mustNotBePublicHoliday(), $this->validateAdvanceBooking(), $this->mustBeValidTimeslot(), $this->validateMaxBookings()],
            'email_address' => ['required', 'email', $this->mustBeUniqueEmail()],
            'first_name' => ['required'],
            'last_name' => ['required']
        ];
    }

    public function messages() 
    {
        return [
            'email_address.unique' => 'This email address already exits for this event',
        ];
    }

    public function mustBeUniqueEmail()
    {
        return function ($attribute, $value, $fail) {
            $isBookedWithSameEmail = $this->event
                                ->timeslots()
                                ->join('bookings', 'timeslots.id', '=', 'bookings.timeslot_id')
                                ->where('bookings.email_address', $this->email_address)
                                ->where('timeslots.starts_at', $this->startsAt())
                                ->exists();

            if ($isBookedWithSameEmail) {
                $fail("This email address already exits for this event");
            }
        };
    }

    public function validateMinMinutesBeforeStarts()
    {
        return function ($attribute, $value, $fail) {
            if (now()->diffInMinutes($this->startsAt()) < $this->event->min_minutes_before_start) {
                $fail("The minimum minutes before booking can be done is {$this->event->min_minutes_before_start}");
            }
        };
    }
    
    public function mustNotBePublicHoliday()
    {
        return function ($attribute, $value, $fail) {
            if (Holiday::isHoliDay($this->startsAt())) {
                $fail('The selected timeslot is a Holiday');
            }
        };
    }

    public function mustBeValidTimeslot()
    {
        return function ($attribute, $value, $fail) {
            $timeslot = new TimeslotGenerator($this->event, $this->startsAt());

            if (!$timeslot->getSlots()->contains(function($timeslot) {
                return Carbon::parse($timeslot)->eq($this->startsAt());
            })) {
                $fail('The selected timeslot is not a valid timeslot.');
            }
        };
    }
    
    
    public function validateMaxBookings()
    {
        return function ($attribute, $value, $fail) {
            $maxBookingCount = $this->event
                                    ->timeslots()
                                    ->where('starts_at', $this->startsAt())
                                    ->join('bookings', 'timeslots.id', '=', 'bookings.timeslot_id')
                                    ->count();

            if($maxBookingCount >= $this->event->max_bookings_per_slot) {
                $fail('Max bookings reached for this event.');
            };
        };
    }

    public function validateAdvanceBooking()
    {
        return function ($attribute, $value, $fail) {
            $dateTillBookingAllowed = now()->addDays($this->event->advance_booking_days);

            if($this->startsAt() > $dateTillBookingAllowed) {
                $fail('Booking is not allowed yet.');
            };
        };
    }

    public function startsAt()
    {
        return Carbon::parse($this->starts_at);
    }
}
