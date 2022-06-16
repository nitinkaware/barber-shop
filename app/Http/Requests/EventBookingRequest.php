<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\Holiday;
use App\Utils\TimeSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

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
            'starts_at' => ['bail', 'required', 'date', 'after:now', $this->mustNotBePublicHoliday(), $this->validateAdvanceBooking(), $this->mustBeValidTimeSlot(), $this->validateMaxBookings()],
            'email_address' => ['required', 'email', Rule::unique('bookings')->where('event_id', $this->event->id)->where('starts_at', $this->startsAt())],
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

    public function mustNotBePublicHoliday()
    {
        return function ($attribute, $value, $fail) {
            if (Holiday::isHoliDay($this->startsAt())) {
                $fail('The selected timeslot is a Holiday');
            }
        };
    }

    public function mustBeValidTimeSlot()
    {
        return function ($attribute, $value, $fail) {
            $timeslot = new TimeSlot($this->event, $this->startsAt());

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
                                    ->bookings()
                                    ->where('starts_at', $this->startsAt())
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
