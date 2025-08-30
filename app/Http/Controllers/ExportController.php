<?php

namespace App\Http\Controllers;

use App\Exports\AnakBermasalahExport;
use App\Models\Posyandu;
use App\Models\Aset;
use App\Models\DataKesehatanAnak;
use App\Exports\AsetsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illmuninate\Support\Facades\Auth;


class ExportController extends Controller
{
    public function exportAnakBermasalah(string $subdomain, Posyandu $posyandu, $tipeMasalah)
    {
        $namaFile = "Daftar Anak {$tipeMasalah} - {$posyandu->nama_posyandu} - " . now()->format('d-m-Y') . '.xlsx';

        return Excel::download(new AnakBermasalahExport($posyandu->id, $tipeMasalah), $namaFile);
    }

    public function exportAnakBermasalahPdf(string $subdomain, Posyandu $posyandu, $tipeMasalah)
    {
        // 1. Otorisasi sederhana
        if (auth()->user()->user_type == 'kader_posyandu' && auth()->user()->posyandu_id != $posyandu->id) {
            abort(403, 'Akses Ditolak');
        }

        // 2. Ambil data (kita gunakan query yang sama persis seperti di class Export Excel)
        $query = DataKesehatanAnak::where('posyandu_id', $posyandu->id)
            ->with(['warga' => fn($q) => $q->with(['kartuKeluarga', 'rt', 'rw']), 'pemeriksaanTerakhir'])
            ->whereHas('pemeriksaanTerakhir', function ($q) use ($tipeMasalah) {
                if ($tipeMasalah == 'stunting') {
                    $q->where('status_stunting', 'like', '%Pendek%');
                } elseif ($tipeMasalah == 'wasting') {
                    $q->where('status_wasting', 'like', '%Kurus%');
                } elseif ($tipeMasalah == 'underweight') {
                    $q->where('status_underweight', 'like', '%Kurang%');
                }
            });
        
        $dataAnak = $query->get();

        // 3. Siapkan data untuk dikirim ke view
        $data = [
            'posyandu' => $posyandu,
            'tipeMasalah' => ucfirst($tipeMasalah), // Mengubah "stunting" menjadi "Stunting"
            'dataAnak' => $dataAnak,
            'tanggalCetak' => now()->isoFormat('D MMMM YYYY'),
        ];

        // 4. Load view, kirim data, dan buat PDF
        $pdf = Pdf::loadView('admin_desa.kesehatan_anak.anak_bermasalah_pdf', $data);
        
        // 5. Download PDF
        $namaFile = "Daftar Anak {$data['tipeMasalah']} - {$posyandu->nama_posyandu} - " . now()->format('d-m-Y') . '.pdf';
        return $pdf->download($namaFile);
    }

    public function exportAsetsExcel(string $subdomain)
    {
        $namaFile = 'Laporan Aset Desa - ' . now()->format('d-m-Y') . '.xlsx';
        return Excel::download(new AsetsExport(), $namaFile);
    }
    public function exportAsetsPdf(string $subdomain)
    {
        $desa = app('tenant');
        $asets = Aset::with('subSubKelompok.subKelompok.kelompok.bidang.golongan')->get();
        $tanggalCetak = now();

        $pdf = Pdf::loadView('admin_desa.laporan.asets_pdf', compact('desa','asets', 'tanggalCetak'));
        $pdf->setPaper('a4', 'landscape'); 

        $namaFile = 'Laporan Aset Desa - ' . now()->format('d-m-Y') . '.pdf';
        return $pdf->download($namaFile);
    }
}