<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        // Owner/Admin has full access to everything
        if ($user->isOwner()) {
            return $next($request);
        }

        // Map role aliases
        $normalizedUserRoles = match ($user->role) {
            'owner', 'admin' => ['owner', 'admin', 'staff', 'kasir', 'photographer', 'fotografer'],
            'staff', 'kasir' => ['staff', 'kasir'],
            'photographer', 'fotografer' => ['photographer', 'fotografer'],
            default => [$user->role],
        };

        $hasAccess = false;
        foreach ($roles as $r) {
            if (in_array(trim($r), $normalizedUserRoles)) {
                $hasAccess = true;
                break;
            }
        }

        if (! $hasAccess) {
            abort(403, 'Akses Ditolak: Role Anda ('.$user->role_label.') tidak memiliki izin untuk modul ini.');
        }

        return $next($request);
    }
}
