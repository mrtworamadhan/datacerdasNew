<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null)
    {
        // Periksa apakah pengguna saat ini adalah instance dari MustVerifyEmail
        // dan apakah email mereka belum diverifikasi.
        // Tambahan: Periksa juga role pengguna. Hanya 'admin_desa' yang diwajibkan verifikasi email.
        // Role lain (admin_rw, admin_rt, kader_posyandu) tidak perlu verifikasi email.
        if (! $request->user() ||
            (
                $request->user() instanceof MustVerifyEmail &&
                ! $request->user()->hasVerifiedEmail() &&
                $request->user()->user_type === 'admin_desa' // Hanya admin_desa yang harus verifikasi
            )
        ) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}

