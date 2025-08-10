<?php

namespace App\Exports;

use App\Models\DataKesehatanAnak;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // <-- Tambahan 1: Untuk kolom otomatis

class AnakBermasalahExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $posyanduId;
    protected $tipeMasalah;

    public function __construct(int $posyanduId, string $tipeMasalah)
    {
        $this->posyanduId = $posyanduId;
        $this->tipeMasalah = $tipeMasalah;
    }

    /**
     * Query untuk mengambil data dari database.
     */
    public function query()
    {
        // PENYEMPURNAAN: Tambahkan eager loading untuk semua relasi yang dibutuhkan
        $query = DataKesehatanAnak::where('posyandu_id', $this->posyanduId)
            ->with([
                'warga' => function ($query) {
                    $query->with(['kartuKeluarga', 'rt', 'rw']); // Ambil data KK, RT, dan RW dari warga
                }, 
                'pemeriksaanTerakhir'
            ])
            ->whereHas('pemeriksaanTerakhir', function ($q) {
                if ($this->tipeMasalah == 'stunting') {
                    $q->where('status_stunting', 'like', '%Pendek%');
                } elseif ($this->tipeMasalah == 'wasting') {
                    $q->where('status_wasting', 'like', '%Kurus%');
                } elseif ($this->tipeMasalah == 'underweight') {
                    $q->where('status_underweight', 'like', '%Kurang%');
                }
            });

        return $query;
    }

    /**
     * Menentukan judul kolom di file Excel.
     */
    public function headings(): array
    {
        // PENYEMPURNAAN: Tambahkan judul kolom baru
        return [
            'Nama Anak',
            'NIK',
            'No. KK',
            'Alamat',
            'RT',
            'RW',
            'Usia (Bulan)',
            'Jenis Kelamin',
            'Nama Ibu',
            'Status Gizi Terakhir',
            'Z-Score Terakhir',
        ];
    }

    /**
     * Memetakan data dari query ke format baris Excel.
     */
    public function map($anak): array
    {
        $status = '';
        $zscore = '';
        $usiaFormatted = 'N/A'; // Default value

        // Ambil data dari pemeriksaan terakhir
        if ($pemeriksaan = $anak->pemeriksaanTerakhir) {
            if ($this->tipeMasalah == 'stunting') {
                $status = $pemeriksaan->status_stunting;
                $zscore = $pemeriksaan->zscore_tb_u;
            } elseif ($this->tipeMasalah == 'wasting') {
                $status = $pemeriksaan->status_wasting;
                $zscore = $pemeriksaan->zscore_bb_tb;
            } elseif ($this->tipeMasalah == 'underweight') {
                $status = $pemeriksaan->status_underweight;
                $zscore = $pemeriksaan->zscore_bb_u;
            }

            // --- INI PERBAIKANNYA ---
            // Ambil data warga dan tanggal pemeriksaan untuk hitung ulang usia yang akurat
            if ($anak->warga && $pemeriksaan->tanggal_pemeriksaan) {
                $age = \Carbon\Carbon::parse($anak->warga->tanggal_lahir)->diff($pemeriksaan->tanggal_pemeriksaan);
                $usiaBulan = $age->y * 12 + $age->m;
                $usiaHari = $age->d;
                $usiaFormatted = "{$usiaBulan} bulan, {$usiaHari} hari";
            }
        }

        return [
            $anak->warga->nama_lengkap ?? 'N/A',
            "'" . ($anak->warga->nik ?? 'N/A'),
            "'" . ($anak->warga->kartuKeluarga->nomor_kk ?? 'N/A'),
            $anak->warga->alamat_lengkap ?? 'N/A',
            $anak->warga->rt->nomor_rt ?? 'N/A',
            $anak->warga->rw->nomor_rw ?? 'N/A',
            $usiaFormatted, // <-- GUNAKAN VARIABEL BARU
            $anak->warga->jenis_kelamin ?? 'N/A',
            $anak->nama_ibu ?? 'N/A',
            $status,
            $zscore,
        ];
    }
}