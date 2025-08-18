<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\Warga;
use App\Models\PemeriksaanAnak;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PosyanduReportController extends Controller
{
    public function generatePdf(string $subdomain, Posyandu $posyandu, $bulan, $tahun)
    {
        // 1. DATA DASAR & PROFIL
        $user = Auth::user();
        $desa = $user->desa;

        $posyandu->load('rws', 'kaders');
        $periode = Carbon::createFromDate($tahun, $bulan)->isoFormat('MMMM YYYY');

        // 2. DATA PARTISIPASI
        $totalBalitaDiWilayah = Warga::where('rw_id', $posyandu->rw_id)
                                ->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) < 60')
                                ->count();
        
        $pemeriksaanBulanIni = PemeriksaanAnak::with('warga')
            ->where('posyandu_id', $posyandu->id)
            ->whereMonth('tanggal_pemeriksaan', $bulan)
            ->whereYear('tanggal_pemeriksaan', $tahun)
            ->get();
        
        $jumlahHadir = $pemeriksaanBulanIni->count();
        $jumlahTidakHadir = $totalBalitaDiWilayah - $jumlahHadir;

        // 3. DAFTAR ANAK DENGAN PERHATIAN KHUSUS (dari anak yang hadir)
        $daftarAnakStunting = $pemeriksaanBulanIni->filter(fn($p) => str_contains($p->status_stunting, 'Stunting'));
        $daftarAnakWasting = $pemeriksaanBulanIni->filter(fn($p) => str_contains($p->status_wasting, 'Kurang'));
        $daftarAnakUnderweight = $pemeriksaanBulanIni->filter(fn($p) => str_contains($p->status_underweight, 'Kurang'));
        
        // 4. STATISTIK KESELURUHAN (dihitung dari daftar di atas)
        $stats = [
            'stunting' => $daftarAnakStunting->count(),
            'wasting' => $daftarAnakWasting->count(),
            'underweight' => $daftarAnakUnderweight->count(),
            'dapat_vitamin_a' => $pemeriksaanBulanIni->where('dapat_vitamin_a', true)->count(),
            'dapat_obat_cacing' => $pemeriksaanBulanIni->where('dapat_obat_cacing', true)->count(),
        ];
        
        // 5. GABUNGKAN SEMUA DATA UNTUK DIKIRIM KE VIEW
        $data = [
            'desa' => $desa,
            'posyandu' => $posyandu,
            'periode' => $periode,
            'tanggalCetak' => now()->isoFormat('D MMMM YYYY'),
            'partisipasi' => [
                'total_balita' => $totalBalitaDiWilayah,
                'hadir' => $jumlahHadir,
                'tidak_hadir' => $jumlahTidakHadir > 0 ? $jumlahTidakHadir : 0,
            ],
            'stats' => $stats,
            'daftarAnakStunting' => $daftarAnakStunting,
            'daftarAnakWasting' => $daftarAnakWasting,
            'daftarAnakUnderweight' => $daftarAnakUnderweight,
            'semuaPemeriksaan' => $pemeriksaanBulanIni,
        ];

        $pdf = Pdf::loadView('admin_desa.posyandu.laporan', $data);
        $namaFile = "Laporan Bulanan Posyandu {$posyandu->nama_posyandu} - {$periode}.pdf";
        return $pdf->download($namaFile);
    }
}