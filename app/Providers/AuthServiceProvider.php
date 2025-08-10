<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\User; // Penting: Pastikan namespace User model kamu benar
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Contoh: Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate ini sekarang HANYA untuk Super Admin. Sangat jelas.
        Gate::define('is_super_admin', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Gate ini sekarang HANYA untuk Admin Desa. Tidak ada lagi || super_admin.
        Gate::define('admin_desa_access', function (User $user) {
            return $user->isAdminDesa();
        });

        // Gate ini sekarang lebih sederhana.
        Gate::define('admin_rw_access', function (User $user) {
            return $user->isAdminDesa() || $user->isAdminRw();
        });

        // Gate ini juga menjadi lebih sederhana.
        Gate::define('admin_rt_access', function (User $user) {
            return $user->isAdminDesa() || $user->isAdminRw() || $user->isAdminRt();
        });

        // Gate untuk Kader Posyandu
        Gate::define('kader_posyandu_access', function (User $user) {
            // Admin Desa kita anggap bisa mengakses menu kader juga
            return $user->isKaderPosyandu() || $user->isAdminDesa();
        });
    }
}