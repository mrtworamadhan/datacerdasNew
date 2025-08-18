<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Klasifikasi
        DB::table('klasifikasis')->insert([
            ['nama' => 'Pra-Sejahtera'],
            ['nama' => 'Sejahtera I'],
            ['nama' => 'Sejahtera II'],
            ['nama' => 'Sejahtera III'],
            ['nama' => 'Sejahtera III Plus'],
        ]);

        // 2. Agama
        DB::table('agamas')->insert([
            ['nama' => 'Islam'],
            ['nama' => 'Kristen'],
            ['nama' => 'Katolik'],
            ['nama' => 'Hindu'],
            ['nama' => 'Buddha'],
            ['nama' => 'Konghucu'],
            ['nama' => 'Lainnya'],
        ]);

        // 3. Status Perkawinan
        DB::table('status_perkawinans')->insert([
            ['nama' => 'BELUM KAWIN'],
            ['nama' => 'KAWIN TERCATAT'],
            ['nama' => 'KAWIN BLM TERCATAT'],
            ['nama' => 'CERAI MATI'],
            ['nama' => 'CERAI HIDUP'],
            ['nama' => 'CERAI BLM TERCATAT'],
        ]);

        // 4. Pekerjaan
        DB::table('pekerjaans')->insert([
            ['nama' => 'Blm Bekerja'], ['nama' => 'Mengurus Rumah Tangga'], ['nama' => 'Pelajar / Mahasiswa'],
            ['nama' => 'Pensiunan'], ['nama' => 'Pegawai Negeri Sipil'], ['nama' => 'Tentara Nasional Indonesia'],
            ['nama' => 'Kepolisian RI'], ['nama' => 'Perdagangan'], ['nama' => 'Petani / Pekebun'], ['nama' => 'Peternak'],
            ['nama' => 'Nelayan / Perikanan'], ['nama' => 'Industri'], ['nama' => 'Konstruksi'],
            ['nama' => 'Transportasi'], ['nama' => 'Karyawan Swasta'], ['nama' => 'Karyawan BUMN'], ['nama' => 'Karyawan BUMD'],
            ['nama' => 'Karyawan Honorer'], ['nama' => 'Buruh Harian Lepas'], ['nama' => 'Buruh Tani / Perkebunan'],
            ['nama' => 'Buruh Nelayan / Perikanan'], ['nama' => 'Buruh Peternakan'], ['nama' => 'Pembantu Rumah Tangga'],
            ['nama' => 'Tukang Cukur'], ['nama' => 'Tukang Listrik'], ['nama' => 'Tukang Batu'], ['nama' => 'Tukang Kayu'],
            ['nama' => 'Tukang Sol Sepatu'], ['nama' => 'Tukang Las / Pandai Besi'], ['nama' => 'Tukang Jahit'],
            ['nama' => 'Penata Rambut'], ['nama' => 'Penata Rias'], ['nama' => 'Penata Busana'], ['nama' => 'Mekanik'],
            ['nama' => 'Tukang Gigi'], ['nama' => 'Seniman'], ['nama' => 'Tabib'], ['nama' => 'Paraji'], ['nama' => 'Perancang Busana'],
            ['nama' => 'Penerjemah'], ['nama' => 'Imam Masjid'], ['nama' => 'Pendeta'], ['nama' => 'Pastur'],
            ['nama' => 'Wartawan'], ['nama' => 'Ustadz / Mubaligh'], ['nama' => 'Juru Masak'], ['nama' => 'Promotor Acara'],
            ['nama' => 'Anggota DPR-RI'], ['nama' => 'Anggota DPD'], ['nama' => 'Anggota BPK'], ['nama' => 'Presiden'],
            ['nama' => 'Wakil Presiden'], ['nama' => 'Anggota Mahkamah Konstitusi'], ['nama' => 'Anggota Kabinet / Kementerian'],
            ['nama' => 'Duta Besar'], ['nama' => 'Gubernur'], ['nama' => 'Wakil Gubernur'], ['nama' => 'Bupati'], ['nama' => 'Wakil Bupati'],
            ['nama' => 'Walikota'], ['nama' => 'Wakil Walikota'], ['nama' => 'Anggota DPRD Provinsi'],
            ['nama' => 'Anggota DPRD Kabupaten'], ['nama' => 'Dosen'], ['nama' => 'Guru'], ['nama' => 'Pilot'],
            ['nama' => 'Pengacara'], ['nama' => 'Notaris'], ['nama' => 'Arsitek'], ['nama' => 'Akuntan'], ['nama' => 'Konsultan'],
            ['nama' => 'Dokter'], ['nama' => 'Bidan'], ['nama' => 'Perawat'], ['nama' => 'Apoteker'],
            ['nama' => 'Psikiater / Psikolog'], ['nama' => 'Penyiar Televisi'], ['nama' => 'Penyiar Radio'],
            ['nama' => 'Pelaut'], ['nama' => 'Peneliti'], ['nama' => 'Sopir'], ['nama' => 'Pialang'], ['nama' => 'Paranormal'],
            ['nama' => 'Pedagang'], ['nama' => 'Perangkat Desa'], ['nama' => 'Kepala Desa'], ['nama' => 'Biarawati'],
            ['nama' => 'Wiraswasta'], ['nama' => 'Anggota Lembaga Tinggi'], ['nama' => 'Artis'], ['nama' => 'Atlit'],
            ['nama' => 'Chef'], ['nama' => 'Manajer'], ['nama' => 'Tenaga Tata Usaha'], ['nama' => 'Operator'],
            ['nama' => 'Pekerja Pengolahan, Kerajinan'], ['nama' => 'Teknisi'], ['nama' => 'Asisten Ahli'], ['nama' => 'Lainnya'],
        ]);

        // 5. Pendidikan
        DB::table('pendidikans')->insert([
            ['nama' => 'BLM SEKOLAH'],
            ['nama' => 'BLM TAMAT SD'],
            ['nama' => 'SD'],
            ['nama' => 'SLTP'],
            ['nama' => 'SLTA'],
            ['nama' => 'DIPLOMA I/II'],
            ['nama' => 'DIPLOMA IV/STRATA I'],
            ['nama' => 'STRATA II'],
            ['nama' => 'STRATA III'],
        ]);

        // 6. Golongan Darah
        DB::table('golongan_darahs')->insert([
            ['nama' => 'A'],
            ['nama' => 'B'],
            ['nama' => 'AB'],
            ['nama' => 'O'],
            ['nama' => '-'],
        ]);

        // 7. Hubungan Keluarga
        DB::table('hubungan_keluargas')->insert([
            ['nama' => 'Kepala Keluarga'],
            ['nama' => 'Anak'],
            ['nama' => 'Cucu'],
            ['nama' => 'Istri'],
            ['nama' => 'Menantu'],
            ['nama' => 'Suami'],
            ['nama' => 'Saudara'],
            ['nama' => 'Kakak'],
            ['nama' => 'Adik'],
            ['nama' => 'Lainnya'],
        ]);

        // 8. Status Kependudukan
        DB::table('status_kependudukans')->insert([
            ['nama' => 'Warga Asli'],
            ['nama' => 'Pendatang'],
            ['nama' => 'Sementara'],
            ['nama' => 'Pindah'],
            ['nama' => 'Meninggal'],
        ]);

        // 9. Status Khusus
        DB::table('status_khususes')->insert([
            ['nama' => 'Disabilitas'],
            ['nama' => 'Lansia'],
            ['nama' => 'Ibu Hamil'],
            ['nama' => 'Balita'],
            ['nama' => 'Lainnya'],
        ]);
    }
}
