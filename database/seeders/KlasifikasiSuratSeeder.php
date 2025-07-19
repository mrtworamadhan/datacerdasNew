<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KlasifikasiSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $klasifikasi = [
            // Kode-kode ini berdasarkan daftar yang kamu berikan
            ['kode' => '474.1', 'deskripsi' => 'Surat Keterangan Domisili'],
            ['kode' => '474.2', 'deskripsi' => 'Surat Keterangan Pindah'],
            ['kode' => '474.3', 'deskripsi' => 'Surat Keterangan Kelahiran'],
            ['kode' => '474.4', 'deskripsi' => 'Surat Keterangan Kematian'],
            ['kode' => '475.1', 'deskripsi' => 'Surat Keterangan Untuk Nikah (N-1, N-2, N-4)'],
            ['kode' => '475.2', 'deskripsi' => 'Surat Tumpang Nikah'],
            ['kode' => '475.3', 'deskripsi' => 'Surat Izin Menikah dari Orang Tua'],
            ['kode' => '475.4', 'deskripsi' => 'Surat Persetujuan Mempelai'],
            ['kode' => '470', 'deskripsi' => 'Surat Keterangan (Umum)'],
            ['kode' => '300', 'deskripsi' => 'Surat Keterangan Kelakuan Baik (SKCK)'],
            ['kode' => '471.1', 'deskripsi' => 'Surat Keterangan Ahli Waris'],
            ['kode' => '474.5', 'deskripsi' => 'Surat Keterangan Janda/Duda'],
            ['kode' => '503', 'deskripsi' => 'Surat Keterangan Usaha (SKU)'],
            ['kode' => '504', 'deskripsi' => 'Surat Keterangan Domisili Usaha'],
            ['kode' => '460', 'deskripsi' => 'Surat Keterangan Tidak Mampu (SKTM)'],
            ['kode' => '331.1', 'deskripsi' => 'Surat Keterangan Kehilangan'],
            ['kode' => '470.1', 'deskripsi' => 'Surat Keterangan Bepergian'],
            ['kode' => '470.2', 'deskripsi' => 'Surat Keterangan Orang yang Sama'],
            ['kode' => '470.3', 'deskripsi' => 'Surat Keterangan Asal-Usul'],
            ['kode' => '470.4', 'deskripsi' => 'Surat Keterangan Tentang Orang Tua'],
            ['kode' => '541', 'deskripsi' => 'Surat Keterangan Jual Beli BBM'],
            // Tambahkan kode lain jika diperlukan
        ];

        // Masukkan data ke database
        DB::table('klasifikasi_surats')->insert($klasifikasi);
    }
}