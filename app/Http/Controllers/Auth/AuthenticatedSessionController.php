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
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logika logout bawaan Laravel
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // DEBUGGING: Log dan dd() untuk melihat redirect target
        $redirectTarget = redirect('/login'); // Arahkan secara eksplisit ke halaman login
        Log::info('User logged out. Redirecting to: ' . $redirectTarget->getTargetUrl());
        // dd('User logged out. Redirecting to: ' . $redirectTarget->getTargetUrl()); // Aktifkan ini untuk debugging langsung

        return $redirectTarget;
    }
}
