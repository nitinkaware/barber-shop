<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventBookingRequest;
use App\Models\Event;

class EventBookingController extends Controller
{
    public function store(Event $event, EventBookingRequest $request)
    {
        $event->createBooking($request->validated(), $request->startsAt());

        return response()->json([
            'message' => 'Booking created successfully.'
        ], 201);
    }
}
