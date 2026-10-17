<?php

use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\RestApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SnapSpace Studio
|--------------------------------------------------------------------------
|
| All routes in this file are automatically prefixed with "/api".
| Protected by Rate Limiting, RBAC, and Security Headers.
|
*/

// 1. Auth Endpoints (Protected by Throttling)
Route::prefix('auth')->group(function () {
    Route::post('/login', [RestApiController::class, 'login'])->middleware('throttle:login')->name('api.auth.login');
    Route::post('/register', [RestApiController::class, 'register'])->middleware('throttle:register')->name('api.auth.register');
    Route::middleware('auth:sanctum')->get('/me', [RestApiController::class, 'me'])->name('api.auth.me');
});

// General Protected API Routes (Throttle: 60 req/min)
Route::middleware('throttle:api')->group(function () {
    // 2. Master Data (Paket, Ruangan, Add-on)
    Route::get('/packages', [RestApiController::class, 'getPackages'])->name('api.packages');
    Route::get('/rooms', [RestApiController::class, 'getRooms'])->name('api.rooms');
    Route::get('/rooms/{id}', [RestApiController::class, 'getRoomDetail'])->name('api.rooms.show');
    Route::get('/addons', [RestApiController::class, 'getAddons'])->name('api.addons');

    // 3. Ketersediaan Slot & Kalkulasi Harga
    Route::get('/slots', [RestApiController::class, 'getSlots'])->name('api.slots.index');
    Route::post('/calculate-price', [RestApiController::class, 'calculatePrice'])->middleware('throttle:promo')->name('api.calculate-price.index');

    // 4. Booking CRUD & Lock Slot
    Route::post('/bookings', [RestApiController::class, 'createBooking'])->name('api.bookings.create');
    Route::get('/bookings/{code}', [RestApiController::class, 'getBooking'])->name('api.bookings.show');

    // 5. Pembayaran
    Route::post('/bookings/{code}/payment', [RestApiController::class, 'createPaymentTransaction'])->name('api.bookings.payment');

    // 6. Voucher / Promo Validation (Anti-Brute Force Throttle)
    Route::post('/vouchers/validate', [RestApiController::class, 'validateVoucher'])->middleware('throttle:promo')->name('api.vouchers.validate');

    // 7. Galeri Portofolio & Showcase
    Route::get('/galleries', [RestApiController::class, 'getGalleries'])->name('api.galleries');

    // 8. Ulasan Publik
    Route::get('/reviews', [RestApiController::class, 'getReviews'])->name('api.reviews');

    // 9. Laporan Ringkasan
    Route::get('/reports/summary', [RestApiController::class, 'getReportSummary'])->name('api.reports.summary');
});

// Midtrans Webhook (Signature verified & exempt from CSRF)
Route::post('/payment/webhook', [PaymentWebhookController::class, 'handle'])->name('api.payment.webhook');
