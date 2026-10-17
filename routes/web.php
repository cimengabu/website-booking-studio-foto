<?php

use App\Http\Controllers\Admin\AdminAddonController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminCalendarController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminPhotoController;
use App\Http\Controllers\Admin\AdminPortfolioController;
use App\Http\Controllers\Admin\AdminPromoController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\AboutController;
use App\Http\Controllers\Customer\BookingWizardController;
use App\Http\Controllers\Customer\ContactController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\FaqController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\MyBookingController;
use App\Http\Controllers\Customer\PackageController;
use App\Http\Controllers\Customer\PortfolioController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\RoomController;
use App\Http\Controllers\Staff\CheckinController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{slug}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');

// New Public Informational Pages
Route::get('/tentang-kami', [AboutController::class, 'index'])->name('about');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'send'])->name('contact.send');

// Static info pages
Route::view('/syarat-ketentuan', 'customer.terms')->name('terms');
Route::view('/kebijakan-privasi', 'customer.privacy')->name('privacy');
Route::view('/kebijakan-reschedule', 'customer.reschedule-policy')->name('reschedule.policy');

// Guest booking status check
Route::get('/cek-booking', function () {
    return view('customer.check-booking');
})->name('booking.check');
Route::post('/api/check-booking', [BookingWizardController::class, 'checkBookingApi'])->name('api.check-booking');

// Public API endpoints for Booking Wizard
Route::get('/api/slots', [BookingWizardController::class, 'getSlotsApi'])->name('api.slots');
Route::post('/api/calculate-price', [BookingWizardController::class, 'calculatePriceApi'])->name('api.calculate-price');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Customer Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/galeri', [CustomerDashboardController::class, 'gallery'])->name('dashboard.gallery');
    Route::get('/dashboard/pembayaran', [CustomerDashboardController::class, 'payments'])->name('dashboard.payments');

    // Profile & Settings
    Route::get('/dashboard/profil', [ProfileController::class, 'index'])->name('dashboard.profile');
    Route::patch('/dashboard/profil', [ProfileController::class, 'update'])->name('dashboard.profile.update');
    Route::patch('/dashboard/profil/password', [ProfileController::class, 'updatePassword'])->name('dashboard.profile.password');
    Route::patch('/dashboard/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('dashboard.profile.avatar');
    Route::delete('/dashboard/profil', [ProfileController::class, 'deleteAccount'])->name('dashboard.profile.delete');

    Route::get('/booking', [BookingWizardController::class, 'showWizard'])->name('booking.wizard');
    Route::post('/booking', [BookingWizardController::class, 'storeBooking'])->name('booking.store');
    Route::get('/booking/{code}/pay', [BookingWizardController::class, 'showPaymentPage'])->name('booking.pay');
    Route::post('/booking/{code}/pay-simulated', [BookingWizardController::class, 'processSimulatedPayment'])->name('booking.pay-simulated');

    Route::get('/my-bookings', [MyBookingController::class, 'index'])->name('my-bookings.index');
    Route::get('/my-bookings/{code}', [MyBookingController::class, 'show'])->name('my-bookings.show');
    Route::post('/my-bookings/{code}/reschedule', [MyBookingController::class, 'reschedule'])->name('my-bookings.reschedule');
    Route::post('/my-bookings/{code}/cancel', [MyBookingController::class, 'cancel'])->name('my-bookings.cancel');
    Route::post('/my-bookings/{code}/review', [MyBookingController::class, 'storeReview'])->name('my-bookings.review');

    Route::get('/photos/download/{photoId}', [MyBookingController::class, 'downloadPhoto'])->name('photos.download');
    Route::get('/photos/zip/{code}', [MyBookingController::class, 'downloadZip'])->name('photos.zip');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/toggle/{roomId}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

// Staff Protected Routes
Route::middleware(['auth', RoleMiddleware::class.':staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/scan', [CheckinController::class, 'showScan'])->name('scan');
    Route::post('/checkin', [CheckinController::class, 'processCheckin'])->name('checkin');
    Route::get('/today', [CheckinController::class, 'todaySchedule'])->name('today');
    Route::post('/completed/{id}', [CheckinController::class, 'markCompleted'])->name('completed');
});

