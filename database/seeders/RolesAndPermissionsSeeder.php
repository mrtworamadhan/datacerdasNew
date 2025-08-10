<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache roles dan permissions agar tidak ada error duplikasi
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- BUAT DAFTAR TUGAS (PERMISSIONS) ---
        // Format: Permission::create(['name' => 'aksi-objek']);
        Permission::create(['name' => 'kelola kegiatan']); // Untuk CRUD universal
        Permission::create(['name' => 'buat proposal']);
        Permission::create(['name' => 'setujui proposal']);
        Permission::create(['name' => 'buat lpj']);
        Permission::create(['name' => 'kelola aset']);
        Permission::create(['name' => 'kelola fasum']);
        Permission::create(['name' => 'kelola surat']);
        Permission::create(['name' => 'kelola warga']);
        Permission::create(['name' => 'kelola kesehatan']);
        Permission::create(['name' => 'kelola pengguna']);

        // --- BUAT JABATAN (ROLES) DAN BERIKAN TUGASNYA ---

        // Role untuk Kader Posyandu
        $roleKader = Role::firstOrCreate(['name' => 'kader_posyandu']);
        $roleKader->givePermissionTo('kelola kesehatan');

        // Role untuk Admin RT & RW (bisa kita kembangkan nanti)
        $roleRt = Role::firstOrCreate(['name' => 'admin_rt']);
        $roleRt->givePermissionTo(['kelola fasum', 'kelola surat', 'kelola warga' ]);

        $roleRw = Role::firstOrCreate(['name' => 'admin_rw']);
        $roleRw->givePermissionTo(['kelola fasum', 'kelola surat', 'kelola warga' ]);
        
        // Role untuk Ketua Lembaga & Kelompok (jabatan baru)
        $roleKetua = Role::firstOrCreate(['name' => 'ketua_organisasi']);
        $roleKetua->givePermissionTo(['buat proposal', 'buat lpj']);

        // Role untuk Admin Desa
        $roleAdminDesa = Role::firstOrCreate(['name' => 'admin_desa']);
        $roleAdminDesa->givePermissionTo(Permission::all()); // Admin bisa melakukan semuanya

        // Role untuk Super Admin (jika ada)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'superadmin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());
    }
}