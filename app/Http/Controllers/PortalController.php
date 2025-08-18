<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\DataKesehatanAnak;
use App\Models\KartuKeluarga;
use App\Models\PemeriksaanAnak;
use App\Models\PenerimaBantuan;
use App\Models\SuratSetting;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortalController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama untuk portal yang cerdas dan informatif.
     */
    public function dashboard(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa;

        $viewData = [
            'user' => $user,
            'desa' => $desa,
            'subdomain' => $subdomain,
        ];

        // =======================================================
        // SIAPKAN DATA UNTUK KADER POSYANDU
        // =======================================================
        if ($user->isKaderPosyandu()) {
            // Ambil ID semua warga yang dipantau di posyandu ini
             $anakIds = DataKesehatanAnak::where('posyandu_id', Auth::user()->posyandu_id)->pluck('warga_id');
            
            $viewData['jumlahAnakBalita'] = Warga::whereIn('id', $anakIds)
                ->where('tanggal_lahir', '>=', now()->subYears(5))
                ->count();

            // Siapkan data untuk grafik tren gizi
            $trendData = ['labels' => [], 'stunting' => [], 'wasting' => [], 'underweight' => []];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $tahun = $date->year;
                $bulan = $date->month;
                $trendData['labels'][] = $date->isoFormat('MMM');

                $queryBulanan = PemeriksaanAnak::where('posyandu_id', $user->posyandu_id)
                    ->whereYear('tanggal_pemeriksaan', $tahun)
                    ->whereMonth('tanggal_pemeriksaan', $bulan);

                $trendData['stunting'][] = (clone $queryBulanan)->where('status_stunting', 'like', '%Pendek%')->count();
                $trendData['wasting'][] = (clone $queryBulanan)->where('status_wasting', 'like', '%Kurus%')->count();
                $trendData['underweight'][] = (clone $queryBulanan)->where('status_underweight', 'like', '%Kurang%')->count();
            }
            $viewData['trendData'] = $trendData;
        }

        // =======================================================
        // SIAPKAN DATA UNTUK RT / RW
        // =======================================================
        if ($user->isAdminRt() || $user->isAdminRw()) {
            $baseWargaQuery = Warga::query()
            ->whereHas('statusKependudukan', fn($q) => $q->where('nama', '!=', 'Meninggal'));
            if ($user->isAdminRt()) {
                $baseWargaQuery->where('rt_id', $user->rt_id);
            } elseif ($user->isAdminRw()) {
                $baseWargaQuery->where('rw_id', $user->rw_id);
            }

            $viewData['jumlahWarga'] = (clone $baseWargaQuery)->count();
            $viewData['jumlahKk'] = KartuKeluarga::whereIn('id', (clone $baseWargaQuery)->pluck('kartu_keluarga_id'))->count();
            $viewData['jumlahBelumVerifikasi'] = (clone $baseWargaQuery)->where('status_data', 'Data Sementara')->count();
            $viewData['jumlahAnakBalita'] = (clone $baseWargaQuery)->where('tanggal_lahir', '>=', now()->subYears(5))->count();
            $viewData['jumlahTidakLengkap'] = (clone $baseWargaQuery)
                ->where(function ($query) {
                    $query->whereNull('nik')
                        ->orWhereNull('tempat_lahir')
                        ->orWhereNull('alamat_lengkap')
                        ->orWhereNull('agama_id')
                        ->orWhereNull('status_perkawinan_id')
                        ->orWhereNull('pekerjaan_id')
                        ->orWhereNull('pendidikan_id')
                        ->orWhereNull('hubungan_keluarga_id')
                        ->orWhereNull('nama_ayah_kandung')
                        ->orWhereNull('nama_ibu_kandung');
                })->count();
            $kkIdsInArea = (clone $baseWargaQuery)->pluck('kartu_keluarga_id')->unique()->filter();
            
            // 2. Buat query dasar baru yang menargetkan tabel kartu_keluargas
            $baseKkQuery = KartuKeluarga::whereIn('id', $kkIdsInArea);

            // 3. Hitung jumlah berdasarkan kolom 'klasifikasi'
            $viewData['klasifikasiWarga'] = [
                'Pra Sejahtera' => (clone $baseKkQuery)->where('klasifikasi', 'Pra Sejahtera')->count(),
                'Sejahtera I' => (clone $baseKkQuery)->where('klasifikasi', 'Sejahtera I')->count(),
                'Sejahtera II' => (clone $baseKkQuery)->where('klasifikasi', 'Sejahtera II')->count(),
                'Sejahtera III' => (clone $baseKkQuery)->where('klasifikasi', 'Sejahtera III')->count(),
                'Sejahtera III Plus' => (clone $baseKkQuery)->where('klasifikasi', 'Sejahtera III Plus')->count(),
            ];

            // Bantuan Sosial (asumsi nama bantuan di tabel kategori_bantuans)
            $wargaIdsInArea = (clone $baseWargaQuery)->pluck('id');
            $kkIdsInArea = (clone $baseWargaQuery)->pluck('kartu_keluarga_id')->unique()->filter();
            
            $penerimaBantuanData = PenerimaBantuan::query()
                ->where('status_permohonan', 'disetujui')
                ->where(function ($query) use ($wargaIdsInArea, $kkIdsInArea) {
                    $query->whereIn('warga_id', $wargaIdsInArea)
                          ->orWhereIn('kartu_keluarga_id', $kkIdsInArea);
                })
                ->with('kategoriBantuan')
                ->get()
                ->groupBy('kategoriBantuan.nama_kategori')
                ->map(fn ($group) => $group->count());
            
            $viewData['penerimaBantuan'] = $penerimaBantuanData;            

            $viewData['bantuanDibuka'] = $desa->kategoriBantuans()->where('is_active_for_submission', 1)->get();
        }

        return view('portal.dashboard', $viewData);
    }
}
