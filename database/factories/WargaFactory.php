<?php

namespace Database\Factories;

use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Untuk Str::random

class WargaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Warga::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
        $agamaOptions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        $statusPerkawinanOptions = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
        $pekerjaanOptions = [
            'Belum / Tidak Bekerja', 'Mengurus Rumah Tangga', 'Pelajar / Mahasiswa', 'Pensiunan',
            'Pegawai Negeri Sipil', 'Tentara Nasional Indonesia', 'Kepolisian RI', 'Perdagangan',
            'Petani / Pekebun', 'Peternak', 'Nelayan / Perikanan', 'Industri', 'Konstruksi',
            'Transportasi', 'Karyawan Swasta', 'Karyawan BUMN', 'Karyawan BUMD', 'Karyawan Honorer',
            'Buruh Harian Lepas', 'Buruh Tani / Perkebunan', 'Buruh Nelayan / Perikanan',
            'Buruh Peternakan', 'Pembantu Rumah Tangga', 'Tukang Cukur', 'Tukang Listrik',
            'Tukang Batu', 'Tukang Kayu', 'Tukang Sol Sepatu', 'Tukang Las / Pandai Besi',
            'Tukang Jahit', 'Penata Rambut', 'Penata Rias', 'Penata Busana', 'Mekanik', 'Tukang Gigi',
            'Seniman', 'Tabib', 'Paraji', 'Perancang Busana', 'Penerjemah', 'Imam Masjid',
            'Pendeta', 'Pastur', 'Wartawan', 'Ustadz / Mubaligh', 'Juru Masak', 'Promotor Acara',
            'Anggota DPR-RI', 'Anggota DPD', 'Anggota BPK', 'Presiden', 'Wakil Presiden',
            'Anggota Mahkamah Konstitusi', 'Anggota Kabinet / Kementerian', 'Duta Besar', 'Gubernur',
            'Wakil Gubernur', 'Bupati', 'Wakil Bupati', 'Walikota', 'Wakil Walikota',
            'Anggota DPRD Provinsi', 'Anggota DPRD Kabupaten', 'Dosen', 'Guru', 'Pilot',
            'Pengacara', 'Notaris', 'Arsitek', 'Akuntan', 'Konsultan', 'Dokter', 'Bidan', 'Perawat',
            'Apoteker', 'Psikiater / Psikolog', 'Penyiar Televisi', 'Penyiar Radio', 'Pelaut',
            'Peneliti', 'Sopir', 'Pialang', 'Paranormal', 'Pedagang', 'Perangkat Desa',
            'Kepala Desa', 'Biarawati', 'Wiraswasta', 'Anggota Lembaga Tinggi', 'Artis', 'Atlit',
            'Chef', 'Manajer', 'Tenaga Tata Usaha', 'Operator', 'Pekerja Pengolahan, Kerajinan',
            'Teknisi', 'Asisten Ahli', 'Lainnya'
        ];
        $pendidikanOptions = ['Tidak/Belum Sekolah', 'SD', 'SMP', 'SMA', 'S1', 'S2', 'S3'];
        $kewarganegaraanOptions = ['WNI', 'WNA'];
        $golonganDarahOptions = ['A', 'B', 'AB', 'O', '-'];
        $hubunganKeluargaOptions = ['Kepala Keluarga','Anak', 'Cucu', 'Istri', 'Menantu', 'Suami', 'Saudara', 'Kakak', 'Adik', 'Lainnya'];
        $statusKependudukanOptions = ['Warga Asli', 'Pendatang', 'Sementara', 'Pindah', 'Meninggal'];
        $statusKhususOptions = ['Disabilitas', 'Lansia', 'Ibu Hamil', 'Balita', 'Penerima PKH', 'Penerima BPNT', 'Lainnya'];

        // Generate random birth date for age variation
        $birthDate = $this->faker->dateTimeBetween('-80 years', '-1 year');
        $age = $birthDate->diff(now())->y; // Calculate age

        $statusKhusus = [];
        if ($age < 5 && $this->faker->boolean(50)) { // 50% chance to be balita if <5
            $statusKhusus[] = 'Balita';
        }
        if ($age >= 60 && $this->faker->boolean(40)) { // 40% chance to be lansia if >=60
            $statusKhusus[] = 'Lansia';
        }
        if ($this->faker->boolean(10)) { // 10% chance to be disabilitas
            $statusKhusus[] = 'Disabilitas';
        }
        if ($this->faker->boolean(5) && $this->faker->randomElement(['Perempuan']) == 'Perempuan' && $age >= 15 && $age <= 45) { // 5% chance to be ibu hamil for women of childbearing age
            $statusKhusus[] = 'Ibu Hamil';
        }
        if ($this->faker->boolean(20)) { // 20% chance to be PKH
            $statusKhusus[] = 'Penerima PKH';
        }


        return [
            'desa_id' => null, // Akan diisi di seeder
            'kartu_keluarga_id' => null, // Akan diisi di seeder
            'rw_id' => null, // Akan diisi di seeder
            'rt_id' => null, // Akan diisi di seeder
            'nik' => $this->faker->unique()->numerify('################'), // 16 digit
            'nama_lengkap' => $this->faker->name($this->faker->randomElement($jenisKelaminOptions) == 'Laki-laki' ? 'male' : 'female'),
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $birthDate->format('Y-m-d'),
            'jenis_kelamin' => $this->faker->randomElement($jenisKelaminOptions),
            'agama' => $this->faker->randomElement($agamaOptions),
            'status_perkawinan' => $this->faker->randomElement($statusPerkawinanOptions),
            'pekerjaan' => $this->faker->randomElement($pekerjaanOptions),
            'pendidikan' => $this->faker->randomElement($pendidikanOptions), // Tambahkan ini
            'kewarganegaraan' => $this->faker->randomElement($kewarganegaraanOptions),
            'golongan_darah' => $this->faker->randomElement($golonganDarahOptions),
            'alamat_lengkap' => $this->faker->address,
            'hubungan_keluarga' => $this->faker->randomElement($hubunganKeluargaOptions),
            'status_kependudukan' => $this->faker->randomElement($statusKependudukanOptions),
            'status_khusus' => !empty($statusKhusus) ? json_encode($statusKhusus) : null,
        ];
    }
}