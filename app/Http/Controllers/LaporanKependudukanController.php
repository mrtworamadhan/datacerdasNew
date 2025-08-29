<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKependudukan;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Exports\PeristiwaKependudukanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanKependudukanController extends Controller
{
    /**
     * Menampilkan halaman laporan peristiwa kependudukan (lahir, meninggal, datang, pindah).
     */
    public function index(Request $request, string $subdomain)
    {
        // 1. Validasi dan pastikan tipe data benar (ini memperbaiki error Carbon)
        $validated = $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ]);

        $bulan = (int) $validated['bulan'];
        $tahun = (int) $validated['tahun'];

        // 2. Query data untuk setiap jenis peristiwa
        $baseQuery = LogKependudukan::with('warga')
            ->whereYear('tanggal_peristiwa', $tahun)
            ->whereMonth('tanggal_peristiwa', $bulan);

        $laporan['lahir'] = (clone $baseQuery)->where('jenis_peristiwa', 'Lahir')->get();
        $laporan['meninggal'] = (clone $baseQuery)->where('jenis_peristiwa', 'Meninggal')->get();
        $laporan['datang'] = (clone $baseQuery)->where('jenis_peristiwa', 'Datang')->get();
        $laporan['pindah'] = (clone $baseQuery)->where('jenis_peristiwa', 'Pindah')->get();

        // 3. Kirim semua data ke view
        return view('admin_desa.laporan.index', compact('laporan', 'bulan', 'tahun'));
    }

    /**
     * Menangani proses ekspor data peristiwa ke Excel.
     */
    public function exportExcel(Request $request, string $subdomain)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ]);
        
        $bulan = (int) $validated['bulan'];
        $tahun = (int) $validated['tahun'];

        $namaFile = 'laporan_peristiwa_kependudukan_' . $bulan . '_' . $tahun . '.xlsx';
        
        return Excel::download(new PeristiwaKependudukanExport($bulan, $tahun), $namaFile);
    }

    public function laporanStatus(Request $request, string $subdomain)
    {
        // 1. Validasi jenis laporan dari URL
        $jenisLaporan = $request->validate([
            'jenis' => 'required|string|in:janda,yatim,piatu'
        ])['jenis'];

        // 2. Siapkan query dasar (sama seperti di WargaController)
        $wargaQuery = Warga::query()
            ->whereHas('statusKependudukan', fn($q) => $q->where('nama', '!=', 'Meninggal'));
        
        $baseKkQuery = KartuKeluarga::query();
        
        $wargaList = collect(); // Siapkan koleksi kosong
        $judul = '';

        // 3. Eksekusi query yang TEPAT sesuai jenis laporan
        // Query ini disalin dari WargaController Anda untuk konsistensi
        switch ($jenisLaporan) {
            case 'janda':
                $judul = 'Janda';
                // Query untuk Janda
                $kkIds = (clone $baseKkQuery)->whereHas('kepalaKeluarga', fn($q) => $q->where('jenis_kelamin', 'Perempuan')
                        ->whereHas('statusPerkawinan', fn($sp) => $sp
                        ->whereIn('nama', ['Cerai Hidup', 'Cerai Mati'])))
                        ->pluck('kepala_keluarga_id');
                $wargaList = Warga::whereIn('id', $kkIds)->get();
                break;
            
            case 'yatim':
                $judul = 'Yatim';
                // Query untuk Yatim
                $wargaList = (clone $wargaQuery)->whereHas('hubunganKeluarga', fn($q) => $q
                        ->where('nama', 'Anak'))
                        ->whereHas('kartuKeluarga.kepalaKeluarga', fn($q) => $q
                        ->where('jenis_kelamin', 'Perempuan')
                        ->whereHas('statusPerkawinan', fn($sp) => $sp
                        ->where('nama', 'Cerai Mati')))
                        ->get();
                break;

            case 'piatu':
                $judul = 'Piatu';
                // Query untuk Piatu
                $wargaList = (clone $wargaQuery)->whereHas('hubunganKeluarga', fn($q) => $q
                        ->where('nama', 'Anak'))
                        ->whereHas('kartuKeluarga.kepalaKeluarga', fn($q) => $q
                        ->where('jenis_kelamin', 'Laki-laki')
                        ->whereHas('statusPerkawinan', fn($sp) => $sp
                        ->where('nama', 'Cerai Mati')))
                        ->get();
                break;
        }

        // 4. Kirim data ke view
        return view('admin_desa.laporan.status_khusus', compact('wargaList', 'judul', 'jenisLaporan'));
    }
}