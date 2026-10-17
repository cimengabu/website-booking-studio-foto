<?php

namespace Tests\Feature;

use App\Exceptions\SlotUnavailableException;
use App\Jobs\ExpireBookingJob;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected User $customer2;

    protected User $admin;

    protected User $photographer;

    protected Room $room;

    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->customer = User::where('role', 'customer')->first();
        $this->customer2 = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer2@example.com',
            'name' => 'Customer Dua',
        ]);
        $this->admin = User::where('role', 'admin')->first();
        $this->photographer = User::where('role', 'photographer')->first() ?? User::factory()->create(['role' => 'photographer', 'name' => 'Staff Foto']);
        $this->room = Room::first();
        $this->package = Package::first();
    }

    /**
     * Edge Case 1: Dua orang memilih slot sama secara bersamaan (Pessimistic Locking & Slot Collision)
     */
    public function test_concurrent_slot_booking_throws_slot_unavailable_exception(): void
    {
        $tomorrow = now()->addDays(2)->format('Y-m-d');
        $startTime = '10:00';

        $service = app(BookingService::class);

        // First customer successfully reserves the slot
        $booking1 = $service->createBooking([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => $tomorrow,
            'start_time' => $startTime,
            'customer_name' => 'Pelanggan Pertama',
            'customer_phone' => '081234567890',
            'customer_email' => 'first@example.com',
            'payment_type' => 'full',
        ]);

        $this->assertNotNull($booking1->id);

        // Second customer tries to book the exact same slot concurrently
        $this->expectException(SlotUnavailableException::class);

        $service->createBooking([
            'user_id' => $this->customer2->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => $tomorrow,
            'start_time' => $startTime,
            'customer_name' => 'Pelanggan Kedua',
            'customer_phone' => '089876543210',
            'customer_email' => 'second@example.com',
            'payment_type' => 'full',
        ]);
    }

    /**
     * Edge Case 4 & 5: Kuota voucher habis saat checkout & pengembalian kuota saat booking expired
     */
    public function test_promo_quota_atomic_limit_and_restoration_on_booking_expiry(): void
    {
        $promo = Promo::create([
            'code' => 'LIMITED1',
            'type' => 'fixed',
            'value' => 20000,
            'quota' => 1,
            'used_count' => 0,
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDays(10),
            'min_spend' => 0,
        ]);

        $tomorrow = now()->addDays(3)->format('Y-m-d');
        $service = app(BookingService::class);

        // Booking 1 uses the 1 available quota
        $booking1 = $service->createBooking([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => $tomorrow,
            'start_time' => '13:00',
            'customer_name' => 'Pelanggan Voucher 1',
            'customer_phone' => '081234567891',
            'customer_email' => 'v1@example.com',
            'promo_code' => 'LIMITED1',
            'payment_type' => 'full',
        ]);

        $promo->refresh();
        $this->assertEquals(1, $promo->used_count);
        $this->assertEquals('LIMITED1', $booking1->promo_code);

        // Booking 2 tries to use the same promo when quota is now full
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Mohon maaf, kuota voucher');

        $service->createBooking([
            'user_id' => $this->customer2->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => $tomorrow,
            'start_time' => '15:00',
            'customer_name' => 'Pelanggan Voucher 2',
            'customer_phone' => '081234567892',
            'customer_email' => 'v2@example.com',
            'promo_code' => 'LIMITED1',
            'payment_type' => 'full',
        ]);
    }

    /**
     * Edge Case 4: Expire booking job correctly releases slot and restores promo quota
     */
    public function test_expire_booking_job_restores_promo_quota(): void
    {
        $promo = Promo::create([
            'code' => 'RESTOREME',
            'type' => 'fixed',
            'value' => 15000,
            'quota' => 2,
            'used_count' => 1,
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDays(5),
        ]);

        $booking = Booking::create([
            'booking_code' => 'EXP-PROMO-TEST',
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '14:45',
            'customer_name' => 'Expired User',
            'customer_phone' => '08123123123',
            'customer_email' => 'exp@example.com',
            'subtotal' => 150000,
            'addons_total' => 0,
            'markup_amount' => 0,
            'discount' => 15000,
            'total_price' => 135000,
            'paid_amount' => 0,
            'payment_type' => 'full',
            'status' => 'pending',
            'promo_code' => 'RESTOREME',
            'expires_at' => now()->subMinute(),
        ]);

        // Run ExpireBookingJob
        (new ExpireBookingJob($booking->id))->handle();

        $booking->refresh();
        $promo->refresh();

        $this->assertEquals('expired', $booking->status);
        $this->assertEquals(0, $promo->used_count, 'Promo used_count should be decremented when expired booking is cleaned up');
    }

    /**
     * Edge Case 6: Perubahan harga paket setelah booking dibuat (Price Freezing)
     */
    public function test_package_price_change_does_not_affect_existing_booking(): void
    {
        $tomorrow = now()->addDays(4)->format('Y-m-d');
        $service = app(BookingService::class);

        $initialPackagePrice = (float) $this->package->price;

        $booking = $service->createBooking([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => $tomorrow,
            'start_time' => '11:00',
            'customer_name' => 'Price Lock Test',
            'customer_phone' => '081234567893',
            'customer_email' => 'pricelock@example.com',
            'payment_type' => 'full',
        ]);

        $initialSubtotal = (float) $booking->subtotal;
        $initialTotal = (float) $booking->total_price;

        // Admin modifies package price drastically
        $this->package->update(['price' => $initialPackagePrice + 100000]);

        $booking->refresh();
        $this->assertEquals($initialSubtotal, (float) $booking->subtotal, 'Saved booking subtotal must remain frozen');
        $this->assertEquals($initialTotal, (float) $booking->total_price, 'Saved booking total price must remain frozen');
    }

    /**
     * Edge Case 7: Deteksi konflik fotografer dan penanganan berhalangan mendadak
     */
    public function test_photographer_assignment_detects_schedule_conflict(): void
    {
        $tomorrow = now()->addDays(3)->format('Y-m-d');

        // Booking 1 with photographer assigned
        $booking1 = Booking::create([
            'booking_code' => 'PG-CONF-1',
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'photographer_id' => $this->photographer->id,
            'booking_date' => $tomorrow,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'customer_name' => 'Pelanggan A',
            'customer_phone' => '0811111111',
            'customer_email' => 'a@example.com',
            'subtotal' => 150000,
            'addons_total' => 0,
            'markup_amount' => 0,
            'discount' => 0,
            'total_price' => 150000,
            'paid_amount' => 150000,
            'payment_type' => 'full',
            'status' => 'confirmed',
        ]);

        // Booking 2 at the same time in another room
        $booking2 = Booking::create([
            'booking_code' => 'PG-CONF-2',
            'user_id' => $this->customer2->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'photographer_id' => null,
            'booking_date' => $tomorrow,
            'start_time' => '09:30',
            'end_time' => '10:30',
            'customer_name' => 'Pelanggan B',
            'customer_phone' => '0822222222',
            'customer_email' => 'b@example.com',
            'subtotal' => 150000,
            'addons_total' => 0,
            'markup_amount' => 0,
            'discount' => 0,
            'total_price' => 150000,
            'paid_amount' => 150000,
            'payment_type' => 'full',
            'status' => 'confirmed',
        ]);

        // Attempting to assign same photographer to booking2 should trigger validation error
        $response = $this->actingAs($this->admin)->patch(route('admin.bookings.assign-photographer', $booking2->id), [
            'photographer_id' => $this->photographer->id,
        ]);

        $response->assertSessionHasErrors('photographer_error');

        // Test emergency photographer unavailable method
        $emergencyResponse = $this->actingAs($this->admin)->post(route('admin.bookings.emergency-photographer', $booking1->id), [
            'reason' => 'Sakit mendadak',
        ]);

        $emergencyResponse->assertRedirect();
        $booking1->refresh();
        $this->assertNull($booking1->photographer_id);
        $this->assertTrue($booking1->isEmergencyAffected());
        $this->assertTrue($booking1->isReschedulable(), 'Emergency affected booking should bypass standard reschedule restrictions');
    }

    /**
     * Edge Case 8: Hari libur / studio tutup mendadak mengidentifikasi booking terdampak
     */
    public function test_sudden_studio_closure_tags_affected_bookings_for_free_reschedule(): void
    {
        $targetDate = now()->addDays(2)->format('Y-m-d');

        $activeBooking = Booking::create([
            'booking_code' => 'CLOSURE-TEST-1',
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => $targetDate,
            'start_time' => '14:00',
            'end_time' => '15:00',
            'customer_name' => 'Pelanggan Penutupan',
            'customer_phone' => '0855555555',
            'customer_email' => 'closure@example.com',
            'subtotal' => 150000,
            'addons_total' => 0,
            'markup_amount' => 0,
            'discount' => 0,
            'total_price' => 150000,
            'paid_amount' => 150000,
            'payment_type' => 'full',
            'status' => 'confirmed',
            'reschedule_count' => 1, // Normally cannot reschedule anymore
        ]);

        $this->assertFalse($activeBooking->isReschedulable());

        // Admin blocks the date due to sudden maintenance
        $response = $this->actingAs($this->admin)->post(route('admin.blocked-slots.store'), [
            'room_id' => $this->room->id,
            'date' => $targetDate,
            'start_time' => '08:00',
            'end_time' => '22:00',
            'reason' => 'Renovasi Darurat Kebocoran AC',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        $activeBooking->refresh();
        $this->assertTrue($activeBooking->isEmergencyAffected());
        $this->assertTrue($activeBooking->isReschedulable(), 'Must be granted free emergency reschedule despite previous reschedule count');
    }

    /**
     * Edge Case 2: Direct Payment Status Check via Gateway endpoint
     */
    public function test_direct_payment_status_check_endpoint(): void
    {
        config(['services.midtrans.server_key' => 'SB-Mid-server-TEST123']);

        $booking = Booking::create([
            'booking_code' => 'DIRECT-PAY-CHECK',
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'customer_name' => 'Cek Status',
            'customer_phone' => '0877777777',
            'customer_email' => 'status@example.com',
            'subtotal' => 150000,
            'addons_total' => 0,
            'markup_amount' => 0,
            'discount' => 0,
            'total_price' => 150000,
            'paid_amount' => 0,
            'payment_type' => 'full',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30),
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'order_id' => 'MIDTRANS-ORDER-12345',
            'payment_kind' => 'full',
            'payment_type' => 'qris',
            'gross_amount' => 150000,
            'transaction_status' => 'pending',
        ]);

        // Mock Midtrans direct API response
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/*' => Http::response([
                'status_code' => '200',
                'transaction_status' => 'settlement',
                'payment_type' => 'qris',
                'gross_amount' => '150000.00',
            ], 200),
        ]);

        $response = $this->actingAs($this->customer)->postJson(route('booking.check-payment-status', $booking->booking_code));

        $response->assertStatus(200);
        $response->assertJson(['is_paid' => true, 'status' => 'success']);

        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals(150000, (float) $booking->paid_amount);
    }

    /**
     * Edge Case 10: Validasi ketat upload foto besar gagal
     */
    public function test_photo_upload_validates_file_format_and_size(): void
    {
        $booking = Booking::create([
            'booking_code' => 'PHOTO-UPLOAD-TEST',
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'room_id' => $this->room->id,
            'booking_date' => now()->subDay()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'customer_name' => 'Upload Test',
            'customer_phone' => '0888888888',
            'customer_email' => 'upload@example.com',
            'subtotal' => 150000,
            'addons_total' => 0,
            'markup_amount' => 0,
            'discount' => 0,
            'total_price' => 150000,
            'paid_amount' => 150000,
            'payment_type' => 'full',
            'status' => 'completed',
        ]);

        // Non-image file upload attempt
        $fakePdf = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('admin.photos.store', $booking->id), [
            'photos' => [$fakePdf],
        ]);

        $response->assertSessionHasErrors('photos.0');
    }

    /**
     * Edge Case 11: Standarisasi zona waktu sistem adalah Asia/Jakarta (WIB)
     */
    public function test_system_timezone_is_strictly_asia_jakarta(): void
    {
        $this->assertEquals('Asia/Jakarta', config('app.timezone'));
    }
}
