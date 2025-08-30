<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\KartuKeluarga;
use App\Models\KategoriBantuan;
use App\Models\PenerimaBantuan;
use App\Models\PemeriksaanAnak;
use App\Models\SuratSetting;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan daftar Kartu Keluarga berdasarkan klasifikasi kesejahteraan.
     */
    public function showByKesejahteraan(string $subdomain, string $klasifikasi)
    {
        $user = Auth::user();
        $desa = $user->desa;
        $baseWargaQuery = Warga::query();
        if ($user->hasRole('admin_rt')) {
            $baseWargaQuery->where('rt_id', $user->rt_id);
        } elseif ($user->hasRole('admin_rw')) {
            $baseWargaQuery->where('rw_id', $user->rw_id);
        }

        $kkIdsInArea = $baseWargaQuery->pluck('kartu_keluarga_id')->unique()->filter();

        $kartuKeluargas = KartuKeluarga::whereIn('id', $kkIdsInArea)
            ->where('klasifikasi', $klasifikasi)
            ->with('kepalaKeluarga') // Eager loading untuk performa
            ->paginate(12);

        return view('portal.laporan.show_kk', [
            'judul' => "Keluarga " . $klasifikasi,
            'kartuKeluargas' => $kartuKeluargas,
            'desa' => $desa,
            'subdomain' => $subdomain,
        ]);
    }

    /**
     * Menampilkan daftar Warga berdasarkan jenis bantuan yang diterima.
     */
    public function showByBantuan(string $subdomain, string $nama_kategori)
    {
        $user = Auth::user();
        $desa = $user->desa;

        // 1. Ambil data kategori bantuan untuk mendapatkan deskripsinya
        $kategori = KategoriBantuan::withoutGlobalScopes()
            ->where('desa_id', $desa->id)
            ->where('nama_kategori', $nama_kategori)
            ->firstOrFail(); // Gunakan firstOrFail untuk otomatis 404 jika tidak ditemukan

        // 2. Siapkan query dasar untuk warga di wilayah RT/RW
        $baseWargaQuery = Warga::query();
        if ($user->hasRole('admin_rt')) {
            $baseWargaQuery->where('rt_id', $user->rt_id);
        } elseif ($user->hasRole('admin_rw')) {
            $baseWargaQuery->where('rw_id', $user->rw_id);
        }

        $wargaIdsInArea = (clone $baseWargaQuery)->pluck('id');
        $kkIdsInArea = (clone $baseWargaQuery)->pluck('kartu_keluarga_id')->unique()->filter();

        // 3. Cari penerima bantuan yang cocok dengan kriteria
        $penerimaBantuan = PenerimaBantuan::withoutGlobalScopes()
            ->where('desa_id', $desa->id)
            ->where('status_permohonan', 'disetujui')
            ->whereHas('kategoriBantuan', function ($q) use ($nama_kategori) {
                $q->withoutGlobalScopes()->where('nama_kategori', $nama_kategori);
            })
            ->where(function ($query) use ($wargaIdsInArea, $kkIdsInArea) {
                $query->whereIn('warga_id', $wargaIdsInArea)
                    ->orWhereIn('kartu_keluarga_id', $kkIdsInArea);
            })
            ->with(['warga.kartuKeluarga', 'kartuKeluarga.kepalaKeluarga'])
            ->paginate(12);

        // 4. Kirim semua data yang dibutuhkan ke view
        return view('portal.laporan.show_warga', [
            'judul' => "Penerima Bantuan " . $kategori->nama_kategori,
            'deskripsi' => $kategori->deskripsi,
            'penerimaBantuan' => $penerimaBantuan,
            'desa' => $desa,
            'subdomain' => $subdomain,
        ]);
    }
    public function showBelumVerifikasi(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa;
        $baseWargaQuery = $this->getBaseWargaQuery($user);

        $wargas = (clone $baseWargaQuery)
            ->where('status_data', 'Data Sementara')
            ->with('kartuKeluarga') // Eager load untuk efisiensi
            ->paginate(15);

        return view('portal.laporan.show_warga_list', [
            'judul' => 'Warga Belum Terverifikasi',
            'deskripsi' => 'Berikut adalah daftar warga yang datanya ditambahkan oleh Kader Posyandu dan perlu dilengkapi (NIK, No. KK, dll).',
            'wargas' => $wargas,
            'subdomain' => $subdomain,
            'desa' => $desa
        ]);
    }

    /**
     * Menampilkan daftar warga yang data pentingnya masih kosong.
     */
    public function showTidakLengkap(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa;
        $baseWargaQuery = $this->getBaseWargaQuery($user);

        $wargas = (clone $baseWargaQuery)
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
            })
            ->with('kartuKeluarga')
            ->paginate(15);

        return view('portal.laporan.show_warga_list', [
            'judul' => 'Warga Data Tidak Lengkap',
            'deskripsi' => 'Berikut adalah daftar warga yang data pentingnya (seperti NIK, tempat lahir, dll) masih kosong dan perlu segera dilengkapi.',
            'wargas' => $wargas,
            'subdomain' => $subdomain,
            'desa' => $desa
        ]);
    }

    // Helper function untuk DRY
    private function getBaseWargaQuery($user)
    {
        $query = Warga::query()
            ->whereHas('statusKependudukan', fn($q) => $q->where('nama', '!=', 'Meninggal'));
        if ($user->hasRole('admin_rt')) {
            $query->where('rt_id', $user->rt_id);
        } elseif ($user->hasRole('admin_rw')) {
            $query->where('rw_id', $user->rw_id);
        }
        return $query;
    }

    public function showKesehatanAnak(string $subdomain, Request $request)
    {
        $user = Auth::user();
        $desa = $user->desa;

        // Query dasar untuk semua pemeriksaan di desa ini
        $query = PemeriksaanAnak::whereHas('warga', function ($q) use ($desa) {
            $q->where('desa_id', $desa->id);
        });

        // Terapkan filter jika ada
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'stunting') {
                $query->where('status_stunting', 'like', '%Stunting%');
            } elseif ($status === 'wasting') {
                $query->where('status_wasting', 'like', '%Kurang%');
            } elseif ($status === 'underweight') {
                $query->where('status_underweight', 'like', '%Kurang%');
            }
        }

        // Ambil data pemeriksaan unik per anak, ambil yang terbaru
        $pemeriksaans = $query->with('warga.kartuKeluarga')
            ->latest('tanggal_pemeriksaan')
            ->get()
            ->unique('data_kesehatan_anak_id');


        return view('portal.laporan.kesehatan_anak', compact('pemeriksaans', 'desa'));
    }

    public function showDemografi(string $subdomain, $jenis)
    {
        $user = Auth::user();
        $desa = $user->desa;

        // --- PERBAIKAN UTAMA DIMULAI DI SINI ---
        // 1. Buat Query Dasar yang sudah terfilter sesuai role
        $query = Warga::query();
        if ($user->hasRole('admin_rt')) {
            $query->where('rt_id', $user->rt_id);
        } elseif ($user->hasRole('admin_rw')) {
            $query->where('rw_id', $user->rw_id);
        }
        // Jika yang login Kepala Desa atau role lain, tidak ada filter wilayah awal

        $judul = 'Data Warga';
        $deskripsi = 'Berikut adalah daftar warga berdasarkan kategori yang dipilih.';
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // 2. Logika switch case sekarang akan bekerja di atas query yang sudah terfilter
        switch ($jenis) {
            case 'lahir':
                $judul = 'Daftar Kelahiran Bulan Ini';
                $query->whereMonth('tanggal_lahir', $bulanIni)->whereYear('tanggal_lahir', $tahunIni);
                break;
            case 'meninggal':
                $judul = 'Daftar Kematian Bulan Ini';
                $query->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Meninggal'))
                    ->whereMonth('updated_at', $bulanIni)->whereYear('updated_at', $tahunIni);
                break;
            // ... (case lainnya tetap sama persis) ...
            case 'yatim':
                $judul = 'Daftar Anak Yatim';
                $query->whereHas('hubunganKeluarga', fn($q) => $q->where('nama', 'Anak'))
                    ->whereHas('kartuKeluarga.kepalaKeluarga', function ($q) {
                        $q->where('jenis_kelamin', 'Perempuan')->whereHas('statusPerkawinan', fn($sp) => $sp->where('nama', 'Cerai Mati'));
                    });
                break;
            case 'piatu':
                $judul = 'Daftar Anak Piatu';
                $query->whereHas('hubunganKeluarga', fn($q) => $q->where('nama', 'Anak'))
                    ->whereHas('kartuKeluarga.kepalaKeluarga', function ($q) {
                        $q->where('jenis_kelamin', 'Laki-laki')->whereHas('statusPerkawinan', fn($sp) => $sp->where('nama', 'Cerai Mati'));
                    });
                break;
            case 'janda':
                $judul = 'Daftar Kepala Keluarga Janda';
                $kkIds = KartuKeluarga::whereHas('kepalaKeluarga', function ($q) {
                    $q->where('jenis_kelamin', 'Perempuan')->whereHas('statusPerkawinan', fn($sp) => $sp->whereIn('nama', ['Cerai Hidup', 'Cerai Mati']));
                });
                // Tambahkan filter wilayah juga untuk query KK
                if ($user->hasRole('admin_rt')) {
                    $kkIds->where('rt_id', $user->rt_id);
                } elseif ($user->hasRole('admin_rw')) {
                    $kkIds->where('rw_id', $user->rw_id);
                }
                $query->whereIn('kartu_keluarga_id', $kkIds->pluck('id'))->whereHas('hubunganKeluarga', fn($q) => $q->where('nama', 'Kepala Keluarga'));
                break;
            case 'pindah':
                $judul = 'Daftar Warga Pindah Bulan Ini';
                $query->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Pindah'))->whereMonth('updated_at', $bulanIni)->whereYear('updated_at', $tahunIni);
                break;
            case 'datang':
                $judul = 'Daftar Warga Datang Bulan Ini';
                $query->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Pendatang'))->whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni);
                break;
            case 'sementara':
                $judul = 'Daftar Warga Domisili Sementara';
                $query->whereHas('statusKependudukan', fn($q) => $q->where('nama', 'Sementara'));
                break;
        }
        // --- AKHIR PERBAIKAN ---

        $wargas = $query->with('kartuKeluarga', 'rt', 'rw')->paginate(15);

        // Kita tetap gunakan view yang sama
        return view('portal.laporan.show_warga_list', compact('wargas', 'desa', 'judul', 'deskripsi', 'subdomain'));
    }
}