<?php

use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\RestApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SnapSpace Studio
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| All routes in this file are automatically prefixed with "/api".
|
*/

// 1. Auth Endpoints
Route::prefix('auth')->group(function () {
    Route::post('/login', [RestApiController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [RestApiController::class, 'register'])->name('api.auth.register');
    Route::middleware('auth:sanctum')->get('/me', [RestApiController::class, 'me'])->name('api.auth.me');
});

// 2. Master Data (Paket, Ruangan, Add-on)
Route::get('/packages', [RestApiController::class, 'getPackages'])->name('api.packages');
Route::get('/rooms', [RestApiController::class, 'getRooms'])->name('api.rooms');
Route::get('/rooms/{id}', [RestApiController::class, 'getRoomDetail'])->name('api.rooms.show');
Route::get('/addons', [RestApiController::class, 'getAddons'])->name('api.addons');

// 3. Ketersediaan Slot & Kalkulasi Harga
Route::get('/slots', [RestApiController::class, 'getSlots'])->name('api.slots.index');
Route::post('/calculate-price', [RestApiController::class, 'calculatePrice'])->name('api.calculate-price.index');

// 4. Booking CRUD & Lock Slot
Route::post('/bookings', [RestApiController::class, 'createBooking'])->name('api.bookings.create');
Route::get('/bookings/{code}', [RestApiController::class, 'getBooking'])->name('api.bookings.show');

// 5. Pembayaran & Midtrans Webhook
Route::post('/bookings/{code}/payment', [RestApiController::class, 'createPaymentTransaction'])->name('api.bookings.payment');
Route::post('/payment/webhook', [PaymentWebhookController::class, 'handle'])->name('api.payment.webhook');

// 6. Voucher / Promo Validation
Route::post('/vouchers/validate', [RestApiController::class, 'validateVoucher'])->name('api.vouchers.validate');

// 7. Galeri Portofolio & Showcase
Route::get('/galleries', [RestApiController::class, 'getGalleries'])->name('api.galleries');

// 8. Ulasan Publik
Route::get('/reviews', [RestApiController::class, 'getReviews'])->name('api.reviews');

// 9. Laporan Ringkasan
Route::get('/reports/summary', [RestApiController::class, 'getReportSummary'])->name('api.reports.summary');
