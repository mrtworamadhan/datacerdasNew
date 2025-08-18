<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\KartuKeluarga;
use App\Models\KategoriBantuan;
use App\Models\PenerimaBantuan;
use App\Models\SuratSetting;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if ($user->isAdminRt()) {
            $baseWargaQuery->where('rt_id', $user->rt_id);
        } elseif ($user->isAdminRw()) {
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
        $kategori = KategoriBantuan::where('desa_id', $desa->id)
                                    ->where('nama_kategori', $nama_kategori)
                                    ->firstOrFail(); // Gunakan firstOrFail untuk otomatis 404 jika tidak ditemukan

        // 2. Siapkan query dasar untuk warga di wilayah RT/RW
        $baseWargaQuery = Warga::query();
        if ($user->isAdminRt()) {
            $baseWargaQuery->where('rt_id', $user->rt_id);
        } elseif ($user->isAdminRw()) {
            $baseWargaQuery->where('rw_id', $user->rw_id);
        }
        
        $wargaIdsInArea = (clone $baseWargaQuery)->pluck('id');
        $kkIdsInArea = (clone $baseWargaQuery)->pluck('kartu_keluarga_id')->unique()->filter();

        // 3. Cari penerima bantuan yang cocok dengan kriteria
        $penerimaBantuan = PenerimaBantuan::query()
            ->where('status_permohonan', 'disetujui')
            // Gunakan relasi untuk mencari berdasarkan nama bantuan
            ->whereHas('kategoriBantuan', fn($q) => $q->where('nama_kategori', $nama_kategori))
            // Logika OR untuk mencari berdasarkan warga ATAU kk
            ->where(function ($query) use ($wargaIdsInArea, $kkIdsInArea) {
                $query->whereIn('warga_id', $wargaIdsInArea)
                      ->orWhereIn('kartu_keluarga_id', $kkIdsInArea);
            })
            // Eager load semua relasi yang dibutuhkan untuk ditampilkan
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
        if ($user->isAdminRt()) {
            $query->where('rt_id', $user->rt_id);
        } elseif ($user->isAdminRw()) {
            $query->where('rw_id', $user->rw_id);
        }
        return $query;
    }
}