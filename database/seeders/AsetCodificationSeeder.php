<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AsetGolongan;
use App\Models\AsetBidang;
use App\Models\AsetKelompok;
use App\Models\AsetSubKelompok;
use App\Models\AsetSubSubKelompok;

class AsetCodificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AsetSubSubKelompok::truncate();
        AsetSubKelompok::truncate();
        AsetKelompok::truncate();
        AsetBidang::truncate();
        AsetGolongan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Memulai impor kodifikasi aset dari file CSV...');
        
        $pad2 = fn($kode) => str_pad($kode, 2, '0', STR_PAD_LEFT);
        $pad3 = fn($kode) => str_pad($kode, 3, '0', STR_PAD_LEFT);

        // 1. Impor Golongan
        $this->importFile(database_path('seeders/data/aset_codification/golongan.csv'), function($row) use ($pad2) {
            AsetGolongan::create(['kode_golongan' => $pad2($row[0]), 'nama_golongan' => $row[1]]);
        });
        $this->command->line('Golongan diimpor.');

        // 2. Impor Bidang
        $this->importFile(database_path('seeders/data/aset_codification/bidang.csv'), function($row) use ($pad2) {
            $golongan = AsetGolongan::where('kode_golongan', $pad2($row[0]))->first();
            if ($golongan) {
                AsetBidang::create(['aset_golongan_id' => $golongan->id, 'kode_bidang' => $pad2($row[1]), 'nama_bidang' => $row[2]]);
            }
        });
        $this->command->line('Bidang diimpor.');

        // 3. Impor Kelompok
        $this->importFile(database_path('seeders/data/aset_codification/kelompok.csv'), function($row) use ($pad2) {
            $bidang = AsetBidang::whereHas('golongan', fn($q) => $q->where('kode_golongan', $pad2($row[0])))->where('kode_bidang', $pad2($row[1]))->first();
            if ($bidang) {
                AsetKelompok::create(['aset_bidang_id' => $bidang->id, 'kode_kelompok' => $pad2($row[2]), 'nama_kelompok' => $row[3]]);
            }
        });
        $this->command->line('Kelompok diimpor.');

        // 4. Impor Sub Kelompok
        $this->importFile(database_path('seeders/data/aset_codification/sub_kelompok.csv'), function($row) use ($pad2) {
            $kelompok = AsetKelompok::whereHas('bidang.golongan', fn($q) => $q->where('kode_golongan', $pad2($row[0])))
                ->whereHas('bidang', fn($q) => $q->where('kode_bidang', $pad2($row[1])))
                ->where('kode_kelompok', $pad2($row[2]))->first();
            if ($kelompok) {
                AsetSubKelompok::create(['aset_kelompok_id' => $kelompok->id, 'kode_sub_kelompok' => $pad2($row[3]), 'nama_sub_kelompok' => $row[4]]);
            }
        });
        $this->command->line('Sub Kelompok diimpor.');
        
        // 5. Impor Sub Sub Kelompok
        $this->importFile(database_path('seeders/data/aset_codification/sub_sub_kelompok.csv'), function($row) use ($pad2, $pad3) {
            $subKelompok = AsetSubKelompok::whereHas('kelompok.bidang.golongan', fn($q) => $q->where('kode_golongan', $pad2($row[0])))
                ->whereHas('kelompok.bidang', fn($q) => $q->where('kode_bidang', $pad2($row[1])))
                ->whereHas('kelompok', fn($q) => $q->where('kode_kelompok', $pad2($row[2])))
                ->where('kode_sub_kelompok', $pad2($row[3]))->first();
            if ($subKelompok) {
                AsetSubSubKelompok::create(['aset_sub_kelompok_id' => $subKelompok->id, 'kode_sub_sub_kelompok' => $pad3($row[4]), 'nama_sub_sub_kelompok' => $row[5]]);
            }
        });
        $this->command->line('Sub Sub Kelompok diimpor.');

        $this->command->info('✅ Impor kodifikasi aset selesai.');
    }

    /**
     * Helper function to read CSV files with semicolon delimiter.
     */
    private function importFile(string $path, callable $callback)
    {
        if (!file_exists($path)) {
            $this->command->error("File tidak ditemukan: {$path}");
            return;
        }
        $handle = fopen($path, 'r');
        fgetcsv($handle, 1000, ';'); // Skip header, use semicolon
        while (($row = fgetcsv($handle, 1000, ';')) !== false) { // Use semicolon
            $callback($row);
        }
        fclose($handle);
    }
}