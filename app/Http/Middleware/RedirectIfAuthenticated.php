<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                if ($user->hasRole('superadmin')) {
                    return redirect(route('superadmin.dashboard'));
                }

                if ($user->hasAnyRole(['kepala_desa', 'admin_rt', 'admin_rw', 'kader_posyandu'])) {
                    if ($user->desa && $user->desa->subdomain) {
                        return redirect()->route('portal.dashboard', ['subdomain' => $user->desa->subdomain]);
                    }
                }
                
                $adminLteRoles = ['admin_desa', 'operator_desa', 'bendahara_desa', 'admin_pelayanan', 'admin_kesra', 'admin_umum'];
                if ($user->hasAnyRole($adminLteRoles)) {
                     if ($user->desa && $user->desa->subdomain) {
                        return redirect()->route('tenant.dashboard', ['subdomain' => $user->desa->subdomain]);
                     }
                }

                // Fallback paling akhir
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
