<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectPortalUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah ada user yang login
        if (Auth::check()) {
            $user = Auth::user();

            // 2. Cek apakah user adalah salah satu dari peran portal
            if (in_array($user->user_type, ['admin_rt', 'admin_rw', 'kader_posyandu'])) {
                
                // 3. Cek apakah mereka mencoba mengakses halaman DI LUAR portal
                //    dan bukan sedang dalam proses logout.
                if (!$request->is('portal/*') && !$request->routeIs('logout')) {
                    
                    // 4. Jika ya, paksa redirect ke portal dashboard mereka
                    if ($user->desa && $user->desa->subdomain) {
                        return redirect()->route('portal.dashboard', ['subdomain' => $user->desa->subdomain]);
                    }
                }
            }
        }

        // Jika tidak ada kondisi yang cocok, biarkan request berjalan seperti biasa
        return $next($request);
    }
}