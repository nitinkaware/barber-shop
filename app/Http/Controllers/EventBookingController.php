<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventBookingRequest;
use App\Models\Booking;
use App\Models\Event;
use App\Utils\Slot;
use Illuminate\Http\Request;

class EventBookingController extends Controller
{
    public function store(Event $event, EventBookingRequest $request)
    {
        $event->bookings()->create($request->validated() + [
            'ends_at' => $request->startsAt()->addMinutes($event->event_duration_minutes)
        ]);

        return response()->json([
            'message' => 'Booking created successfully'
        ], 201);
    }
}
