<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Desa;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DesaSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        // User::firstOrCreate(
        //     ['email' => 'admin@datacerdas.com'],
        //     [
        //         'name' => 'Super Admin DataCerdas',
        //         'password' => Hash::make('password'),
        //         'user_type' => 'super_admin',
        //         'desa_id' => null,
        //     ]
        // );

        // Desa Dummy
        $desa1 = Desa::firstOrCreate(
            ['nama_desa' => 'Cerdas'],
            [
                'subdomain' => 'cerdas',
                'kecamatan' => 'Ciomas',
                'kota' => 'Kab Bogor',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '12345',
                'alamat_desa' => 'Jl. Raya Cerdas Maju Jaya No. 1',
                'nama_kades' => 'Arunika Larasati, MM',
                'subscription_status' => 'aktif',
                'subscription_ends_at' => now()->addYear(),
            ]
        );

        // Admin Desa
        User::firstOrCreate(
            ['email' => 'admin.cerdas@datacerdas.com'],
            [
                'name' => 'Admin Desa Cerdas',
                'password' => Hash::make('password'),
                'user_type' => 'admin_desa',
                'desa_id' => $desa1->id,
            ]
        );

        $jumlahRW = 3;
        $jumlahRTPerRW = 3;
        // Buat RW/RT + User RW/RT
        foreach ([$desa1] as $desa) {
            $slugDesa = Str::slug($desa->nama_desa);
            for ($i = 1; $i <= $jumlahRW; $i++) {
                $rwNomor = str_pad($i, 2, '0', STR_PAD_LEFT);
                $rw = RW::firstOrCreate(
                    ['desa_id' => $desa->id, 'nomor_rw' => $rwNomor],
                    ['nama_ketua' => 'Ketua RW ' . $rwNomor]
                );

                // Buat User untuk RW
                User::firstOrCreate(
                    ['email' => "rw{$rwNomor}.{$slugDesa}@datacerdas.com"],
                    [
                        'name' => "Ketua RW {$rwNomor}",
                        'password' => Hash::make('password'),
                        'user_type' => 'admin_rw',
                        'desa_id' => $desa->id,
                        'rw_id' => $rw->id,
                    ]
                );

                for ($j = 1; $j <= $jumlahRTPerRW; $j++) {
                    $rtNomor = str_pad($j, 2, '0', STR_PAD_LEFT);
                    $rt = RT::firstOrCreate(
                        ['desa_id' => $desa->id, 'rw_id' => $rw->id, 'nomor_rt' => $rtNomor],
                        ['nama_ketua' => 'Ketua RT ' . $rtNomor]
                    );

                    // Buat User untuk RT
                    User::firstOrCreate(
                        ['email' => "rt{$rtNomor}{$rwNomor}.{$slugDesa}@datacerdas.com"],
                        [
                            'name' => "Ketua RT {$rtNomor} RW {$rwNomor}",
                            'password' => Hash::make('password'),
                            'user_type' => 'admin_rt',
                            'desa_id' => $desa->id,
                            'rw_id' => $rw->id,
                            'rt_id' => $rt->id,
                        ]
                    );
                }
            }
        }
    }
}
