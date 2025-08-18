<?php

namespace App\Imports;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\RW;
use App\Models\RT;
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
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class WargaImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    private $desa;
    private $rws;
    private $rts;
    private $kartuKeluargaSaatIni = null;
    private $validateOnly = false; // <-- Tambahan: Mode validasi-only
    public $errors = [];
    public $successRowCount = 0;

    public function __construct($validateOnly = false, $rwId = null, $rtId = null)
    {
        $this->desa = Auth::user()->desa;
        $this->rws = RW::where('desa_id', $this->desa->id)->get()->keyBy('nomor_rw');
        $this->rts = RT::whereIn('rw_id', $this->rws->pluck('id'))->get();
        $this->validateOnly = $validateOnly;

        $this->rwIdPilihan = $rwId;
        $this->rtIdPilihan = $rtId;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $rowNumber = 1;

            foreach ($rows as $rowNumber => $row) {
            $rowNumber++; 

            try {
                // Ambil RT/RW
                $rw = $this->rwIdPilihan ? $this->rws->firstWhere('id', $this->rwIdPilihan) 
                                        : $this->rws->get(str_pad($row['rw'],2,'0',STR_PAD_LEFT));
                $rt = $this->rtIdPilihan ? $this->rts->firstWhere('id', $this->rtIdPilihan)
                                        : $this->rts->where('rw_id', $rw->id)
                                                    ->where('nomor_rt', str_pad($row['rt'],3,'0',STR_PAD_LEFT))
                                                    ->first();

                if (!$rw || !$rt) {
                    $this->addError($rowNumber, 'RT/RW', 'RT/RW tidak ditemukan.');
                    continue;
                }

                // Validasi tanggal lahir
                if (!$this->validateDate($row['tanggal_lahir'])) {
                    $this->addError($rowNumber, 'tanggal_lahir', "Format tanggal tidak valid: {$row['tanggal_lahir']}");
                    continue;
                }

                // Master data → mapping case-insensitive
                $agamas = DB::table('agamas')->pluck('id', 'nama')->mapWithKeys(fn($id,$nama)=>[strtoupper(trim($nama))=>$id]);
                $statusPerkawinans = DB::table('status_perkawinans')->pluck('id','nama')->mapWithKeys(fn($id,$nama)=>[strtoupper(trim($nama))=>$id]);
                $pendidikans = DB::table('pendidikans')->pluck('id','nama')->mapWithKeys(fn($id,$nama)=>[strtoupper(trim($nama))=>$id]);
                $pekerjaans = DB::table('pekerjaans')->pluck('id','nama')->mapWithKeys(fn($id,$nama)=>[strtoupper(trim($nama))=>$id]);
                $statusKependudukans = DB::table('status_kependudukans')->pluck('id','nama')->mapWithKeys(fn($id,$nama)=>[strtoupper(trim($nama))=>$id]);

                // Kepala keluarga → updateOrCreate KK
                if (strtolower($row['hubungan_keluarga']) === 'kepala keluarga') {
                    $this->kartuKeluargaSaatIni = KartuKeluarga::updateOrCreate(
                        ['desa_id'=>$this->desa->id,'nomor_kk'=>$row['no_kk']],
                        ['alamat_lengkap'=>$row['alamat_lengkap'],'rt_id'=>$rt->id,'rw_id'=>$rw->id]
                    );
                }

                if (!$this->kartuKeluargaSaatIni || $this->kartuKeluargaSaatIni->nomor_kk != $row['no_kk']) {
                    continue;
                }

                // Insert/update Warga
                $warga = Warga::updateOrCreate(
                    ['desa_id'=>$this->desa->id,'nik'=>$row['nik']],
                    [
                        'kartu_keluarga_id'=>$this->kartuKeluargaSaatIni->id,
                        'nama_lengkap'=>$row['nama_lengkap'],
                        'alamat_lengkap'=>$row['alamat_lengkap'],
                        'hubungan_keluarga_id'=>$this->translateHubunganKeluarga($row['hubungan_keluarga']),
                        'jenis_kelamin'=>$this->translateJenisKelamin($row['jenis_kelamin']),
                        'agama_id'=>$agamas[strtoupper(trim($row['agama']))] ?? null,
                        'tempat_lahir'=>$row['tempat_lahir'],
                        'tanggal_lahir'=>$this->parseDate($row['tanggal_lahir']),
                        'pendidikan_id'=>$pendidikans[strtoupper(trim($row['pendidikan']))] ?? null,
                        'pekerjaan_id'=>$pekerjaans[strtoupper(trim($row['pekerjaan']))] ?? null,
                        'status_perkawinan_id'=>$statusPerkawinans[strtoupper(trim($row['status_perkawinan']))] ?? null,
                        'status_kependudukan_id'=>$statusKependudukans[strtoupper(trim($row['status_kependudukan']))] ?? 1,
                        'kewarganegaraan'=>'WNI',
                        'nama_ayah_kandung'=>$row['nama_ayah'],
                        'nama_ibu_kandung'=>$row['nama_ibu'],
                        'rt_id'=>$rt->id,
                        'rw_id'=>$rw->id
                    ]
                );

                if (strtolower($row['hubungan_keluarga'])==='kepala keluarga') {
                    $this->kartuKeluargaSaatIni->update(['kepala_keluarga_id'=>$warga->id]);
                }

                $this->successRowCount++;

            } catch (\Exception $e) {
                $this->addError($rowNumber,'Kesalahan Umum',$e->getMessage());
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

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
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

    private function translateJenisKelamin($value)
    {
        $input = strtoupper(trim($value));
        if ($input === 'P' || $input === 'PEREMPUAN') {
            return 'Perempuan';
        }
        return 'Laki-laki';
    }

    private function translateHubunganKeluarga($value)
    {
        $value = trim(strtolower($value));
        $map = [
            'kepala keluarga' => 1,
            'anak' => 2,
            'cucu' => 3,
            'istri' => 4,
            'menantu' => 5,
            'suami' => 6,
            'saudara' => 7,
            'kakak' => 8,
            'adik' => 9,
            'lainnya' => 10,
        ];

        return $map[$value] ?? null;
    }

    private function validateDate($value)
    {
        try {
            if (is_numeric($value)) {
                ExcelDate::excelToDateTimeObject($value);
            } else {
                Carbon::parse(str_replace(' ', '', $value));
            }
            return true; // valid
        } catch (\Exception $e) {
            return false; // tidak valid
        }
    }


    private function parseDate($value)
    {
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value);
        }
        return Carbon::parse(str_replace(' ', '', $value));
    }

    public function batchSize(): int
    {
        return 100;
    }
    public function chunkSize(): int
    {
        return 100;
    }
}
