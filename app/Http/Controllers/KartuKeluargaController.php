<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
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

        $search = $request->query('search');

        $desaId = $user->desa_id;

        $query = KartuKeluarga::where('desa_id', $desaId)
                            ->with('kepalaKeluarga'); // Eager load kepalaKeluarga

        // Apply RW/RT filtering if user is Admin RW/RT  
        if ($user->hasRole('admin_rw') && $user->rw_id) {
            $query->where('rw_id', $user->rw_id);
        }
        if ($user->hasRole('admin_rw') && $user->rt_id) {
            $query->where('rt_id', $user->rt_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_kk', 'like', '%' . $search . '%')
                ->orWhereHas('kepalaKeluarga', function($q2) use ($search) {
                    $q2->where('nama_lengkap', 'like', '%' . $search . '%');
                });
            });
        }

        $kartuKeluargas = $query->paginate(15);

        return view('admin_desa.kartu_keluarga.index', compact('kartuKeluargas'));
    }

    /**
     * Show the form for creating a new Kartu Keluarga.
     */
    public function create(string $subdomain)
    {
        $user = Auth::user();

        // Ambil RW dan RT sesuai scope user yang login
        $rws = RW::all();
        $rts = RT::all();

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

        $rws = RW::all();
        $rts = RT::where('rw_id', $kartuKeluarga->rw_id)->get(); 

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
        
        return view('admin_desa.kartu_keluarga.edit', compact(
            'kartuKeluarga', 'rws', 'rts', 'klasifikasiOptions', 'pekerjaanOptions', 'pendidikanOptions'
        ));
    }

    public function show(string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $kartuKeluarga->load('kepalaKeluarga', 'wargas.hubunganKeluarga', 'wargas.statusKependudukan');

        // Ambil semua status kependudukan untuk dropdown di modal
        $semuaStatus = StatusKependudukan::all();

        return view('admin_desa.kartu_keluarga.show', compact('kartuKeluarga', 'semuaStatus')); // <-- Kirim $semuaStatus
    }

    // 2. Tambahkan method BARU di bawah ini untuk memproses update massal
    public function updateStatusAnggota(Request $request, string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $request->validate([
            'status_kependudukan_id' => 'required|exists:status_kependudukans,id',
        ]);

        // 1. Ambil semua model warga yang terkait dengan KK ini
        $anggotaKeluarga = $kartuKeluarga->wargas;
        $newStatusId = $request->status_kependudukan_id;

        // 2. Lakukan perulangan dan update setiap warga secara individu
        foreach ($anggotaKeluarga as $anggota) {
            $anggota->update([
                'status_kependudukan_id' => $newStatusId
            ]);
            // Karena kita menggunakan ->update() pada model individu ($anggota),
            // Observer akan terpicu untuk setiap anggota.
        }

        return redirect()->back()->with('success', 'Status seluruh anggota keluarga berhasil diperbarui dan dicatat di log.');
    }
    
    /**
     * Update the specified Kartu Keluarga.
     */
    public function update(Request $request, string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
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

        $rwId = $request->query('rw_id');
        if (!$rwId) {
            return response()->json([]);
        }

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

        $search = $request->query('term'); // Query pencarian dari Select2
        $desaId = $user->desa_id;

        $query = KartuKeluarga::where('desa_id', $desaId)
                              ->with('kepalaKeluarga'); // Eager load kepalaKeluarga

        // Apply RW/RT filtering if user is Admin RW/RT
        if ($user->hasRole('admin_rw') && $user->rw_id) {
            $query->where('rw_id', $user->rw_id);
        }
        if ($user->hasRole('admin_rw') && $user->rt_id) {
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
