<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_events_should_be_listed_successfully()
    {
        $this->seed();
        
        $response = $this->getJson('/api/events');

        $response->assertStatus(200);

        Event::all()->each(function ($event, $index) use ($response) {
            $response->assertJsonPath("events.{$index}.title", $event->title);
        });
    }

    public function test_validate_quantity_left()
    {
        $this->seed();
        
        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 08:00:00',
            'email_address' => 'nitin@gmail.com',
            'first_name' => 'Nitin',
            'last_name' => 'Kaware',
        ]);

        $response = $this->getJson('/api/events');

        $response->assertStatus(200);

        Event::with('timeslots.bookings')->get()->each(function ($event, $index) use ($response) {
            $event->timeslots->map(function ($timeslot, $timeslotIndex) use ($index, $response, $event) {
                $response->assertJsonPath(
                    "events.{$index}.timeslots.{$timeslotIndex}.availableQuantityLeft", 
                    $event->max_bookings_per_slot - $timeslot->total_confirmed_bookings
                );
            }); 
        });
    }
}
