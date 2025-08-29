<?php

namespace App\Exports;

use App\Models\Warga;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WargaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $rwId;

    // Constructor untuk menerima rw_id (opsional)
    public function __construct(int $rwId = null)
    {
        $this->rwId = $rwId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Query dasar untuk mengambil data warga
        $query = Warga::with(['kartuKeluarga', 'rw', 'rt', 'agama', 'statusPerkawinan','golonganDarah', 'pekerjaan', 'pendidikan', 'hubunganKeluarga'])
                      ->orderBy('kartu_keluarga_id'); // Urutkan berdasarkan KK

        // Jika rw_id diberikan, tambahkan filter
        if ($this->rwId) {
            $query->where('rw_id', $this->rwId);
        }

        return $query->get();
    }

    // Mendefinisikan judul kolom di file Excel
    public function headings(): array
    {
        return [
            'No. KK',
            'NIK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Gol. Darah',
            'Status Perkawinan',
            'Alamat',
            'RW',
            'RT',
            'Hubungan Keluarga',
            'Agama',
            'Pendidikan',
            'Pekerjaan',
            'Ayah Kandung',
            'Ibu Kandung',
        ];
    }

    // Memetakan data dari collection ke setiap baris Excel
    public function map($warga): array
    {
        return [
            $warga->kartuKeluarga->nomor_kk ?? '-',
            $warga->nik,
            $warga->nama_lengkap,
            $warga->jenis_kelamin,
            $warga->tempat_lahir,
            $warga->tanggal_lahir->format('d-m-Y'),
            $warga->golonganDarah->nama ?? '-',
            $warga->statusPerkawinan->nama ?? '-',
            $warga->alamat_lengkap,
            $warga->rw->nomor_rw ?? '-',
            $warga->rt->nomor_rt ?? '-',
            $warga->hubunganKeluarga->nama ?? '-',
            $warga->agama->nama ?? '-',
            $warga->pendidikan->nama ?? '-',
            $warga->pekerjaan->nama ?? '-',
            $warga->nama_ayah_kandung,
            $warga->nama_ibu_kandung,
        ];
    }
}