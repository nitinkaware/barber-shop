<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventCollection;
use App\Models\Event;

class EventsController extends Controller
{
    public function index()
    {
        return [
            'events' => new EventCollection(Event::with('bookings')->get())
        ];
    }
}
