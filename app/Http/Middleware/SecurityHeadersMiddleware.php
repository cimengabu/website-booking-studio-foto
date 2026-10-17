<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach standard security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Clickjacking protection
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // MIME-sniffing prevention
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Cross-Site Scripting (XSS) filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer Information Leakage prevention
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict unnecessary browser features
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=()');

        // Content Security Policy (compatible with Alpine.js, Tailwind CDN, Google Fonts, and Midtrans Snap)
        $csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; "
            ."script-src 'self' 'unsafe-inline' 'unsafe-eval' https://app.sandbox.midtrans.com https://app.midtrans.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            ."style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
            ."font-src 'self' https://fonts.gstatic.com data:; "
            ."img-src 'self' data: https: blob:; "
            ."connect-src 'self' https://app.sandbox.midtrans.com https://app.midtrans.com https:; "
            ."frame-src 'self' https://app.sandbox.midtrans.com https://app.midtrans.com;";
        $response->headers->set('Content-Security-Policy', $csp);

        // HTTP Strict Transport Security (HSTS) when running under HTTPS or production
        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
