<?php

namespace App\Exports;

use App\Models\LogKependudukan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PeristiwaKependudukanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bulan;
    protected $tahun;

    public function __construct(int $bulan, int $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil semua log yang relevan dalam satu query
        return LogKependudukan::with('warga')
            ->whereIn('jenis_peristiwa', ['Lahir', 'Meninggal', 'Datang', 'Pindah'])
            ->whereYear('tanggal_peristiwa', $this->tahun)
            ->whereMonth('tanggal_peristiwa', $this->bulan)
            ->orderBy('tanggal_peristiwa', 'asc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal Peristiwa',
            'Status',
            'Nama Warga',
            'NIK',
            'Keterangan',
        ];
    }

    /**
     * @param mixed $log
     *
     * @return array
     */
    public function map($log): array
    {
        return [
            \Carbon\Carbon::parse($log->tanggal_peristiwa)->format('d-m-Y'),
            $log->jenis_peristiwa,
            $log->warga->nama_lengkap ?? 'N/A',
            $log->warga->nik ?? 'N/A',
            $log->keterangan,
        ];
    }
}