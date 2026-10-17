<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production or behind secure reverse proxy
        if (app()->environment('production') || request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // 1. Rate Limiter: Login (Anti-Brute Force: 5 percobaan per menit per kombinasi email & IP)
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            $throttleKey = Str::transliterate(Str::lower($email).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey)->response(function (Request $request, array $headers) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak percobaan login. Silakan coba kembali dalam 1 menit.',
                ], 429, $headers);
            });
        });

        // 2. Rate Limiter: Register & OTP (Anti-Spam Akun: 3 registrasi per menit per IP)
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(function (Request $request, array $headers) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak percobaan pendaftaran. Silakan tunggu sebentar.',
                ], 429, $headers);
            });
        });

        // 3. Rate Limiter: Promo Code & Voucher (Anti-Brute Force Kupon: 10 percobaan per menit)
        RateLimiter::for('promo', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(10)->by($identifier)->response(function (Request $request, array $headers) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak percobaan kode promo. Silakan coba lagi beberapa saat lagi.',
                ], 429, $headers);
            });
        });

        // 4. Rate Limiter: General API (60 per menit per IP / user)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
