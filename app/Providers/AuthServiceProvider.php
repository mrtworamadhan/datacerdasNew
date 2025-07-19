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
        // Gate 'is_super_admin': Hanya Super Admin
        Gate::define('is_super_admin', function (User $user) {
            return $user->user_type === 'super_admin';
        });

        // Gate 'admin_desa_access': Super Admin atau Admin Desa
        Gate::define('admin_desa_access', function (User $user) {
            return $user->user_type === 'admin_desa' || $user->user_type === 'super_admin';
        });

        // Gate 'admin_rw_access': Super Admin, Admin Desa, atau Admin RW
        Gate::define('admin_rw_access', function (User $user) {
            return $user->user_type === 'admin_rw' || $user->user_type === 'admin_desa' || $user->user_type === 'super_admin';
        });

        // Gate 'admin_rt_access': Super Admin, Admin Desa, Admin RW, atau Admin RT
        Gate::define('admin_rt_access', function (User $user) {
            return $user->user_type === 'admin_rt' || $user->user_type === 'admin_rw' || $user->user_type === 'admin_desa' || $user->user_type === 'super_admin';
        });

        Gate::define('create-surat-request', function (User $user) {
            return $user->user_type === 'admin_rt' || $user->user_type === 'admin_rw' || $user->user_type === 'admin_desa' || $user->user_type === 'super_admin';
        });

        // Gate 'kader_posyandu_access': Super Admin, Admin Desa, Admin RW, Admin RT, atau Kader Posyandu
        Gate::define('kader_posyandu_access', function (User $user) {
            return $user->user_type === 'kader_posyandu' || $user->user_type === 'admin_rt' || $user->user_type === 'admin_rw' || $user->user_type === 'admin_desa' || $user->user_type === 'super_admin';
        });

        // Gate 'auth': User terautentikasi (seharusnya sudah otomatis oleh AdminLTE, tapi bisa didaftarkan juga)
        Gate::define('auth', function (User $user) {
            return true;
        });
    }
}