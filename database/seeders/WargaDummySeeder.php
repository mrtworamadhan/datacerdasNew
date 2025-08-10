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

            // Buat 60 KK (unsaved models dari factory), nanti kita save satu per satu
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
                $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
                $birthDate = $faker->dateTimeBetween('-100 years', 'now');
                $age = (new \DateTime())->diff($birthDate)->y;
                $statusPerkawinan = $age < 17 
                    ? 'BELUM KAWIN' 
                    : $faker->randomElement([
                        'BELUM KAWIN',
                        'KAWIN TERCATAT',
                        'KAWIN BLM TERCATAT',
                        'CERAI MATI',
                        'CERAI HIDUP',
                        'CERAI BLM TERCATAT'
                    ]);

                $nikKepala = $this->generateUniqueNIK($desa->id, $faker, $birthDate, $gender);

                $kepala = Warga::factory()->create([
                    'desa_id' => $desa->id,
                    'kartu_keluarga_id' => $kk->id,
                    'rw_id' => $kk->rw_id,
                    'rt_id' => $kk->rt_id,
                    'hubungan_keluarga' => 'Kepala Keluarga',
                    'nik' => $nikKepala,
                    'jenis_kelamin' => $gender,
                    'tanggal_lahir' => $birthDate->format('Y-m-d'),
                    'status_perkawinan' => $statusPerkawinan,
                ]);

                $kk->update(['kepala_keluarga_id' => $kepala->id]);

                // Anggota keluarga lain (2-4 orang)
                $jumlahAnggota = rand(2, 4);
                for ($i = 0; $i < $jumlahAnggota; $i++) {
                    $genderA = $faker->randomElement(['Laki-laki', 'Perempuan']);
                    $birthDateA = $faker->dateTimeBetween('-100 years', 'now');
                    $ageA = (new \DateTime())->diff($birthDateA)->y;
                    $statusA = $ageA < 17 ? 'BELUM KAWIN' : $faker->randomElement([
                        'BELUM KAWIN',
                        'KAWIN TERCATAT',
                        'KAWIN BLM TERCATAT',
                        'CERAI MATI',
                        'CERAI HIDUP',
                        'CERAI BLM TERCATAT'
                    ]);

                    Warga::factory()->create([
                        'desa_id' => $desa->id,
                        'kartu_keluarga_id' => $kk->id,
                        'rw_id' => $kk->rw_id,
                        'rt_id' => $kk->rt_id,
                        'hubungan_keluarga' => $faker->randomElement(['Istri', 'Anak', 'Cucu', 'Famili Lain']),
                        'nik' => $this->generateUniqueNIK($desa->id, $faker, $birthDateA, $genderA),
                        'jenis_kelamin' => $genderA,
                        'tanggal_lahir' => $birthDateA->format('Y-m-d'),
                        'status_perkawinan' => $statusA,
                    ]);
                }
            }

            // Beberapa warga tanpa KK
            $this->command->info('Membuat warga tanpa KK...');
            for ($i = 0; $i < 10; $i++) {
                $randomRw = $rws->random();
                $randomRt = $rts->where('rw_id', $randomRw->id)->random();

                $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
                $birthDate = $faker->dateTimeBetween('-100 years', 'now');
                $age = (new \DateTime())->diff($birthDate)->y;
                $status = $age < 17 ? 'BELUM KAWIN' : $faker->randomElement([
                    'BELUM KAWIN',
                    'KAWIN TERCATAT',
                    'KAWIN BLM TERCATAT',
                    'CERAI MATI',
                    'CERAI HIDUP',
                    'CERAI BLM TERCATAT'
                ]);

                Warga::factory()->create([
                    'desa_id' => $desa->id,
                    'kartu_keluarga_id' => null,
                    'rw_id' => $randomRw->id,
                    'rt_id' => $randomRt->id,
                    'hubungan_keluarga' => null,
                    'nik' => $this->generateUniqueNIK($desa->id, $faker, $birthDate, $gender),
                    'jenis_kelamin' => $gender,
                    'tanggal_lahir' => $birthDate->format('Y-m-d'),
                    'status_perkawinan' => $status,
                ]);
            }

            $this->command->info('Selesai membuat data dummy untuk desa: ' . $desa->nama_desa);
        }
    }

    /**
     * Generate KK number: 6-digit kode wilayah + 10-digit unik -> total 16 digit
     */
    private function generateUniqueKKNumber($desaId, $faker): string
    {
        do {
            // pastikan 6 digit (misal 320xxx)
            $kodeWilayah = str_pad((string) $faker->numberBetween(320000, 329999), 6, '0', STR_PAD_LEFT);
            // 10 digit acak
            $nomorUnik = $faker->numerify('##########');

            $kk = $kodeWilayah . $nomorUnik;
        } while (KartuKeluarga::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->where('nomor_kk', $kk)
            ->exists());

        return $kk;
    }

    /**
     * Generate NIK dari tanggal lahir + gender sehingga konsisten dengan tanggal_lahir field.
     * Format: 6-digit kode wilayah + ddmmyy (female: dd+40) + 4-digit urut = 16 digit
     */
    private function generateUniqueNIK($desaId, $faker, \DateTimeInterface $birthDate, string $gender): string
    {
        do {
            $kodeWilayah = str_pad((string) $faker->numberBetween(320000, 329999), 6, '0', STR_PAD_LEFT);

            $day = (int) $birthDate->format('d');
            if ($gender === 'Perempuan') {
                $day += 40;
            }
            $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
            $month = $birthDate->format('m');
            $year = $birthDate->format('y'); // 2 digit

            $tanggalLahir = $day . $month . $year;

            $nomorUrut = str_pad((string) $faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

            $nik = $kodeWilayah . $tanggalLahir . $nomorUrut;
        } while (Warga::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->where('nik', $nik)
            ->exists());

        return $nik;
    }
}
