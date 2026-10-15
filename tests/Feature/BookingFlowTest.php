<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('role', 'customer')->first();
    }

    public function test_can_view_home_page(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('SnapSpace');
    }

    public function test_can_view_packages_page(): void
    {
        $response = $this->get(route('packages.index'));
        $response->assertStatus(200);
        $response->assertSee('Basic Snap');
        $response->assertSee('Standard Studio');
    }

    public function test_can_view_booking_wizard_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('booking.wizard'));
        $response->assertStatus(200);
    }

    public function test_can_get_available_slots_api(): void
    {
        $tomorrow = now()->addDay()->format('Y-m-d');
        $room = Room::first();
        $package = Package::first();

        $response = $this->getJson('/api/slots?room_id='.$room->id.'&date='.$tomorrow.'&package_id='.$package->id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['slots']);
    }

    public function test_can_create_booking(): void
    {
        $package = Package::first();
        $room = Room::first();
        $tomorrow = now()->addDay()->format('Y-m-d');

        $bookingData = [
            'room_id' => $room->id,
            'package_id' => $package->id,
            'booking_date' => $tomorrow,
            'start_time' => '14:00',
            'people_count' => 2,
            'payment_option' => 'full',
            'notes' => 'Sesi foto wisuda',
        ];

        $response = $this->actingAs($this->user)->post(route('booking.store'), $bookingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'room_id' => $room->id,
            'package_id' => $package->id,
        ]);
    }

    public function test_can_check_booking_status(): void
    {
        $booking = Booking::first();

        $response = $this->actingAs($this->user)->get(route('my-bookings.show', $booking->booking_code));

        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
    }
}
