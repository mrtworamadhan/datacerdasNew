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

        $permissions = [
            'view superadmin menu',
            'kelola kegiatan', 'kelola aset', 'kelola fasum',
            'kelola profil', 'kelola surat', 'kelola warga',
            'kelola bantuan',
            'verifikasi bantuan',
            'hapus bantuan',
            'kelola kesehatan', 'kelola pengguna', 'view laporan desa',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $roles = [
            'admin_umum'     => ['kelola fasum', 'kelola aset'],
            'bendahara_desa' => ['kelola kegiatan'],
            'operator_desa'  => ['kelola profil', 'kelola pengguna'],
            'admin_pelayanan'=> ['kelola warga', 'kelola surat'],
            'admin_kesra'    => ['kelola bantuan', 'verifikasi bantuan', 'hapus bantuan', 'kelola kesehatan'],
            'kepala_desa'    => ['view laporan desa'],
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