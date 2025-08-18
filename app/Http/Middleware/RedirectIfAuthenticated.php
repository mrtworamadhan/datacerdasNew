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

                // 1. Cek Super Admin menggunakan user_type
                if ($user->user_type === 'super_admin') {
                    return redirect(route('superadmin.dashboard'));
                }

                // 2. Cek role Portal (RT, RW, Kader) menggunakan user_type
                if (in_array($user->user_type, ['admin_rt', 'admin_rw', 'kader_posyandu'])) {
                    // Pastikan user punya desa dan subdomain
                    if ($user->desa && $user->desa->subdomain) {
                        // Ambil subdomain dari data user, BUKAN dari request
                        return redirect()->route('portal.dashboard', ['subdomain' => $user->desa->subdomain]);
                    }
                }
                
                // 3. Cek Admin Desa menggunakan user_type
                if ($user->user_type === 'admin_desa') {
                     if ($user->desa && $user->desa->subdomain) {
                        return redirect()->route('tenant.dashboard', ['subdomain' => $user->desa->subdomain]);
                     }
                }

                // 4. Fallback default jika tidak ada yang cocok
                // Jika user punya desa, arahkan ke dashboard tenant, jika tidak, ke root
                if ($user->desa && $user->desa->subdomain) {
                    return redirect()->route('tenant.dashboard', ['subdomain' => $user->desa->subdomain]);
                } else {
                    return redirect('/dashboard'); // Fallback paling akhir
                }
            }
        }

        return $next($request);
    }
}