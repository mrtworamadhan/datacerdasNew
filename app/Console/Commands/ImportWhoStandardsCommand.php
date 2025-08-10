<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportWhoStandardsCommand extends Command
{
    protected $signature = 'import:who-standards';
    protected $description = 'Reads ALL WHO CSV standards and generates a complete PHP array with smart keys.';

    public function handle()
    {
        $this->info('🚀 Memulai impor standar WHO (Versi Final & Cerdas)...');

        $path = storage_path('app/who_standards');
        if (!File::isDirectory($path)) {
            $this->error('Direktori tidak ditemukan: ' . $path);
            return 1;
        }
        $files = File::files($path);
        if (empty($files)) {
            $this->error('Tidak ada file CSV yang ditemukan di ' . $path);
            return 1;
        }

        $data = [
            'HAZ_BOYS' => [], 'HAZ_GIRLS' => [],
            'WAZ_BOYS' => [], 'WAZ_GIRLS' => [],
            'WHZ_BOYS' => [], 'WHZ_GIRLS' => [],
        ];

        foreach ($files as $file) {
            if (strtolower($file->getExtension()) !== 'csv') continue;

            $this->line('Memproses file: ' . $file->getFilename());
            $handle = fopen($file->getPathname(), 'r');
            fgetcsv($handle, 1000, ';'); // Lewati header

            $fileName = strtolower($file->getFilename());
            // Cek apakah file ini berbasis tinggi badan (Length)
            $isLengthBased = str_contains($fileName, 'wfl') || str_contains($fileName, 'wfh');

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                if (count($row) < 4 || empty($row[0])) continue;

                // =================================================================
                // === LOGIKA CERDAS: Kunci berbeda untuk tipe file berbeda ===
                // =================================================================
                if ($isLengthBased) {
                    // Jika file WHZ (wfl/wfh), kunci adalah string desimal
                    $value = (float)str_replace(',', '.', $row[0]);
                    $key = number_format($value, 1, '.', '');
                } else {
                    // Jika file HAZ (lhfa) atau WAZ (wfa), kunci adalah integer hari
                    $key = (int)$row[0];
                }
                // =================================================================

                $l = (float)str_replace(',', '.', $row[1]);
                $m = (float)str_replace(',', '.', $row[2]);
                $s = (float)str_replace(',', '.', $row[3]);
                $dataPoint = ['L' => $l, 'M' => $m, 'S' => $s];
                
                $targetArrayKey = $this->getTargetArrayKey($fileName);
                if ($targetArrayKey) {
                    if (!isset($data[$targetArrayKey][$key])) {
                        $data[$targetArrayKey][$key] = $dataPoint;
                    }
                }
            }
            fclose($handle);
        }

        // Urutkan semua data
        foreach ($data as $key => &$array) {
            uksort($array, 'strnatcmp');
        }

        // Generate kode PHP
        $phpCode = "<?php\n\n// File ini digenerate oleh command import:who-standards pada " . now() . "\n\n";
        foreach ($data as $key => $array) {
            $phpCode .= "private const {$key} = " . var_export($array, true) . ";\n\n";
        }
        
        $outputFile = storage_path('app/who_standards_output.php');
        File::put($outputFile, $phpCode);

        $this->info('✅ Berhasil men-generate kode array PHP untuk SEMUA standar!');
        $this->comment('Silakan salin konten dari "' . $outputFile . '" ke dalam file StuntingCalculatorService.php kamu.');

        return 0;
    }

    private function getTargetArrayKey(string $fileName): ?string
    {
        $isBoys = str_contains($fileName, 'boys');
        $gender = $isBoys ? 'BOYS' : 'GIRLS';

        if (str_contains($fileName, 'lhfa')) return 'HAZ_' . $gender;
        if (str_contains($fileName, 'wfa')) return 'WAZ_' . $gender;
        if (str_contains($fileName, 'wfl') || str_contains($fileName, 'wfh')) return 'WHZ_' . $gender;
        
        return null;
    }
}