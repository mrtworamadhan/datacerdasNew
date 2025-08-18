<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga; // Perbaikan: dari App->Models->Warga menjadi App\Models\Warga
use App\Models\RW;
use App\Models\RT;
use App\Models\Agama;
use App\Models\StatusPerkawinan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\GolonganDarah;
use App\Models\HubunganKeluarga;
use App\Models\StatusKependudukan;
use App\Models\StatusKhusus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KartuKeluargaController extends Controller
{
    /**
     * Display a listing of the Kartu Keluarga for the current desa.
     */
    public function index(Request $request, string $subdomain)
    {
        $user = Auth::user();
        // Check if user has permission to access this module at all
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola Kartu Keluarga.');
        }
        // Global scope 'desa_id_and_area' akan otomatis memfilter berdasarkan user yang login
        // Tidak perlu where() manual di sini karena scope sudah menanganinya
        $search = $request->query('search'); // Ambil query pencarian dari parameter 'search'

        $desaId = $user->desa_id;

        $query = KartuKeluarga::where('desa_id', $desaId)
                            ->with('kepalaKeluarga'); // Eager load kepalaKeluarga

        // Apply RW/RT filtering if user is Admin RW/RT  
        if ($user->isAdminRw() && $user->rw_id) {
            $query->where('rw_id', $user->rw_id);
        }
        if ($user->isAdminRt() && $user->rt_id) {
            $query->where('rt_id', $user->rt_id);
        }

        // Perbaiki logika pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_kk', 'like', '%' . $search . '%')
                ->orWhereHas('kepalaKeluarga', function($q2) use ($search) {
                    $q2->where('nama_lengkap', 'like', '%' . $search . '%');
                });
            });
        }

        // Ambil semua hasil tanpa limit jika ini untuk halaman index, bukan Select2
        $kartuKeluargas = $query->paginate(15); // Pakai paginate untuk halaman index
        // Jika ini untuk Select2, pakai limit seperti sebelumnya
        // $kartuKeluargas = $query->limit(15)->get();

        return view('admin_desa.kartu_keluarga.index', compact('kartuKeluargas'));
    }

    /**
     * Show the form for creating a new Kartu Keluarga.
     */
    public function create(string $subdomain)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola Kartu Keluarga.');
        }

        // Ambil RW dan RT sesuai scope user yang login
        $rws = RW::all(); // Global scope akan memfilter RW sesuai desa/RW user
        $rts = RT::all(); // Global scope akan memfilter RT sesuai desa/RW/RT user

        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
        $agamaOptions              = Agama::pluck('nama', 'id');
        $statusPerkawinanOptions   = StatusPerkawinan::pluck('nama', 'id');
        $pekerjaanOptions          = Pekerjaan::pluck('nama', 'id');
        $pendidikanOptions         = Pendidikan::pluck('nama', 'id');
        $kewarganegaraanOptions     = ['WNI', 'WNA'];
        $golonganDarahOptions      = GolonganDarah::pluck('nama', 'id');
        $hubunganKeluargaOptions   = HubunganKeluarga::pluck('nama', 'id');
        $statusKependudukanOptions = StatusKependudukan::pluck('nama', 'id');
        $statusKhususOptions       = StatusKhusus::pluck('nama', 'id');

        return view('admin_desa.kartu_keluarga.create', compact(
            'rws', 'rts', 'klasifikasiOptions', 'jenisKelaminOptions', 'agamaOptions',
            'statusPerkawinanOptions', 'pekerjaanOptions', 'pendidikanOptions', 'kewarganegaraanOptions',
            'golonganDarahOptions', 'hubunganKeluargaOptions', 'statusKependudukanOptions'
        ));
    }

    /**
     * Store a newly created Kartu Keluarga and its Kepala Keluarga.
     */
    public function store(Request $request,string $subdomain)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melakukan aksi ini.');
        }

        $request->validate([
            'nomor_kk' => 'required|string|max:20|unique:kartu_keluargas,nomor_kk,NULL,id,desa_id,'.$user->desa_id,
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
            'alamat_lengkap_kk' => 'required|string|max:255',
            'klasifikasi' => 'required|in:Pra-Sejahtera,Sejahtera I,Sejahtera II,Sejahtera III,Sejahtera III Plus',

            // Validasi untuk Kepala Keluarga
            'nik_kk' => 'required|string|digits:16|unique:wargas,nik,NULL,id,desa_id,'.$user->desa_id,
            'nama_lengkap_kk' => 'required|string|max:255',
            'tempat_lahir_kk' => 'required|string|max:255',
            'tanggal_lahir_kk' => 'required|date',
            'jenis_kelamin_kk' => 'required|in:Laki-laki,Perempuan',
            'agama_kk' => 'required|string|max:50',
            'status_perkawinan_kk' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan_kk' => 'required|string|max:100',
            'pendidikan_kk' => 'nullable|in:Tidak/Belum Sekolah,SD,SMP,SMA,S1,S2,S3',
            'kewarganegaraan_kk' => 'required|string|max:50',
            'golongan_darah_kk' => 'nullable|string|max:5',
            'alamat_lengkap_warga_kk' => 'required|string|max:255',
            'status_kependudukan_kk' => 'required|in:Warga Asli,Pendatang,Sementara,Pindah,Meninggal',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Kartu Keluarga
            $kartuKeluarga = KartuKeluarga::create([
                'desa_id' => $user->desa_id,
                'nomor_kk' => $request->nomor_kk,
                'rw_id' => $request->rw_id,
                'rt_id' => $request->rt_id,
                'alamat_lengkap' => $request->alamat_lengkap_kk,
                'klasifikasi' => $request->klasifikasi,
            ]);

            // 2. Buat data Warga untuk Kepala Keluarga
            $kepalaKeluarga = Warga::create([
                'desa_id' => $user->desa_id,
                'kartu_keluarga_id' => $kartuKeluarga->id,
                'rw_id' => $request->rw_id,
                'rt_id' => $request->rt_id,
                'nik' => $request->nik_kk,
                'nama_lengkap' => $request->nama_lengkap_kk,
                'tempat_lahir' => $request->tempat_lahir_kk,
                'tanggal_lahir' => $request->tanggal_lahir_kk,
                'jenis_kelamin' => $request->jenis_kelamin_kk,
                'agama' => $request->agama_kk,
                'status_perkawinan' => $request->status_perkawinan_kk,
                'pekerjaan' => $request->pekerjaan_kk,
                'pendidikan' => $request->pendidikan_kk,
                'kewarganegaraan' => $request->kewarganegaraan_kk,
                'golongan_darah' => $request->golongan_darah_kk,
                'alamat_lengkap' => $request->alamat_lengkap_warga_kk,
                'hubungan_keluarga' => 'Kepala Keluarga',
                'status_kependudukan' => $request->status_kependudukan_kk,
            ]);

            // 3. Update Kartu Keluarga dengan ID Kepala Keluarga
            $kartuKeluarga->update(['kepala_keluarga_id' => $kepalaKeluarga->id]);

            DB::commit();
            return redirect()->route('kartu-keluarga.index')->with('success', 'Kartu Keluarga dan Kepala Keluarga berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan Kartu Keluarga: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified Kartu Keluarga.
     */
    public function edit(string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
        // Global scope should handle filtering. This is a safeguard.
        if ($user->isAdminDesa() && $kartuKeluarga->desa_id !== $user->desa_id) {
            abort(403, 'Kartu Keluarga ini bukan milik desa Anda.');
        } elseif ($user->isAdminRw() && ($kartuKeluarga->rw_id !== $user->rw_id || $kartuKeluarga->desa_id !== $user->desa_id)) {
            abort(403, 'Kartu Keluarga ini bukan milik wilayah RW Anda.');
        } elseif ($user->isAdminRt() && ($kartuKeluarga->rt_id !== $user->rt_id || $kartuKeluarga->rw_id !== $user->rw_id || $kartuKeluarga->desa_id !== $user->desa_id)) {
            abort(403, 'Kartu Keluarga ini bukan milik wilayah RT Anda.');
        }

        $rws = RW::all();
        $rts = RT::where('rw_id', $kartuKeluarga->rw_id)->get(); 

        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        $pekerjaanOptions = [
            'Belum / Tidak Bekerja', 'Mengurus Rumah Tangga', 'Pelajar / Mahasiswa', 'Pensiunan',
            'Pegawai Negeri Sipil', 'Tentara Nasional Indonesia', 'Kepolisian RI', 'Perdagangan',
            'Petani / Pekebun', 'Peternak', 'Nelayan / Perikanan', 'Industri', 'Konstruksi',
            'Transportasi', 'Karyawan Swasta', 'Karyawan BUMN', 'Karyawan BUMD', 'Karyawan Honorer',
            'Buruh Harian Lepas', 'Buruh Tani / Perkebunan', 'Buruh Nelayan / Perikanan',
            'Buruh Peternakan', 'Pembantu Rumah Tangga', 'Tukang Cukur', 'Tukang Listrik',
            'Tukang Batu', 'Tukang Kayu', 'Tukang Sol Sepatu', 'Tukang Las / Pandai Besi',
            'Tukang Jahit', 'Penata Rambut', 'Penata Rias', 'Penata Busana', 'Mekanik', 'Tukang Gigi',
            'Seniman', 'Tabib', 'Paraji', 'Perancang Busana', 'Penerjemah', 'Imam Masjid',
            'Pendeta', 'Pastur', 'Wartawan', 'Ustadz / Mubaligh', 'Juru Masak', 'Promotor Acara',
            'Anggota DPR-RI', 'Anggota DPD', 'Anggota BPK', 'Presiden', 'Wakil Presiden',
            'Anggota Mahkamah Konstitusi', 'Anggota Kabinet / Kementerian', 'Duta Besar', 'Gubernur',
            'Wakil Gubernur', 'Bupati', 'Wakil Bupati', 'Walikota', 'Wakil Walikota',
            'Anggota DPRD Provinsi', 'Anggota DPRD Kabupaten', 'Dosen', 'Guru', 'Pilot',
            'Pengacara', 'Notaris', 'Arsitek', 'Akuntan', 'Konsultan', 'Dokter', 'Bidan', 'Perawat',
            'Apoteker', 'Psikiater / Psikolog', 'Penyiar Televisi', 'Penyiar Radio', 'Pelaut',
            'Peneliti', 'Sopir', 'Pialang', 'Paranormal', 'Pedagang', 'Perangkat Desa',
            'Kepala Desa', 'Biarawati', 'Wiraswasta', 'Anggota Lembaga Tinggi', 'Artis', 'Atlit',
            'Chef', 'Manajer', 'Tenaga Tata Usaha', 'Operator', 'Pekerja Pengolahan, Kerajinan',
            'Teknisi', 'Asisten Ahli', 'Lainnya'
        ];
        $pendidikanOptions = ['BLM SEKOLAH','BLM TAMAT SD', 'SD', 'SLTP', 'SLTA', 'DIPLOMA I/II', 'DIPLOMA IV/STRATA I', 'STRATA II', 'STRATA III'];
        
        return view('admin_desa.kartu_keluarga.edit', compact(
            'kartuKeluarga', 'rws', 'rts', 'klasifikasiOptions', 'pekerjaanOptions', 'pendidikanOptions'
        ));
    }

    /**
     * Update the specified Kartu Keluarga.
     */
    public function update(Request $request, string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
        // Global scope should handle filtering. This is a safeguard.
        if ($user->isAdminDesa() && $kartuKeluarga->desa_id !== $user->desa_id) {
            abort(403, 'Kartu Keluarga ini bukan milik desa Anda.');
        } elseif ($user->isAdminRw() && ($kartuKeluarga->rw_id !== $user->rw_id || $kartuKeluarga->desa_id !== $user->desa_id)) {
            abort(403, 'Kartu Keluarga ini bukan milik wilayah RW Anda.');
        } elseif ($user->isAdminRt() && ($kartuKeluarga->rt_id !== $user->rt_id || $kartuKeluarga->rw_id !== $user->rw_id || $kartuKeluarga->desa_id !== $user->desa_id)) {
            abort(403, 'Kartu Keluarga ini bukan milik wilayah RT Anda.');
        }
        // Super Admin has full access.

        $request->validate([
            'nomor_kk' => 'required|string|max:20|unique:kartu_keluargas,nomor_kk,'.$kartuKeluarga->id.',id,desa_id,'.$user->desa_id,
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
            'alamat_lengkap_kk' => 'required|string|max:255',
            'klasifikasi' => 'required|in:Pra-Sejahtera,Sejahtera I,Sejahtera II,Sejahtera III,Sejahtera III Plus',
        ]);

        $kartuKeluarga->update([
            'nomor_kk' => $request->nomor_kk,
            'rw_id' => $request->rw_id,
            'rt_id' => $request->rt_id,
            'alamat_lengkap' => $request->alamat_lengkap_kk,
            'klasifikasi' => $request->klasifikasi,
        ]);

        return redirect()->route('kartu-keluarga.index')->with('success', 'Kartu Keluarga berhasil diperbarui!');
    }

    /**
     * Remove the specified Kartu Keluarga.
     */
    public function destroy(string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
            // Check if user has permission to access this module at all
            if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengelola data anggota keluarga.');
            }
        // Super Admin has full access.

        DB::beginTransaction();
        try {
            $kartuKeluarga->wargas()->delete(); 
            $kartuKeluarga->delete();

            DB::commit();
            return redirect()->route('kartu-keluarga.index')->with('success', 'Kartu Keluarga dan semua anggotanya berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Kartu Keluarga: ' . $e->getMessage());
        }
    }

    /**
     * Get RTs by RW ID for dynamic dropdowns.
     */
    public function getRtsByRw(Request $request, string $subdomain)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt() || !$user->desa_id) {
            return response()->json([], 403); // Forbidden
        }

        $rwId = $request->query('rw_id');
        if (!$rwId) {
            return response()->json([]);
        }

        // Pastikan RW tersebut milik desa yang sedang login
        $rts = RT::where('rw_id', $rwId)
                ->where('desa_id', $user->desa_id)
                ->orderBy('nomor_rt')
                ->get(['id', 'nomor_rt']); // Hanya ambil kolom yang dibutuhkan

        return response()->json($rts);
    }

    /**
     * Search Kartu Keluarga by nomor_kk or nama_kepala_keluarga for AJAX Select2.
     */
    public function searchKk(Request $request, string $subdomain)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            return response()->json(['results' => []], 403); // Forbidden
        }

        $search = $request->query('term'); // Query pencarian dari Select2
        $desaId = $user->desa_id;

        $query = KartuKeluarga::where('desa_id', $desaId)
                              ->with('kepalaKeluarga'); // Eager load kepalaKeluarga

        // Apply RW/RT filtering if user is Admin RW/RT
        if ($user->isAdminRw() && $user->rw_id) {
            $query->where('rw_id', $user->rw_id);
        }
        if ($user->isAdminRt() && $user->rt_id) {
            $query->where('rt_id', $user->rt_id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_kk', 'like', '%' . $search . '%')
                  ->orWhereHas('kepalaKeluarga', function($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', '%' . $search . '%');
                  });
            });
        }

        $results = $query->limit(10)->get(); // Ambil 10 hasil teratas

        // Format data untuk Select2
        $formattedResults = $results->map(function ($kk) {
            return [
                'id' => $kk->id,
                'text' => 'KK: ' . $kk->nomor_kk . ' (Kepala: ' . ($kk->kepalaKeluarga->nama_lengkap ?? '-') . ')',
            ];
        });

        return response()->json(['results' => $formattedResults]);
    }
}