// Admin Protected Routes (RBAC)
Route::middleware(['auth', RoleMiddleware::class.':admin,owner,staff,kasir,photographer,fotografer'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Global APIs (Search & Notifications for Topbar)
    Route::get('/api/search', [DashboardController::class, 'searchApi'])->name('api.search');
    Route::get('/api/notifications', [DashboardController::class, 'notificationsApi'])->name('api.notifications');

    // Booking Management (Owner, Staff, Photographer)
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/bulk-status', [AdminBookingController::class, 'bulkUpdateStatus'])->name('bookings.bulk-status');
    Route::get('/bookings/walkin', [AdminBookingController::class, 'createWalkin'])->name('bookings.walkin');
    Route::post('/bookings/walkin', [AdminBookingController::class, 'storeWalkin'])->name('bookings.walkin.store');
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::post('/bookings/{id}/pay-cash', [AdminBookingController::class, 'recordCashPayment'])->name('bookings.pay-cash');
    Route::patch('/bookings/{id}/assign-photographer', [AdminBookingController::class, 'assignPhotographer'])->name('bookings.assign-photographer');
    Route::post('/bookings/{id}/notes', [AdminBookingController::class, 'saveInternalNotes'])->name('bookings.notes');
    Route::post('/bookings/{id}/resend-notification', [AdminBookingController::class, 'resendNotification'])->name('bookings.resend-notification');
    Route::post('/bookings/{id}/refund', [AdminBookingController::class, 'processRefund'])->name('bookings.refund');

    // Calendar
    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [AdminCalendarController::class, 'eventsApi'])->name('calendar.events');
    Route::post('/calendar/reschedule/{id}', [AdminCalendarController::class, 'rescheduleApi'])->name('calendar.reschedule');

    // Photos Results Upload & Management (Owner, Staff, Photographer)
    Route::get('/photos', [AdminPhotoController::class, 'index'])->name('photos.index');
    Route::get('/photos/upload/{bookingId}', [AdminPhotoController::class, 'showUpload'])->name('photos.upload');
    Route::post('/photos/upload/{bookingId}', [AdminPhotoController::class, 'storeUpload'])->name('photos.store');
    Route::delete('/photos/{photoId}', [AdminPhotoController::class, 'destroyPhoto'])->name('photos.destroy');

    // Customers (Owner & Staff)
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');

    // Reviews Moderation (Owner & Staff)
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{id}/toggle', [AdminReviewController::class, 'toggleVisibility'])->name('reviews.toggle');
    Route::post('/reviews/{id}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');

    // ── OWNER EXCLUSIVE MODULES (Master Data, Keuangan, Settings, RBAC Team) ──
    Route::middleware([RoleMiddleware::class.':admin,owner'])->group(function () {
        // Team Management & RBAC Roles
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{id}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Master Data: Rooms, Packages, Addons, Portfolio
        Route::resource('rooms', AdminRoomController::class);
        Route::post('/blocked-slots', [AdminRoomController::class, 'storeBlockedSlot'])->name('blocked-slots.store');
        Route::delete('/blocked-slots/{id}', [AdminRoomController::class, 'destroyBlockedSlot'])->name('blocked-slots.destroy');
        Route::resource('packages', AdminPackageController::class)->except(['create', 'edit', 'show']);
        Route::resource('addons', AdminAddonController::class)->except(['create', 'edit', 'show']);

        Route::get('/portfolios', [AdminPortfolioController::class, 'index'])->name('portfolios.index');
        Route::post('/portfolios', [AdminPortfolioController::class, 'store'])->name('portfolios.store');
        Route::delete('/portfolios/{id}', [AdminPortfolioController::class, 'destroy'])->name('portfolios.destroy');

        // Marketing Promos
        Route::resource('promos', AdminPromoController::class)->except(['create', 'edit', 'show']);

        // Financial Reports & Export
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');

        // System Settings & Activity Logs
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');
    });
});
