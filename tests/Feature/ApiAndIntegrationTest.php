<?php

namespace Tests\Feature;

use App\Jobs\CleanupExpiredDataJob;
use App\Jobs\ExpireBookingJob;
use App\Jobs\MarkNoShowJob;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAndIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->customer = User::where('role', 'customer')->first();
    }

    public function test_packages_and_rooms_api(): void
    {
        $response = $this->getJson('/api/packages');
        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);

        $response = $this->getJson('/api/rooms');
        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);
    }

    public function test_slots_and_calculate_price_api(): void
    {
        $room = Room::first();
        $package = Package::first();
        $tomorrow = Carbon::now()->addDays(2)->toDateString();

        $slotsResponse = $this->getJson("/api/slots?room_id={$room->id}&package_id={$package->id}&date={$tomorrow}");
        $slotsResponse->assertStatus(200);

        $priceResponse = $this->postJson('/api/calculate-price', [
            'package_id' => $package->id,
            'date' => $tomorrow,
            'start_time' => '10:00',
            'people_count' => 2,
        ]);
        $priceResponse->assertStatus(200);
    }

    public function test_create_and_fetch_booking_api(): void
    {
        $room = Room::first();
        $package = Package::first();
        $targetDate = Carbon::now()->addDays(3)->toDateString();

        $response = $this->actingAs($this->customer)->postJson('/api/bookings', [
            'room_id' => $room->id,
            'package_id' => $package->id,
            'booking_date' => $targetDate,
            'start_time' => '11:00',
            'people_count' => 2,
            'payment_option' => 'dp',
            'notes' => 'API Booking Test',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['booking_code', 'total_price', 'status'],
            ]);

        $bookingCode = $response->json('data.booking_code');

        $fetchResponse = $this->getJson("/api/bookings/{$bookingCode}");
        $fetchResponse->assertStatus(200)
            ->assertJsonPath('data.booking_code', $bookingCode);
    }

    public function test_voucher_validation_api(): void
    {
        $promo = Promo::create([
            'code' => 'TESTDISKON20',
            'type' => 'percent',
            'value' => 20,
            'quota' => 50,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/vouchers/validate', [
            'code' => 'TESTDISKON20',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.code', 'TESTDISKON20');
    }

    public function test_midtrans_webhook_signature_and_idempotency(): void
    {
        $serverKey = 'test-server-key-xyz';
        config(['services.midtrans.server_key' => $serverKey]);

        $booking = Booking::first();
        $orderId = "SNAP-{$booking->booking_code}-TEST";
        $grossAmount = '150000.00';
        $statusCode = '200';

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'order_id' => $orderId,
            'payment_kind' => 'full',
            'payment_type' => 'bank_transfer',
            'gross_amount' => 150000,
            'transaction_status' => 'pending',
            'snap_token' => 'MOCK-TOKEN',
        ]);

        $validSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ];

        // 1. Process payment webhook
        $response = $this->postJson('/api/payment/webhook', $payload);
        $response->assertStatus(200)
            ->assertJsonPath('status', 'ok');

        $this->assertEquals('settlement', $payment->fresh()->transaction_status);

        // 2. Idempotency check: sending same payload again should be accepted without re-processing
        $idempotentResponse = $this->postJson('/api/payment/webhook', $payload);
        $idempotentResponse->assertStatus(200)
            ->assertJsonPath('status', 'ok');
    }

    public function test_expire_booking_job(): void
    {
        $booking = Booking::first();
        $booking->update([
            'status' => 'pending',
            'expires_at' => Carbon::now()->subMinutes(10),
        ]);

        $job = new ExpireBookingJob;
        $job->handle();

        $this->assertEquals('expired', $booking->fresh()->status);
    }

    public function test_mark_no_show_job(): void
    {
        $booking = Booking::first();
        $booking->update([
            'status' => 'confirmed',
            'booking_date' => Carbon::now()->subDays(1)->toDateString(),
            'end_time' => '12:00:00',
            'checked_in_at' => null,
        ]);

        $job = new MarkNoShowJob;
        $job->handle();

        $this->assertEquals('no_show', $booking->fresh()->status);
    }

    public function test_cleanup_expired_data_job(): void
    {
        $booking = Booking::first();
        $booking->update(['status' => 'expired']);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'order_id' => 'SNAP-EXPIRE-TEST',
            'payment_kind' => 'full',
            'gross_amount' => 100000,
            'transaction_status' => 'pending',
        ]);
        $payment->timestamps = false;
        $payment->created_at = Carbon::now()->subHours(3);
        $payment->save();

        $job = new CleanupExpiredDataJob;
        $job->handle();

        $this->assertEquals('expire', $payment->fresh()->transaction_status);
    }
}
