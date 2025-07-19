<?php

namespace App\Exports;

use App\Models\PenerimaBantuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenerimaBantuanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $kategoriBantuanId;

    public function __construct(int $kategoriBantuanId)
    {
        $this->kategoriBantuanId = $kategoriBantuanId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil data penerima bantuan yang sudah difilter oleh Global Scope
        return PenerimaBantuan::with('warga.kartuKeluarga', 'warga.rt', 'warga.rw', 'kartuKeluarga.kepalaKeluarga', 'kartuKeluarga.rt', 'kartuKeluarga.rw', 'diajukanOleh', 'disetujuiOleh')
            ->where('kategori_bantuan_id', $this->kategoriBantuanId)
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No.',
            'Nama Penerima',
            'NIK / Nomor KK',
            'RW',
            'RT',
            'Tanggal Menerima',
            'Keterangan',
            'Status Permohonan',
            'Diajukan Oleh',
            'Diverifikasi Oleh',
            'Tanggal Verifikasi',
            'Catatan Verifikasi',
        ];
    }

    /**
     * @param mixed $penerima
     * @return array
     */
    public function map($penerima): array
    {
        $namaPenerima = '';
        $nikOrKk = '';
        $rw = '';
        $rt = '';

        if ($penerima->warga) {
            $namaPenerima = $penerima->warga->nama_lengkap;
            $nikOrKk = $penerima->warga->nik;
            $rw = $penerima->warga->rw->nomor_rw ?? '-';
            $rt = $penerima->warga->rt->nomor_rt ?? '-';
        } elseif ($penerima->kartuKeluarga) {
            $namaPenerima = 'KK: ' . $penerima->kartuKeluarga->nomor_kk . ' (Kepala: ' . ($penerima->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-') . ')';
            $nikOrKk = $penerima->kartuKeluarga->nomor_kk;
            $rw = $penerima->kartuKeluarga->rw->nomor_rw ?? '-';
            $rt = $penerima->kartuKeluarga->rt->nomor_rt ?? '-';
        }

        return [
            $penerima->id, // Menggunakan ID sebagai nomor urut sementara, bisa diganti dengan index
            $namaPenerima,
            $nikOrKk,
            $rw,
            $rt,
            $penerima->tanggal_menerima->format('d M Y'),
            $penerima->keterangan ?? '-',
            $penerima->status_permohonan,
            $penerima->diajukanOleh->name ?? '-',
            $penerima->disetujuiOleh->name ?? '-',
            $penerima->tanggal_verifikasi ? $penerima->tanggal_verifikasi->format('d M Y H:i') : '-',
            $penerima->catatan_persetujuan_penolakan ?? '-',
        ];
    }
}
