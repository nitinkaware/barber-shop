<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventCollection;
use App\Http\Resources\HolidayCollection;
use App\Models\Event;
use App\Models\Holiday;

class EventsController extends Controller
{
    public function index()
    {
        return [
            'events' => new EventCollection(Event::with('timeslots.bookings')->get()),
            'holidays' => new HolidayCollection(Holiday::all()),
        ];
    }
}
