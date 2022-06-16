<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventBookingRequest;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Timeslot;
use App\Utils\Slot;
use Illuminate\Http\Request;

class EventBookingController extends Controller
{
    public function store(Event $event, EventBookingRequest $request)
    {
        $event->createBooking($request->validated(), $request->startsAt());

        return response()->json([
            'message' => 'Booking created successfully'
        ], 201);
    }
}
