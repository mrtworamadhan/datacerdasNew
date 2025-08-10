<?php

namespace App\Exports;

use App\Models\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle; // Untuk nama sheet
use Maatwebsite\Excel\Concerns\WithEvents; // Untuk styling header
use Maatwebsite\Excel\Events\AfterSheet;

class AsetsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Aset::with('subSubKelompok.subKelompok.kelompok.bidang.golongan')->get();
    }

    public function title(): string
    {
        return 'Buku Inventaris Aset Desa';
    }

    public function headings(): array
    {
        // Header sesuai format standar
        return [
            'No Urut',
            'Jenis Barang / Nama Barang',
            'Kode Barang',
            'Nomor Register',
            'Tahun Pembelian',
            'Asal Usul',
            'Harga (Rp.)',
            'Keterangan',
        ];
    }

    public function map($aset): array
    {
        // Ekstrak nomor register dari kode aset
        $kodeParts = explode('.', $aset->kode_aset);
        $nomorRegister = end($kodeParts);

        // Gabungkan beberapa field untuk kolom keterangan
        $keterangan = "Kondisi: {$aset->kondisi}, Lokasi: {$aset->lokasi}, Penanggung Jawab: {$aset->penanggung_jawab}. {$aset->keterangan}";

        return [
            $aset->id, // No Urut bisa menggunakan ID Aset
            $aset->nama_aset,
            $aset->kode_aset,
            $nomorRegister,
            $aset->tahun_perolehan,
            $aset->sumber_dana,
            $aset->nilai_perolehan,
            trim($keterangan),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Tambahkan header utama di atas tabel
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->setCellValue('A1', 'BUKU INVENTARIS ASET DESA');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Styling untuk judul kolom
                $event->sheet->getStyle('A3:H3')->getFont()->setBold(true);
                $event->sheet->getStyle('A3:H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
            },
        ];
    }
}