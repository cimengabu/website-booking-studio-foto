<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\PhotoResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SecurityAndComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->customer = User::where('role', 'customer')->first();
        $this->owner = User::where('role', 'admin')->first();
    }

    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    public function test_rbac_restricts_customer_from_accessing_admin_endpoints(): void
    {
        // Customer should receive 403 when trying to access admin dashboard
        $response = $this->actingAs($this->customer)->get('/admin');
        $response->assertStatus(403);

        // Customer should receive 403 on admin settings
        $response = $this->actingAs($this->customer)->get('/admin/settings');
        $response->assertStatus(403);

        // Customer should receive 403 on user management
        $response = $this->actingAs($this->customer)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_owner_can_access_admin_endpoints(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_signed_url_allows_valid_and_rejects_invalid_photo_download(): void
    {
        $booking = Booking::first();
        $photo = PhotoResult::create([
            'booking_id' => $booking->id,
            'path' => 'photos/test-secure.jpg',
            'original_name' => 'test-secure.jpg',
            'size' => 1024,
        ]);

        // 1. Valid signed URL should attempt download (or fail with 404 if physical file doesn't exist, but pass signature check)
        $validSignedUrl = URL::temporarySignedRoute(
            'photos.signed-download',
            Carbon::now()->addHours(1),
            ['photoId' => $photo->id]
        );

        $response = $this->get($validSignedUrl);
        // Signature is valid (doesn't return 401 or 403)
        $this->assertNotEquals(401, $response->status());

        // 2. Tampered URL must return 401 Unauthorized / Invalid Signature
        $tamperedUrl = $validSignedUrl.'&tampered=1';
        $tamperedResponse = $this->get($tamperedUrl);
        $tamperedResponse->assertStatus(401);
    }

    public function test_pdp_personal_data_export(): void
    {
        $response = $this->actingAs($this->customer)->get('/dashboard/profil/export-data');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_pdp_account_deletion_with_confirmation(): void
    {
        $testUser = User::factory()->create([
            'role' => 'customer',
            'email' => 'pdp-delete-test@example.com',
        ]);

        $response = $this->actingAs($testUser)->delete('/dashboard/profil', [
            'confirm_delete' => 'HAPUS',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $testUser->id]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'pdp_account_erasure',
        ]);
    }

    public function test_database_backup_artisan_command(): void
    {
        $this->artisan('app:backup')
            ->assertExitCode(0);

        $backupDir = storage_path('app/backups');
        $this->assertTrue(File::exists($backupDir));
        $this->assertNotEmpty(File::files($backupDir));
    }
}
