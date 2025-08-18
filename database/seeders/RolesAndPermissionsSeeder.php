<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache agar tidak ada error duplikasi permission/role
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- PERMISSIONS ---
        $permissions = [
            'kelola kegiatan',
            'buat proposal',
            'setujui proposal',
            'buat lpj',
            'kelola aset',
            'kelola fasum',
            'kelola profil',
            'kelola surat',
            'kelola warga',
            'kelola bantuan',
            'kelola kesehatan',
            'kelola pengguna',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // --- ROLES ---
        $roles = [
            'kader_posyandu' => ['kelola kesehatan'],

            'admin_rt' => ['kelola fasum', 'kelola surat', 'kelola warga'],
            'admin_rw' => ['kelola fasum', 'kelola surat', 'kelola warga'],

            'lembaga' => ['buat proposal', 'buat lpj'],

            'operator_desa' => ['kelola profil', 'kelola pengguna'],
            'bendahara_desa' => ['buat proposal', 'buat lpj', 'kelola kegiatan'],
            'admin_pelayanan' => ['kelola aset', 'kelola fasum', 'kelola surat', 'kelola warga'],
            'admin_kesra' => ['kelola bantuan', 'kelola kesehatan'],

            'admin_desa' => Permission::all()->pluck('name')->toArray(),
            'superadmin' => Permission::all()->pluck('name')->toArray(),
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
