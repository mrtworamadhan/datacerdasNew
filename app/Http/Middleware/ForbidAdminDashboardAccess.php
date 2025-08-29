<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForbidAdminDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Definisikan role yang seharusnya hanya bisa mengakses Portal
            $portalRoles = ['kepala_desa', 'admin_rt', 'admin_rw', 'kader_posyandu'];

            // Jika user memiliki salah satu dari role portal
            if ($user->hasAnyRole($portalRoles) && !$request->is('portal/*')) {

                // Usir mereka kembali ke dashboard portal.
                if ($user->desa && $user->desa->subdomain) {
                    return redirect()->route('portal.dashboard', ['subdomain' => $user->desa->subdomain]);
                }

                // Fallback jika ada masalah
                Auth::logout();
                return redirect('/login')->with('error', 'Akses tidak diizinkan.');
            }
        }

        return $next($request);
    }
}