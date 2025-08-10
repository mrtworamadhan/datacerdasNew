<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // Cek jika request datang dari subdomain
        $subdomain = explode('.', request()->getHost())[0];
        if ($subdomain !== basename(config('app.url'), '.test')) { // Ganti .test dengan .id atau domain utamamu
            return view('auth.login', ['subdomain' => $subdomain]);
        }
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $user = $request->user();

        // =================================================================
        // === VERSI FINAL YANG SUDAH DISEMPURNAKAN ===
        // =================================================================
        
        // 1. Cek untuk Super Admin (paling tinggi)
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        // 2. Cek untuk role-role yang akan masuk ke Portal
        if ($user->isAdminRw() || $user->isAdminRt() || $user->isKaderPosyandu()) {
            if ($user->desa && $user->desa->subdomain) {
                return redirect()->intended(
                    route('portal.dashboard', ['subdomain' => $user->desa->subdomain])
                );
            }
        }
        
        // 3. Cek untuk Admin Desa (yang masuk ke dashboard utama tenant)
        if ($user->isAdminDesa()) {
            if ($user->desa && $user->desa->subdomain) {
                return redirect()->intended(
                    route('tenant.dashboard', ['subdomain' => $user->desa->subdomain])
                );
            }
        }
        
        // 4. Fallback jika tidak ada role yang cocok atau user tidak punya desa
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('error', 'Akun Anda tidak terkonfigurasi dengan benar atau tidak memiliki hak akses yang sesuai.');
        // =================================================================
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $isSuperAdmin = $request->user()->isSuperAdmin();
        
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan ke login yang sesuai setelah logout
        if ($isSuperAdmin) {
            return redirect('/');
        }
        // Untuk tenant, idealnya kita tahu subdomainnya, tapi redirect ke root juga aman
        return redirect('/');
    }
}
