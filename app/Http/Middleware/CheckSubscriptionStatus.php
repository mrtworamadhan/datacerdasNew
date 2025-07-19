<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user login dan memiliki desa
        if (Auth::check() && Auth::user()->desa) {
            $user = Auth::user();
            $desa = $user->desa;

            // Super admin tidak dibatasi
            if ($user->user_type === 'super_admin') {
                return $next($request);
            }

            // Izinkan akses ke route tertentu tanpa blokir langganan
            if ($request->routeIs('logout', 'subscription.expired')) {
                return $next($request);
            }

            // Jika langganan tidak aktif, redirect ke halaman expired
            if ($desa->isSubscriptionInactive()) {
                return redirect()->route('subscription.expired');
            }

            // Jika langganan aktif atau trial, beri notifikasi jika mendekati habis
            if ($desa->isSubscriptionActive() || $desa->isInTrial()) {
                $endsAt = $desa->subscription_ends_at ?? $desa->trial_ends_at;

                if ($endsAt && $endsAt->isFuture()) {
                    $remainingDays = Carbon::now()->diffInDays($endsAt, false);
                    if ($remainingDays <= 7 && $remainingDays >= 0) {
                        session()->flash('warning', "Masa langganan desa Anda akan berakhir dalam {$remainingDays} hari. Harap segera perpanjang.");
                    } elseif ($remainingDays < 0) {
                        session()->flash('error', "Masa langganan desa Anda telah berakhir.");
                    }
                }
            }
        }

        return $next($request);
    }

}
