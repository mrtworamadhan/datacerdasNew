<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\DataKesehatanAnak;
use App\Models\KartuKeluarga;
use App\Models\PemeriksaanAnak;
use App\Models\PenerimaBantuan;
use App\Models\KategoriBantuan;
use App\Models\SuratSetting;
use App\Models\Warga;
use App\Models\PengajuanSurat;
use App\Models\Lembaga;
use App\Models\Kegiatan;
use App\Models\Fasum;
use App\Models\Aset;
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

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        if ($user->hasRole('kepala_desa')) {
            $baseWargaQuery = Warga::withoutGlobalScopes()->where('desa_id', $desa->id);
            $baseKkQuery = KartuKeluarga::withoutGlobalScopes()->where('desa_id', $desa->id);
            $baseWargaHidupQuery = Warga::query()
            ->whereHas('statusKependudukan', fn($q) => $q->where('nama', '!=', 'Meninggal'));

            $viewData['stats'] = [
                'jumlahWarga' => (clone $baseWargaHidupQuery)->count(),
                'jumlahKk' => KartuKeluarga::whereIn('id', (clone $baseWargaHidupQuery)->pluck('kartu_keluarga_id'))->count(),
                // Rekap Aset
                'total_aset' => Aset::withoutGlobalScopes()->where('desa_id', $desa->id)->count(),
                // Rekap Fasum
                'fasum_baik' => Fasum::withoutGlobalScopes()->where('desa_id', $desa->id)->where('status_kondisi', 'Baik')->count(),
                'fasum_sedang' => Fasum::withoutGlobalScopes()->where('desa_id', $desa->id)->where('status_kondisi', 'Sedang')->count(),
                'fasum_rusak' => Fasum::withoutGlobalScopes()->where('desa_id', $desa->id)->where('status_kondisi', 'Rusak')->count(),
                // Rekap Kesehatan Anak
                'anak_stunting' => PemeriksaanAnak::whereHas('warga', function($q) use ($desa) {
                    $q->where('desa_id', $desa->id);
                })->where('status_stunting', 'like', '%Stunting%')->distinct('data_kesehatan_anak_id')->count(),

                'anak_wasting' => PemeriksaanAnak::whereHas('warga', function($q) use ($desa) {
                    $q->where('desa_id', $desa->id);
                })->where('status_wasting', 'like', '%Kurang%')->distinct('data_kesehatan_anak_id')->count(),

                'anak_underweight' => PemeriksaanAnak::whereHas('warga', function($q) use ($desa) {
                    $q->where('desa_id', $desa->id);
                })->where('status_underweight', 'like', '%Kurang%')->distinct('data_kesehatan_anak_id')->count(),
                // Rekap Demografi Bulan Ini
                'warga_lahir_bulan_ini' => (clone $baseWargaQuery)->whereMonth('tanggal_lahir', $bulanIni)->whereYear('tanggal_lahir', $tahunIni)->count(),
                'warga_meninggal_bulan_ini' => (clone $baseWargaQuery)->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Meninggal'))->whereMonth('updated_at', $bulanIni)->whereYear('updated_at', $tahunIni)->count(),
                'warga_pindah_bulan_ini' => (clone $baseWargaQuery)->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Pindah'))->whereMonth('updated_at', $bulanIni)->whereYear('updated_at', $tahunIni)->count(),
                'warga_datang_bulan_ini' => (clone $baseWargaQuery)->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Pendatang'))->whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->count(),
                'warga_sementara' => (clone $baseWargaQuery)->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Sementara'))->count(),
                // --- AKHIR PERBAIKAN & PENAMBAHAN ---                // Rekap Bantuan
                'total_penerima_bantuan' => PenerimaBantuan::withoutGlobalScopes()->where('desa_id', $desa->id)->where('status_permohonan', 'Disetujui')->count(),
                'jumlah_janda' => (clone $baseKkQuery)->whereHas('kepalaKeluarga', function ($q) {
                    $q->where('jenis_kelamin', 'Perempuan')->whereHas('statusPerkawinan', fn($sp) => $sp->whereIn('nama', ['Cerai Hidup', 'Cerai Mati']));
                })->count(),
                'jumlah_yatim' => (clone $baseWargaQuery)->whereHas('hubunganKeluarga', fn($q) => $q->where('nama', 'Anak'))
                                        ->whereHas('kartuKeluarga.kepalaKeluarga', function($q) {
                                            $q->where('jenis_kelamin', 'Perempuan')->whereHas('statusPerkawinan', fn($sp) => $sp->where('nama', 'Cerai Mati'));
                                        })->count(),
                'jumlah_piatu' => (clone $baseWargaQuery)->whereHas('hubunganKeluarga', fn($q) => $q->where('nama', 'Anak'))
                                        ->whereHas('kartuKeluarga.kepalaKeluarga', function($q) {
                                            $q->where('jenis_kelamin', 'Laki-laki')->whereHas('statusPerkawinan', fn($sp) => $sp->where('nama', 'Cerai Mati'));
                                        })->count(),
                // Rekap Klasifikasi Keluarga
                'klasifikasi_keluarga' => KartuKeluarga::withoutGlobalScopes()->where('desa_id', $desa->id)
                    ->select('klasifikasi', DB::raw('count(*) as total'))
                    ->groupBy('klasifikasi')
                    ->pluck('total', 'klasifikasi'),
            ];
        }

        if ($user->hasRole('kader_posyandu')) {
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
        if ($user->hasRole('admin_rw') || $user->hasRole('admin_rt')) {
            
            // 1. BUAT QUERY DASAR YANG BERSIH UNTUK SEMUA WARGA HIDUP DI WILAYAHNYA
            $baseWargaHidupQuery = Warga::query()->whereHas('statusKependudukan', fn($q) => $q->where('nama', '!=', 'Meninggal'|'Pindah'));
            if ($user->hasRole('admin_rt')) {
                $baseWargaHidupQuery->where('rt_id', $user->rt_id);
            } else { // admin_rw
                $baseWargaHidupQuery->where('rw_id', $user->rw_id);
            }

            // 2. DAPATKAN ID PENTING DARI QUERY DASAR (INI AKAN KITA PAKAI BERULANG KALI)
            $wargaIdsInArea = (clone $baseWargaHidupQuery)->pluck('id');
            $kkIdsInArea = (clone $baseWargaHidupQuery)->pluck('kartu_keluarga_id')->unique()->filter();
            $baseKkQuery = KartuKeluarga::whereIn('id', $kkIdsInArea);

            // 3. HITUNG SEMUA STATISTIK BERDASARKAN DATA BERSIH INI
            $viewData['jumlahWarga'] = $wargaIdsInArea->count();
            $viewData['jumlahKk'] = $kkIdsInArea->count();
            $viewData['jumlahAnakBalita'] = (clone $baseWargaHidupQuery)->where('tanggal_lahir', '>=', now()->subYears(5))->count();
            $viewData['jumlahBelumVerifikasi'] = (clone $baseWargaHidupQuery)->where('status_data', 'Data Sementara')->count();
            $viewData['jumlahTidakLengkap'] = (clone $baseWargaHidupQuery)->where(function ($query) {
                $query->whereNull('nik')->orWhereNull('tempat_lahir')->orWhereNull('alamat_lengkap')
                      ->orWhereNull('agama_id')->orWhereNull('status_perkawinan_id')->orWhereNull('pekerjaan_id')
                      ->orWhereNull('pendidikan_id')->orWhereNull('hubungan_keluarga_id')
                      ->orWhereNull('nama_ayah_kandung')->orWhereNull('nama_ibu_kandung');
            })->count();
            
            $basePemeriksaanQuery = PemeriksaanAnak::whereHas('dataAnak', fn($q) => $q->whereIn('warga_id', $wargaIdsInArea));

            $viewData['anak_stunting_wilayah'] = (clone $basePemeriksaanQuery)->where('status_stunting', 'like', '%Stunting%')->distinct('data_kesehatan_anak_id')->count();
            $viewData['anak_wasting_wilayah'] = (clone $basePemeriksaanQuery)->where('status_wasting', 'like', '%Kurang%')->distinct('data_kesehatan_anak_id')->count();
            $viewData['anak_underweight_wilayah'] = (clone $basePemeriksaanQuery)->where('status_underweight', 'like', '%Kurang%')->distinct('data_kesehatan_anak_id')->count();

            $viewData['warga_lahir_bulan_ini'] = (clone $baseWargaHidupQuery)->whereMonth('tanggal_lahir', $bulanIni)->whereYear('tanggal_lahir', $tahunIni)->count();
            $viewData['warga_meninggal_bulan_ini'] = (clone $baseWargaHidupQuery)->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Meninggal'))->whereMonth('updated_at', $bulanIni)->whereYear('updated_at', $tahunIni)->count();
            $viewData['jumlah_janda'] = (clone $baseKkQuery)->whereHas('kepalaKeluarga', fn($q) => $q->where('jenis_kelamin', 'Perempuan')->whereHas('statusPerkawinan', fn($sp) => $sp->whereIn('nama', ['Cerai Hidup', 'Cerai Mati'])))->count();
            $viewData['jumlah_yatim'] = (clone $baseWargaHidupQuery)->whereHas('hubunganKeluarga', fn($q) => $q->where('nama', 'Anak'))->whereHas('kartuKeluarga.kepalaKeluarga', fn($q) => $q->where('jenis_kelamin', 'Perempuan')->whereHas('statusPerkawinan', fn($sp) => $sp->where('nama', 'Cerai Mati')))->count();
            $viewData['jumlah_piatu'] = (clone $baseWargaHidupQuery)->whereHas('hubunganKeluarga', fn($q) => $q->where('nama', 'Anak'))->whereHas('kartuKeluarga.kepalaKeluarga', fn($q) => $q->where('jenis_kelamin', 'Laki-laki')->whereHas('statusPerkawinan', fn($sp) => $sp->where('nama', 'Cerai Mati')))->count();
            $viewData['klasifikasiWarga'] = (clone $baseKkQuery)->select('klasifikasi', DB::raw('count(*) as total'))->groupBy('klasifikasi')->pluck('total', 'klasifikasi');
            
            // 4. PERBAIKAN QUERY PENERIMA BANTUAN
            $kategoriBantuan = KategoriBantuan::withoutGlobalScopes()
                                           ->where('desa_id', $desa->id)
                                           ->get();

            $penerimaBantuanData = PenerimaBantuan::withoutGlobalScopes()
                // 2. Tambahkan filter desa_id secara manual karena scope-nya kita matikan
                ->where('desa_id', $desa->id)
                // 3. Terapkan filter yang benar melalui relasi
                ->where('status_permohonan', 'disetujui')
                ->where(function ($query) use ($wargaIdsInArea, $kkIdsInArea) {
                    $query->whereIn('warga_id', $wargaIdsInArea) // Filter berdasarkan warga di wilayah
                        ->orWhereIn('kartu_keluarga_id', $kkIdsInArea); // ATAU filter berdasarkan KK di wilayah
                })
                // 4. Eager load relasi TANPA global scope-nya
                ->with(['kategoriBantuan' => function ($query) {
                    $query->withoutGlobalScopes();
                }])
                ->get()
                ->groupBy('kategoriBantuan.nama_kategori')
                ->map(fn ($group) => $group->count());
            // --- AKHIR PERBAIKAN ---

            $viewData['penerimaBantuan'] = $penerimaBantuanData; 
            $viewData['bantuanDibuka'] = KategoriBantuan::withoutGlobalScopes()
                                           ->where('desa_id', $desa->id) // Tambahkan filter desa_id secara manual
                                           ->where('is_active_for_submission', 1)
                                           ->get();
        }

        return view('portal.dashboard', $viewData);
    }
}
