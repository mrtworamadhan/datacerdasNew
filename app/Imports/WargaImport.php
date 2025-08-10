<?php

namespace App\Imports;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\Rw;
use App\Models\Rt;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // <-- 1. Tambahkan ini
use Maatwebsite\Excel\Validators\Failure; 
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WargaImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    private $desa;
    private $rws;
    private $rts;
    private $kartuKeluargaSaatIni = null; // Menyimpan KK yang sedang diproses
    public $errors = [];
    public $successRowCount = 0;

    public function __construct()
    {
        $this->desa = Auth::user()->desa;
        $this->rws = Rw::where('desa_id', $this->desa->id)->get()->keyBy('nomor_rw');
        $this->rts = Rt::whereIn('rw_id', $this->rws->pluck('id'))->get();
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function() use ($rows) {
            $rowNumber = 1; // Mulai dari 1 (karena baris header sudah dilewati oleh WithHeadingRow)

            foreach ($rows as $row) {
                $rowNumber++;
                try {
                    // Cek jika ini adalah Kepala Keluarga, proses KK-nya dulu
                    if (strtolower($row['hubungan_keluarga']) === 'kepala keluarga') {
                        $rw = $this->rws->get(str_pad($row['rw'], 2, '0', STR_PAD_LEFT));
                        $rt = $rw ? $this->rts->where('rw_id', $rw->id)->where('nomor_rt', str_pad($row['rt'], 3, '0', STR_PAD_LEFT))->first() : null;
                        if (!$rt) { continue; }

                        $this->kartuKeluargaSaatIni = KartuKeluarga::updateOrCreate(
                            ['desa_id' => $this->desa->id, 'nomor_kk' => $row['no_kk']],
                            ['alamat_lengkap' => $row['alamat_lengkap'], 'rt_id' => $rt->id, 'rw_id' => $rw->id]
                        );
                    }

                    if (!$this->kartuKeluargaSaatIni || $this->kartuKeluargaSaatIni->nomor_kk != $row['no_kk']) {
                        continue;
                    }

                    // Buat atau update data Warga
                    $warga = Warga::updateOrCreate(
                        ['desa_id' => $this->desa->id, 'nik' => $row['nik']],
                        [
                            'kartu_keluarga_id' => $this->kartuKeluargaSaatIni->id,
                            'nama_lengkap' => $row['nama_lengkap'],
                            'alamat_lengkap' => $row['alamat_lengkap'],
                            'hubungan_keluarga' => $row['hubungan_keluarga'],
                            'jenis_kelamin' => $this->translateJenisKelamin($row['jenis_kelamin']),
                            'agama' => $row['agama'],
                            'tempat_lahir' => $row['tempat_lahir'],
                            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir']),
                            'pendidikan' => $row['pendidikan'],
                            'pekerjaan' => $row['pekerjaan'],
                            'status_perkawinan' => $row['status_perkawinan'],
                            'kewarganegaraan' => 'WNI',
                            'nama_ayah_kandung' => $row['nama_ayah'],
                            'nama_ibu_kandung' => $row['nama_ibu'],
                            'rt_id' => $this->kartuKeluargaSaatIni->rt_id,
                            'rw_id' => $this->kartuKeluargaSaatIni->rw_id,
                        ]
                    );

                    // Update kepala_keluarga_id di KK
                    if (strtolower($row['hubungan_keluarga']) === 'kepala keluarga') {
                        $this->kartuKeluargaSaatIni->update(['kepala_keluarga_id' => $warga->id]);
                    }
                    
                    $this->successRowCount++;

                } catch (\Exception $e) {
                    $this->addError($rowNumber, 'Kesalahan Umum', $e->getMessage());
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            'no_kk' => 'nullable|numeric',
            'nik' => 'nullable|numeric|digits:16',
            'nama_lengkap' => 'nullable|string',
            'hubungan_keluarga' => 'nullable|string',
            'jenis_kelamin' => 'nullable|string',
            'tanggal_lahir' => 'nullable',
        ];
    }
     /**
     * Menangani kegagalan validasi.
     * @param Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Di sinilah kita mencatat semua error validasi
        foreach ($failures as $failure) {
            // Hanya catat error jika barisnya tidak benar-benar kosong
            if ($failure->values()['no_kk'] || $failure->values()['nik'] || $failure->values()['nama_lengkap']) {
                $this->errors[] = "Baris {$failure->row()}: Kolom '{$failure->attribute()}' -> " . implode(', ', $failure->errors());
            }
        }
    }
    
    private function addError(int $rowNumber, string $attribute, string $message)
    {
        $this->errors[] = "Baris {$rowNumber}: Kolom '{$attribute}' -> {$message}";
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
    
    // --- FUNGSI BANTUAN YANG HILANG, SEKARANG SUDAH ADA LAGI ---
    private function translateJenisKelamin($value)
    {
        $input = strtoupper(trim($value));
        if ($input === 'P' || $input === 'PEREMPUAN') {
            return 'Perempuan';
        }
        return 'Laki-laki'; // Default
    }

    private function parseDate($value)
    {
        if (is_numeric($value)) {
            // Menangani format tanggal dari Excel (serial number)
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }
        // Menangani format teks dengan membersihkan spasi
        return \Carbon\Carbon::parse(str_replace(' ', '', $value));
    }

    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 100; }
}