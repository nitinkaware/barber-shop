<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_time_should_be_a_valid_start_time()
    {
        $this->seed();

        // Trying to book with invalid time format
        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => 'DFGHJGVHG'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.starts_at.0', 'The starts at is not a valid date.');
        
        
        // Trying to book at lunch time
        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 12:00:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.starts_at.0', 'The selected timeslot is not a valid timeslot.');

        // Trying to book incorrect slot
        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 08:02:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.starts_at.0', 'The selected timeslot is not a valid timeslot.');

        // Trying to book in the past
        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => now()->subDays(3)->format('Y-m-d H:i:s')
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.starts_at.0', 'The starts at must be a date after now.');
        
        // Trying to book incorrect slot
        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => now()->addDay(3)->format('Y-m-d H:i:s')
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.starts_at.0', 'The selected timeslot is a Holiday.');
    }

    public function test_booking_should_be_created_successfully()
    {
        $this->seed();

        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 08:00:00',
            'email_address' => 'nitin@gmail.com',
            'first_name' => 'Nitin',
            'last_name' => 'Kaware',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Booking created successfully.');
    }

    public function test_booking_should_not_be_created_if_email_already_exists()
    {
        $this->seed();

        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 08:00:00',
            'email_address' => 'nitin@gmail.com',
            'first_name' => 'Nitin',
            'last_name' => 'Kaware',
        ]);
        
        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 08:00:00',
            'email_address' => 'nitin@gmail.com',
            'first_name' => 'Nitin',
            'last_name' => 'Kaware',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email_address.0', 'This email address already exits for this event.');
    }
    
    public function test_email_address_should_be_a_valid_email_address()
    {
        $this->seed();

        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 08:00:00',
            'email_address' => 'nitin',
            'first_name' => 'Nitin',
            'last_name' => 'Kaware',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email_address.0', 'The email address must be a valid email address.');
    }

    public function test_a_booking_at_end_of_slot_should_not_success()
    {
        $this->seed();

        $response = $this->postJson('/api/events/1/bookings', [
            'starts_at' => '2022-06-21 20:00:00',
            'email_address' => 'nitin@gmail.com',
            'first_name' => 'Nitin',
            'last_name' => 'Kaware',
        ]);

        $response->assertStatus(422);
    }
}
