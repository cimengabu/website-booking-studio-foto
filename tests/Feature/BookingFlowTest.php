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

    public function test_can_view_official_invoice(): void
    {
        $booking = Booking::first();

        $response = $this->actingAs($booking->user)->get(route('booking.invoice', $booking->booking_code));

        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
        $response->assertSee('BUKTI PEMBAYARAN RESMI');
    }

    public function test_booking_rejects_people_count_exceeding_room_capacity(): void
    {
        $room = Room::where('capacity', '<=', 6)->first();
        $package = Package::where('max_people', '>=', 8)->first();

        $response = $this->actingAs($this->user)->post(route('booking.store'), [
            'package_id' => $package->id,
            'room_id' => $room->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'people_count' => 8, // Exceeds room capacity of 6
            'payment_option' => 'full',
        ]);

        $response->assertSessionHasErrors(['people_count']);
    }

    public function test_auth_preserves_booking_state_parameters(): void
    {
        // 1. Visit login with booking parameters
        $response = $this->get(route('login', ['package_id' => 2, 'room_id' => 1]));
        $response->assertStatus(200);
        $response->assertSessionHas('booking_package_id', 2);
        $response->assertSessionHas('booking_room_id', 1);

        // 2. Perform login
        $loginResponse = $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('booking.wizard', ['package_id' => 2, 'room_id' => 1]));
    }

    public function test_public_ticket_page_accessible_without_auth_and_shows_details(): void
    {
        $booking = Booking::first();

        // Accessible by guest without login
        $response = $this->get(route('ticket.show', $booking->booking_code));

        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
        $response->assertSee('SnapSpace Studio');
        $response->assertSee($booking->room->name);
    }

    public function test_staff_checkin_supports_full_url_token_and_dp_status(): void
    {
        $staff = User::where('role', 'staff')->first() ?? User::factory()->create(['role' => 'staff']);
        $booking = Booking::first();
        $booking->update(['status' => 'confirmed', 'checked_in_at' => null]);

        // 1. Scan using full URL format (as read by camera scanner)
        $scannedUrl = "https://snapspace.web.id/ticket/{$booking->booking_code}";

        $response = $this->actingAs($staff)->post(route('staff.checkin'), [
            'token' => $scannedUrl,
        ]);

        $response->assertRedirect(route('staff.today'));
        $this->assertEquals('in_progress', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->checked_in_at);
    }

    public function test_ticket_arrival_confirmation_endpoint(): void
    {
        $booking = Booking::where('status', 'confirmed')->first() ?? Booking::first();
        $booking->update(['status' => 'confirmed', 'checked_in_at' => null]);

        $response = $this->post(route('ticket.confirm', $booking->booking_code));

        $response->assertSessionHas('success');
        $this->assertEquals('in_progress', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->checked_in_at);
    }
}
