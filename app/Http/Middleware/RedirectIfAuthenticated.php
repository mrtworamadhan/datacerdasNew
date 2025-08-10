<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    // Di dalam RedirectIfAuthenticated.php
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                // Cek role dan arahkan ke dashboard yang benar
                if ($user->isSuperAdmin()) {
                    return redirect(route('superadmin.dashboard'));
                } else if ($user->desa && $user->desa->subdomain) {
                    return redirect()->route('tenant.dashboard', ['subdomain' => $user->desa->subdomain]);
                }
            }
        }
        return $next($request);
    }
}