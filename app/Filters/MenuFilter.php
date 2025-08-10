<?php

namespace App\Filters;

// Kita tidak lagi butuh 'use Builder'
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class MenuFilter implements FilterInterface
{
    /**
     * Transforms a menu item.
     *
     * @param  array  $item The menu item being transformed
     * @return array|bool The transformed menu item, or false to remove it
     */
    public function transform($item)
    {
        $user = Auth::user();

        // Jika user tidak login, sembunyikan semua menu yang butuh role
        if (!$user) {
            // Kita biarkan menu Profile (yang tidak punya 'role') tetap lewat
            return isset($item['role']) ? false : $item;
        }

        // Cek jika menu ini punya 'role'
        if (isset($item['role'])) {
            if ($user->isSuperAdmin()) {
                // Jika user adalah Super Admin, hanya tampilkan menu dengan role 'superadmin'
                if ($item['role'] === 'superadmin') {
                    return $item; // Kembalikan paket menu
                }
            } else {
                // Jika user BUKAN Super Admin, hanya tampilkan menu dengan role 'tenant'
                if ($item['role'] === 'tenant') {
                    return $item; // Kembalikan paket menu
                }
            }
            // Jika role tidak cocok, sembunyikan menu ini
            return false;
        }

        // Jika menu tidak punya 'role' (misal: header PENGATURAN AKUN, menu Profile),
        // tampilkan untuk semua user yang sudah login.
        return $item;
    }
}