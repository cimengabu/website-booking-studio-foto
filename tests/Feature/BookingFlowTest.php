<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_home_page(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('KILAT');
    }

    public function test_can_view_packages_page(): void
    {
        $response = $this->get(route('packages'));
        $response->assertStatus(200);
        $response->assertSee('Quick Snap');
        $response->assertSee('Classic');
    }

    public function test_can_view_booking_wizard_page(): void
    {
        $response = $this->get(route('booking.wizard'));
        $response->assertStatus(200);
    }

    public function test_can_get_available_slots_api(): void
    {
        $tomorrow = now()->addDay()->format('Y-m-d');

        $response = $this->getJson('/api/slots?date='.$tomorrow.'&package_id='.Package::first()->id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['slots']);
    }

    public function test_can_create_booking(): void
    {
        $package = Package::where('slug', 'classic')->first();
        $tomorrow = now()->addDay()->format('Y-m-d');

        $bookingData = [
            'package_id' => $package->id,
            'booking_date' => $tomorrow,
            'start_time' => '14:00',
            'customer_name' => 'Budi Pratama',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'notes' => 'Sesi foto wisuda',
            'addons' => [],
            'promo_code' => 'SELFIE10',
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'booking_code',
            'redirect_url',
        ]);

        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Budi Pratama',
            'customer_phone' => '081234567890',
        ]);
    }

    public function test_can_check_booking_status(): void
    {
        $package = Package::first();
        $booking = Booking::create([
            'booking_code' => 'KLT-20261005-TEST',
            'package_id' => $package->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '15:00',
            'end_time' => '15:30',
            'customer_name' => 'Siti Rahma',
            'customer_email' => 'siti@example.com',
            'customer_phone' => '081987654321',
            'subtotal' => 65000,
            'total_price' => 65000,
            'status' => 'paid',
            'expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->get(route('check.booking', [
            'code' => $booking->booking_code,
            'phone' => '081987654321',
        ]));

        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
        $response->assertSee('Siti Rahma');
    }
}
