<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // =================================================================
            // === INI ADALAH "PERINTAH EKSPLISIT" FINAL UNTUK LARAVEL 12 ===
            // =================================================================
            // Format: Route::model('nama_parameter_di_route', NamaClassModel::class);

            Route::model('aset', \App\Models\Aset::class);
            Route::model('fasum', \App\Models\Fasum::class);
            Route::model('kegiatan', \App\Models\Kegiatan::class);
            Route::model('kelompok', \App\Models\Kelompok::class);
            Route::model('lembaga', \App\Models\Lembaga::class);
            Route::model('pengeluaran', \App\Models\Pengeluaran::class);
            Route::model('pengajuanSurat', \App\Models\PengajuanSurat::class);
            Route::model('jenisSurat', \App\Models\JenisSurat::class);
            // ... (tambahkan untuk model lain jika ada)
            // =================================================================
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            // \App\Http\Middleware\IsSuperAdmin::class, 
            \App\Http\Middleware\TenantMiddleware::class,
            \App\Http\Middleware\CheckSubscriptionStatus::class,
            // \App\Http\Middleware\ForbidAdminDashboardAccess::class,
        ]);
        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'tenant'=> \App\Http\Middleware\TenantMiddleware::class,
            'is_super_admin' => \App\Http\Middleware\IsSuperAdmin::class, 
            'check.subscription' => \App\Http\Middleware\CheckSubscriptionStatus::class,
            'forbid_admin_dashboard' => \App\Http\Middleware\ForbidAdminDashboardAccess::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
        $middleware->priority([
            \App\Http\Middleware\TenantMiddleware::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            // Coba dapatkan instance 'tenant' (desa) yang sudah diatur
            $desa = app('tenant');

            // Jika ada tenant (desa) yang aktif dari subdomain
            if ($desa) {
                // Arahkan ke route login DENGAN nama 'tenant.login'
                return route('login', ['subdomain' => $desa->subdomain]);
            }
            
            // Jika tidak ada subdomain, arahkan ke route login GLOBAL
            return route('login'); 
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
    })->create();

