<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desa;
use App\Models\RW;
use App\Models\RT;
use App\Models\KartuKeluarga;
use App\Models\Warga;

class WargaDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $desas = Desa::all();

        if ($desas->isEmpty()) {
            $this->command->error('Tidak ada desa ditemukan.');
            return;
        }

        foreach ($desas as $desa) {
            $this->command->info('Membuat data dummy untuk desa: ' . $desa->nama_desa);

            $rws = RW::withoutGlobalScopes()->where('desa_id', $desa->id)->get();
            $rts = RT::withoutGlobalScopes()->where('desa_id', $desa->id)->get();

            if ($rws->isEmpty() || $rts->isEmpty()) {
                $this->command->warn('RW/RT tidak ditemukan untuk desa: ' . $desa->nama_desa);
                continue;
            }

            // Buat 60 KK
            $kartuKeluargas = KartuKeluarga::factory(60)->make();

            foreach ($kartuKeluargas as $kk) {
                $randomRw = $rws->random();
                $randomRt = $rts->where('rw_id', $randomRw->id)->random();

                $kk->desa_id = $desa->id;
                $kk->rw_id = $randomRw->id;
                $kk->rt_id = $randomRt->id;
                $kk->nomor_kk = $this->generateUniqueKKNumber($desa->id, $faker);
                $kk->save();

                // Kepala Keluarga
                $kepala = Warga::factory()->make([
                    'desa_id' => $desa->id,
                    'kartu_keluarga_id' => $kk->id,
                    'rw_id' => $kk->rw_id,
                    'rt_id' => $kk->rt_id,
                    'hubungan_keluarga' => 'Kepala Keluarga',
                    'nik' => $this->generateUniqueNIK($desa->id, $faker),
                ]);
                $kepala->save();
                $kk->update(['kepala_keluarga_id' => $kepala->id]);

                // Anggota keluarga lain
                $jumlahAnggota = rand(2, 4);
                for ($i = 0; $i < $jumlahAnggota; $i++) {
                    Warga::factory()->create([
                        'desa_id' => $desa->id,
                        'kartu_keluarga_id' => $kk->id,
                        'rw_id' => $kk->rw_id,
                        'rt_id' => $kk->rt_id,
                        'hubungan_keluarga' => $faker->randomElement(['Istri', 'Anak', 'Cucu']),
                        'nik' => $this->generateUniqueNIK($desa->id, $faker),
                    ]);
                }
            }

            $this->command->info('Membuat warga...');
            for ($i = 0; $i < 10; $i++) {
                $randomRw = $rws->random();
                $randomRt = $rts->where('rw_id', $randomRw->id)->random();

                Warga::factory()->create([
                    'desa_id' => $desa->id,
                    'kartu_keluarga_id' => null,
                    'rw_id' => $randomRw->id,
                    'rt_id' => $randomRt->id,
                    'hubungan_keluarga' => null,
                    'nik' => $this->generateUniqueNIK($desa->id, $faker),
                ]);
            }

            $this->command->info('Selesai membuat data dummy untuk desa: ' . $desa->nama_desa);
        }
    }

    private function generateUniqueKKNumber($desaId, $faker): string
    {
        do {
            $number = $faker->numerify('##############');
        } while (KartuKeluarga::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->where('nomor_kk', $number)
            ->exists());

        return $number;
    }

    private function generateUniqueNIK($desaId, $faker): string
    {
        do {
            $nik = $faker->numerify('################');
        } while (Warga::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->where('nik', $nik)
            ->exists());

        return $nik;
    }
}
