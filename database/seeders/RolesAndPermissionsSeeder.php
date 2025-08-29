<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- PERMISSIONS (Disederhanakan) ---
        $permissions = [
            'view superadmin menu',
            'kelola kegiatan', 'kelola aset', 'kelola fasum',
            'kelola profil', 'kelola surat', 'kelola warga',
            'kelola bantuan', // Untuk membuat pengajuan
            'verifikasi bantuan', // Untuk menyetujui/menolak
            'hapus bantuan',
            'kelola kesehatan', 'kelola pengguna', 'view laporan desa',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // --- ROLES (Disesuaikan) ---
        $roles = [
            'admin_umum'     => ['kelola fasum', 'kelola aset'],
            'bendahara_desa' => ['kelola kegiatan'],
            'operator_desa'  => ['kelola profil', 'kelola pengguna'],
            'admin_pelayanan'=> ['kelola warga', 'kelola surat'],
            // Admin Kesra bisa kelola, verifikasi, dan hapus
            'admin_kesra'    => ['kelola bantuan', 'verifikasi bantuan', 'hapus bantuan', 'kelola kesehatan'],
            'kepala_desa'    => ['view laporan desa'],
            // RT & RW hanya bisa 'kelola' (mengajukan), tidak bisa verifikasi
            'admin_rt'       => ['kelola surat', 'kelola warga', 'kelola bantuan'],
            'admin_rw'       => ['kelola surat', 'kelola warga', 'kelola bantuan'],
            'kader_posyandu' => ['kelola kesehatan'],
            'admin_desa'     => Permission::where('name', '!=', 'view superadmin menu')->pluck('name')->toArray(),
            'superadmin'     => Permission::all()->pluck('name')->toArray(),
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}