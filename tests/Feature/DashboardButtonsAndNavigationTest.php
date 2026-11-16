<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardButtonsAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected User $admin;

    protected User $staff;

    protected User $photographer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->customer = User::where('role', 'customer')->first() ?? User::factory()->create(['role' => 'customer']);
        $this->admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $this->staff = User::where('role', 'staff')->first() ?? User::factory()->create(['role' => 'staff']);
        $this->photographer = User::where('role', 'photographer')->first() ?? User::factory()->create(['role' => 'photographer']);
    }

    public function test_customer_can_access_all_dashboard_views(): void
    {
        // 1. Customer Main Dashboard
        $response = $this->actingAs($this->customer)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertSee('Ringkasan');

        // 2. My Bookings Index
        $response = $this->actingAs($this->customer)->get(route('my-bookings.index'));
        $response->assertStatus(200);
        $response->assertSee('Pesanan Saya');

        // 3. Customer Gallery
        $response = $this->actingAs($this->customer)->get(route('dashboard.gallery'));
        $response->assertStatus(200);

        // 4. Customer Payments History
        $response = $this->actingAs($this->customer)->get(route('dashboard.payments'));
        $response->assertStatus(200);

        // 5. Customer Profile
        $response = $this->actingAs($this->customer)->get(route('dashboard.profile'));
        $response->assertStatus(200);
        $response->assertSee('Profil & Pengaturan');

        // 6. Favorites
        $response = $this->actingAs($this->customer)->get(route('favorites.index'));
        $response->assertStatus(200);

        // 7. Booking Wizard
        $response = $this->actingAs($this->customer)->get(route('booking.wizard'));
        $response->assertStatus(200);
    }

    public function test_customer_can_view_booking_detail_and_buttons(): void
    {
        $room = Room::first();
        $package = Package::first();

        $booking = Booking::create([
            'booking_code' => 'SS-TEST-BTNS',
            'user_id' => $this->customer->id,
            'room_id' => $room->id,
            'package_id' => $package->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'people_count' => 2,
            'subtotal' => 150000,
            'addons_total' => 0,
            'total_price' => 150000,
            'paid_amount' => 150000,
            'status' => 'confirmed',
            'qr_token' => 'token-test-btns',
        ]);

        $response = $this->actingAs($this->customer)->get(route('my-bookings.show', $booking->booking_code));
        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
        $response->assertSee('Nota Resmi');

        // Verify invoice button target works
        $invoiceResponse = $this->actingAs($this->customer)->get(route('booking.invoice', $booking->booking_code));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee($booking->booking_code);
    }

    public function test_admin_can_access_all_management_modules(): void
    {
        // 1. Admin Dashboard
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // 2. Admin Bookings
        $response = $this->actingAs($this->admin)->get(route('admin.bookings.index'));
        $response->assertStatus(200);

        // 3. Walk-in Booking
        $response = $this->actingAs($this->admin)->get(route('admin.bookings.walkin'));
        $response->assertStatus(200);

        // 4. Calendar Studio
        $response = $this->actingAs($this->admin)->get(route('admin.calendar.index'));
        $response->assertStatus(200);

        // 5. Photos Management
        $response = $this->actingAs($this->admin)->get(route('admin.photos.index'));
        $response->assertStatus(200);

        // 6. Customers CRM
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index'));
        $response->assertStatus(200);

        // 7. Reviews Moderation
        $response = $this->actingAs($this->admin)->get(route('admin.reviews.index'));
        $response->assertStatus(200);

        // 8. Rooms Master
        $response = $this->actingAs($this->admin)->get(route('admin.rooms.index'));
        $response->assertStatus(200);

        // 9. Packages Master
        $response = $this->actingAs($this->admin)->get(route('admin.packages.index'));
        $response->assertStatus(200);

        // 10. Addons Master
        $response = $this->actingAs($this->admin)->get(route('admin.addons.index'));
        $response->assertStatus(200);

        // 11. Portfolios Master
        $response = $this->actingAs($this->admin)->get(route('admin.portfolios.index'));
        $response->assertStatus(200);

        // 12. Promos Master
        $response = $this->actingAs($this->admin)->get(route('admin.promos.index'));
        $response->assertStatus(200);

        // 13. Reports & Analytics
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);

        // 14. Settings
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);

        // 15. System Logs
        $response = $this->actingAs($this->admin)->get(route('admin.logs.index'));
        $response->assertStatus(200);

        // 16. Team & RBAC
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    public function test_staff_and_photographer_views_load_successfully(): void
    {
        // Staff check-in / QR scan
        $response = $this->actingAs($this->staff)->get(route('staff.scan'));
        $response->assertStatus(200);

        // Staff today schedule
        $response = $this->actingAs($this->staff)->get(route('staff.today'));
        $response->assertStatus(200);

        // Photographer Dashboard view
        $response = $this->actingAs($this->photographer)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Ruang kerja fotografer');
    }
}
