<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Desa;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@tatadesa.id'],
            [
                'name' => 'Super Admin TataDesa',
                'password' => Hash::make('password'),
                'user_type' => 'super_admin',
                'desa_id' => null,
            ]
        );

        // Desa Dummy
        $desa1 = Desa::firstOrCreate(
            ['nama_desa' => 'Desa Pagelaran'],
            [
                'kecamatan' => 'Ciomas',
                'kota' => 'Kab Bogor',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '12345',
                'alamat_desa' => 'Jl. Raya Maju Jaya No. 1',
                'nama_kades' => 'Ir. Suganda',
                'jumlah_rw' => 3,
                'jumlah_rt' => 3,
                'status_langganan' => 'aktif',
                'tanggal_mulai_langganan' => now(),
                'tanggal_akhir_langganan' => now()->addYear(),
            ]
        );

        $desa2 = Desa::firstOrCreate(
            ['nama_desa' => 'Desa Srumpit'],
            [
                'kecamatan' => 'Widodaren',
                'kota' => 'Kab Ngawi',
                'provinsi' => 'Jawa Timur',
                'kode_pos' => '54321',
                'alamat_desa' => 'Jl. Sejahtera Abadi No. 2',
                'nama_kades' => 'Sri Sulastri,MH',
                'jumlah_rw' => 2,
                'jumlah_rt' => 2,
                'status_langganan' => 'pending',
                'tanggal_mulai_langganan' => now(),
                'tanggal_akhir_langganan' => now()->addMonths(6),
            ]
        );

        // Admin Desa
        User::firstOrCreate(
            ['email' => 'admin.pagelaran@tatadesa.id'],
            [
                'name' => 'Admin Desa Pagelaran',
                'password' => Hash::make('password'),
                'user_type' => 'admin_desa',
                'desa_id' => $desa1->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin.srumpit@tatadesa.id'],
            [
                'name' => 'Admin Desa Srumpit',
                'password' => Hash::make('password'),
                'user_type' => 'admin_desa',
                'desa_id' => $desa2->id,
            ]
        );

        // Buat RW/RT + User RW/RT
        foreach ([$desa1, $desa2] as $desa) {
            $slugDesa = Str::slug($desa->nama_desa);
            for ($i = 1; $i <= $desa->jumlah_rw; $i++) {
                $rwNomor = str_pad($i, 2, '0', STR_PAD_LEFT);
                $rw = RW::firstOrCreate(
                    ['desa_id' => $desa->id, 'nomor_rw' => $rwNomor],
                    ['nama_ketua' => 'Ketua RW ' . $rwNomor]
                );

                // Buat User untuk RW
                User::firstOrCreate(
                    ['email' => "rw{$rwNomor}_{$slugDesa}@tatadesa.id"],
                    [
                        'name' => "Ketua RW {$rwNomor}",
                        'password' => Hash::make('password'),
                        'user_type' => 'admin_rw',
                        'desa_id' => $desa->id,
                        'rw_id' => $rw->id,
                    ]
                );

                for ($j = 1; $j <= $desa->jumlah_rt; $j++) {
                    $rtNomor = str_pad($j, 2, '0', STR_PAD_LEFT);
                    $rt = RT::firstOrCreate(
                        ['desa_id' => $desa->id, 'rw_id' => $rw->id, 'nomor_rt' => $rtNomor],
                        ['nama_ketua' => 'Ketua RT ' . $rtNomor]
                    );

                    // Buat User untuk RT
                    User::firstOrCreate(
                        ['email' => "{$rwNomor}{$rtNomor}_{$slugDesa}@tatadesa.id"],
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
