<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Gunakan hasRole() dari Spatie untuk pengecekan
        if (Auth::check() && Auth::user()->hasRole('superadmin')) {
            return $next($request);
        }

        // Jika bukan super admin, tolak akses dengan error 403 (Forbidden)
        // Ini lebih baik daripada redirect, karena jelas menandakan masalah hak akses.
        abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES UNTUK HALAMAN INI.');
    }
}
