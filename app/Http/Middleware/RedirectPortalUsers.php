<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectPortalUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Gunakan hasAnyRole dari Spatie
            if ($user->hasAnyRole(['kepala_desa', 'admin_rt', 'admin_rw', 'kader_posyandu'])) {
                
                // Cek apakah mereka mencoba mengakses halaman DI LUAR portal
                if (!$request->is('portal/*') && !$request->routeIs('logout') && !$request->routeIs('profile.edit')) {
                    
                    if ($user->desa && $user->desa->subdomain) {
                        return redirect()->route('portal.dashboard', ['subdomain' => $user->desa->subdomain]);
                    }
                }
            }
        }

        return $next($request);
    }
}
