<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = [
            [
                'title' => 'Men Haircut',
                'event_duration_minutes' => '10',
                'cleaning_duration_minutes' => '5',
                'advance_booking_days' => '7',
                'max_bookings_per_slot' => '3',   
            ],
            [
                'title' => 'Women Haircut',
                'event_duration_minutes' => '30',
                'cleaning_duration_minutes' => '10',
                'advance_booking_days' => '7',
                'max_bookings_per_slot' => '3',   
            ],
            [
                'title' => 'Women Hair Color',
                'event_duration_minutes' => '60',
                'cleaning_duration_minutes' => '10',
                'advance_booking_days' => '7',
                'max_bookings_per_slot' => '3',   
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
