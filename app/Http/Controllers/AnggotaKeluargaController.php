<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnggotaKeluargaController extends Controller
{
    /**
     * Display a listing of the family members for a specific Kartu Keluarga.
     */
    public function index(string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
        // Check if user has permission to access this module at all
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola data anggota keluarga.');
        }

        // If user is Admin Desa, ensure the KK belongs to their desa
        // This check is often redundant due to global scope but acts as a safeguard.
        if ($user->isAdminDesa()) {
            if ($kartuKeluarga->desa_id !== $user->desa_id) {
                abort(403, 'Kartu Keluarga ini bukan milik desa Anda.');
            }
        }
        // Super Admin can access any desa's KK, so no desa_id check here for them.

        // Ambil semua anggota keluarga dari KK ini, termasuk Kepala Keluarga
        $anggotaKeluargas = $kartuKeluarga->wargas()->orderBy('hubungan_keluarga')->get();

        return view('admin_desa.anggota_keluarga.index', compact('kartuKeluarga', 'anggotaKeluargas'));
    }

    /**
     * Show the form for creating a new family member for a specific Kartu Keluarga.
     */
    public function create(string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
        // Check if user has permission to access this module at all
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola data anggota keluarga.');
        }

        // If user is Admin Desa, ensure the KK belongs to their desa
        if ($user->isAdminDesa()) {
            if ($kartuKeluarga->desa_id !== $user->desa_id) {
                abort(403, 'Kartu Keluarga ini bukan milik desa Anda.');
            }
        }
        // Super Admin can access any desa's KK.

        // Ambil RW dan RT dari KK yang bersangkutan
        $rw = $kartuKeluarga->rw;
        $rt = $kartuKeluarga->rt;

        // Opsi dropdown untuk Warga
        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
        $agamaOptions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        $statusPerkawinanOptions = ['BELUM KAWIN', 'KAWIN TERCATAT', 'KAWIN BLM TERCATAT', 'CERAI MATI', 'CERAI HIDUP', 'CERAI BLM TERCATAT'];
        $pekerjaanOptions = [
            'BLm Bekerja',
            'Mengurus Rumah Tangga',
            'Pelajar / Mahasiswa',
            'Pensiunan',
            'Pegawai Negeri Sipil',
            'Tentara Nasional Indonesia',
            'Kepolisian RI',
            'Perdagangan',
            'Petani / Pekebun',
            'Peternak',
            'Nelayan / Perikanan',
            'Industri',
            'Konstruksi',
            'Transportasi',
            'Karyawan Swasta',
            'Karyawan BUMN',
            'Karyawan BUMD',
            'Karyawan Honorer',
            'Buruh Harian Lepas',
            'Buruh Tani / Perkebunan',
            'Buruh Nelayan / Perikanan',
            'Buruh Peternakan',
            'Pembantu Rumah Tangga',
            'Tukang Cukur',
            'Tukang Listrik',
            'Tukang Batu',
            'Tukang Kayu',
            'Tukang Sol Sepatu',
            'Tukang Las / Pandai Besi',
            'Tukang Jahit',
            'Penata Rambut',
            'Penata Rias',
            'Penata Busana',
            'Mekanik',
            'Tukang Gigi',
            'Seniman',
            'Tabib',
            'Paraji',
            'Perancang Busana',
            'Penerjemah',
            'Imam Masjid',
            'Pendeta',
            'Pastur',
            'Wartawan',
            'Ustadz / Mubaligh',
            'Juru Masak',
            'Promotor Acara',
            'Anggota DPR-RI',
            'Anggota DPD',
            'Anggota BPK',
            'Presiden',
            'Wakil Presiden',
            'Anggota Mahkamah Konstitusi',
            'Anggota Kabinet / Kementerian',
            'Duta Besar',
            'Gubernur',
            'Wakil Gubernur',
            'Bupati',
            'Wakil Bupati',
            'Walikota',
            'Wakil Walikota',
            'Anggota DPRD Provinsi',
            'Anggota DPRD Kabupaten',
            'Dosen',
            'Guru',
            'Pilot',
            'Pengacara',
            'Notaris',
            'Arsitek',
            'Akuntan',
            'Konsultan',
            'Dokter',
            'Bidan',
            'Perawat',
            'Apoteker',
            'Psikiater / Psikolog',
            'Penyiar Televisi',
            'Penyiar Radio',
            'Pelaut',
            'Peneliti',
            'Sopir',
            'Pialang',
            'Paranormal',
            'Pedagang',
            'Perangkat Desa',
            'Kepala Desa',
            'Biarawati',
            'Wiraswasta',
            'Anggota Lembaga Tinggi',
            'Artis',
            'Atlit',
            'Chef',
            'Manajer',
            'Tenaga Tata Usaha',
            'Operator',
            'Pekerja Pengolahan, Kerajinan',
            'Teknisi',
            'Asisten Ahli',
            'Lainnya'
        ];
        $pendidikanOptions = ['BLM SEKOLAH', 'BLM TAMAT SD', 'SD', 'SLTP', 'SLTA', 'DIPLOMA I/II', 'DIPLOMA IV/STRATA I', 'STRATA II', 'STRATA III'];
        $kewarganegaraanOptions = ['WNI', 'WNA'];
        $golonganDarahOptions = ['A', 'B', 'AB', 'O', '-'];
        $hubunganKeluargaOptions = ['Kepala Keluarga', 'Anak', 'Cucu', 'Istri', 'Menantu', 'Suami', 'Saudara', 'Kakak', 'Adik', 'Lainnya'];
        $statusKependudukanOptions = ['Warga Asli', 'Pendatang', 'Sementara', 'Pindah', 'Meninggal'];
        $statusKhususOptions = ['Disabilitas', 'Lansia', 'Ibu Hamil', 'Balita', 'Penerima PKH', 'Penerima BPNT', 'Lainnya'];

        return view('admin_desa.anggota_keluarga.create', compact(
            'kartuKeluarga',
            'rw',
            'rt',
            'jenisKelaminOptions',
            'agamaOptions',
            'statusPerkawinanOptions',
            'pekerjaanOptions',
            'pendidikanOptions',
            'kewarganegaraanOptions', // Tambahkan pendidikanOptions
            'golonganDarahOptions',
            'hubunganKeluargaOptions',
            'statusKependudukanOptions',
            'statusKhususOptions'
        ));
    }

    /**
     * Store a newly created family member for a specific Kartu Keluarga.
     */
    public function store(Request $request, string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
        // Check if user has permission to access this module at all
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola data anggota keluarga.');
        }

        // If user is Admin Desa, ensure the KK belongs to their desa
        if ($user->isAdminDesa()) {
            if ($kartuKeluarga->desa_id !== $user->desa_id) {
                abort(403, 'Kartu Keluarga ini bukan milik desa Anda.');
            }
        }
        // Super Admin can access any desa's KK.

        $request->validate([
            'nik' => 'required|string|digits:16|unique:wargas,nik,NULL,id,desa_id,' . $user->desa_id, // NIK unik per desa
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string|max:50',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan' => 'required|string|max:100',
            'pendidikan' => 'nullable|in:Tidak/Belum Sekolah,SD,SMP,SMA,S1,S2,S3', // Tambahkan validasi ini
            'kewarganegaraan' => 'required|string|max:50',
            'golongan_darah' => 'nullable|string|max:5',
            'alamat_lengkap' => 'required|string|max:255',
            'hubungan_keluarga' => 'required|in:Anak,Cucu,Istri,Menantu,Suami,Saudara,Kakak,Adik,Lainnya',
            'status_kependudukan' => 'required|in:Warga Asli,Pendatang,Sementara,Pindah,Meninggal', // Fixed: 'Meninggal' added back
            'status_khusus' => 'nullable|array', // Untuk checkbox multiple
        ]);

        DB::beginTransaction();
        try {
            Warga::create([
                'desa_id' => $user->desa_id,
                'kartu_keluarga_id' => $kartuKeluarga->id, // Tautkan ke KK yang sedang aktif
                'rw_id' => $kartuKeluarga->rw_id, // Ambil dari KK
                'rt_id' => $kartuKeluarga->rt_id, // Ambil dari KK
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'pekerjaan' => $request->pekerjaan,
                'pendidikan' => $request->pendidikan, // Tambahkan ini
                'kewarganegaraan' => $request->kewarganegaraan,
                'golongan_darah' => $request->golongan_darah,
                'alamat_lengkap' => $request->alamat_lengkap,
                'hubungan_keluarga' => $request->hubungan_keluarga,
                'status_kependudukan' => $request->status_kependudukan,
                'status_khusus' => $request->status_khusus ? json_encode($request->status_khusus) : null,
            ]);

            DB::commit();
            return redirect()->route('kartu-keluarga.anggota.index', $kartuKeluarga)->with('success', 'Anggota keluarga berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan anggota keluarga: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified family member.
     */
    public function edit(string $subdomain, KartuKeluarga $kartuKeluarga, $anggotaId) // Menggunakan $anggotaId
    {
        $user = Auth::user();
        // Check if user has permission to access this module at all
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola data anggota keluarga.');
        }

        // Retrieve Warga without global scope, then apply manual checks
        $anggotaKeluarga = Warga::withoutGlobalScopes()
            ->where('id', $anggotaId)
            ->firstOrFail();

        // Manual check for desa_id for Admin Desa
        if ($user->isAdminDesa()) {
            if ($anggotaKeluarga->desa_id !== $user->desa_id) {
                abort(403, 'Anggota keluarga ini tidak milik desa Anda.');
            }
            if ($kartuKeluarga->desa_id !== $user->desa_id) { // Also check KK desa_id
                abort(403, 'Kartu Keluarga ini tidak milik desa Anda.');
            }
        }
        // Super Admin can access any desa's data.

        // Pengecekan integritas data: Pastikan anggota keluarga ini benar-benar terhubung dengan Kartu Keluarga yang dimaksud.
        if ($anggotaKeluarga->kartu_keluarga_id !== $kartuKeluarga->id) {
            abort(403, 'Anggota keluarga ini tidak terhubung dengan Kartu Keluarga yang dimaksud.');
        }

        // Ensure status_khusus is an array for the view
        // If it's a string (JSON), decode it. Otherwise, use as is or default to empty array.
        $anggotaKeluarga->status_khusus = is_string($anggotaKeluarga->status_khusus)
            ? json_decode($anggotaKeluarga->status_khusus, true)
            : ($anggotaKeluarga->status_khusus ?? []);


        // Opsi dropdown untuk Warga
        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
        $agamaOptions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        $statusPerkawinanOptions = ['BELUM KAWIN', 'KAWIN TERCATAT', 'KAWIN BLM TERCATAT', 'CERAI MATI', 'CERAI HIDUP', 'CERAI BLM TERCATAT'];
        $pekerjaanOptions = [
            'BLm Bekerja',
            'Mengurus Rumah Tangga',
            'Pelajar',
            'Pelajar / Mahasiswa',
            'Pensiunan',
            'Pegawai Negeri Sipil',
            'Tentara Nasional Indonesia',
            'Kepolisian RI',
            'Perdagangan',
            'Petani / Pekebun',
            'Peternak',
            'Nelayan / Perikanan',
            'Industri',
            'Konstruksi',
            'Transportasi',
            'Karyawan Swasta',
            'Karyawan BUMN',
            'Karyawan BUMD',
            'Karyawan Honorer',
            'Buruh Harian Lepas',
            'Buruh Tani / Perkebunan',
            'Buruh Nelayan / Perikanan',
            'Buruh Peternakan',
            'Pembantu Rumah Tangga',
            'Tukang Cukur',
            'Tukang Listrik',
            'Tukang Batu',
            'Tukang Kayu',
            'Tukang Sol Sepatu',
            'Tukang Las / Pandai Besi',
            'Tukang Jahit',
            'Penata Rambut',
            'Penata Rias',
            'Penata Busana',
            'Mekanik',
            'Tukang Gigi',
            'Seniman',
            'Tabib',
            'Paraji',
            'Perancang Busana',
            'Penerjemah',
            'Imam Masjid',
            'Pendeta',
            'Pastur',
            'Wartawan',
            'Ustadz / Mubaligh',
            'Juru Masak',
            'Promotor Acara',
            'Anggota DPR-RI',
            'Anggota DPD',
            'Anggota BPK',
            'Presiden',
            'Wakil Presiden',
            'Anggota Mahkamah Konstitusi',
            'Anggota Kabinet / Kementerian',
            'Duta Besar',
            'Gubernur',
            'Wakil Gubernur',
            'Bupati',
            'Wakil Bupati',
            'Walikota',
            'Wakil Walikota',
            'Anggota DPRD Provinsi',
            'Anggota DPRD Kabupaten',
            'Dosen',
            'Guru',
            'Pilot',
            'Pengacara',
            'Notaris',
            'Arsitek',
            'Akuntan',
            'Konsultan',
            'Dokter',
            'Bidan',
            'Perawat',
            'Apoteker',
            'Psikiater / Psikolog',
            'Penyiar Televisi',
            'Penyiar Radio',
            'Pelaut',
            'Peneliti',
            'Sopir',
            'Pialang',
            'Paranormal',
            'Pedagang',
            'Perangkat Desa',
            'Kepala Desa',
            'Biarawati',
            'Wiraswasta',
            'Anggota Lembaga Tinggi',
            'Artis',
            'Atlit',
            'Chef',
            'Manajer',
            'Tenaga Tata Usaha',
            'Operator',
            'Pekerja Pengolahan, Kerajinan',
            'Teknisi',
            'Asisten Ahli',
            'Lainnya'
        ];
        $pendidikanOptions = ['BLM SEKOLAH', 'BLM TAMAT SD', 'SD', 'SLTP', 'SLTA', 'DIPLOMA I/II', 'DIPLOMA IV/STRATA I', 'STRATA II', 'STRATA III'];
        $kewarganegaraanOptions = ['WNI', 'WNA'];
        $golonganDarahOptions = ['A', 'B', 'AB', 'O', '-'];
        $hubunganKeluargaOptions = ['Kepala Keluarga', 'Anak', 'Cucu', 'Istri', 'Menantu', 'Suami', 'Saudara', 'Kakak', 'Adik', 'Lainnya'];
        $statusKependudukanOptions = ['Warga Asli', 'Pendatang', 'Sementara', 'Pindah', 'Meninggal'];
        $statusKhususOptions = ['Disabilitas', 'Lansia', 'Ibu Hamil', 'Balita', 'Penerima PKH', 'Penerima BPNT', 'Lainnya'];

        return view('admin_desa.anggota_keluarga.edit', compact(
            'kartuKeluarga',
            'anggotaKeluarga',
            'jenisKelaminOptions',
            'agamaOptions',
            'statusPerkawinanOptions',
            'pekerjaanOptions',
            'pendidikanOptions',
            'kewarganegaraanOptions', // Tambahkan pendidikanOptions
            'golonganDarahOptions',
            'hubunganKeluargaOptions',
            'statusKependudukanOptions',
            'statusKhususOptions'
        ));
    }

    /**
     * Update the specified family member in storage.
     */
    public function update(Request $request, string $subdomain, KartuKeluarga $kartuKeluarga, $anggotaId) // Menggunakan $anggotaId
    {
        $user = Auth::user();
        // Check if user has permission to access this module at all
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola data anggota keluarga.');
        }

        // Retrieve Warga without global scope, then apply manual checks
        $anggotaKeluarga = Warga::withoutGlobalScopes()
            ->where('id', $anggotaId)
            ->firstOrFail();

        // Manual check for desa_id for Admin Desa
        if ($user->isAdminDesa()) {
            if ($anggotaKeluarga->desa_id !== $user->desa_id) {
                abort(403, 'Anggota keluarga ini tidak milik desa Anda.');
            }
            if ($kartuKeluarga->desa_id !== $user->desa_id) { // Also check KK desa_id
                abort(403, 'Kartu Keluarga ini tidak milik desa Anda.');
            }
        }
        // Super Admin can access any desa's data.

        // Pengecekan integritas data: Pastikan anggota keluarga ini benar-benar terhubung dengan Kartu Keluarga yang dimaksud.
        if ($anggotaKeluarga->kartu_keluarga_id !== $kartuKeluarga->id) {
            abort(403, 'Anggota keluarga ini tidak terhubung dengan Kartu Keluarga yang dimaksud.');
        }

        $request->validate([
            'nik' => 'required|string|digits:16|unique:wargas,nik,' . $anggotaKeluarga->id . ',id,desa_id,' . $user->desa_id, // NIK unik per desa, kecuali dirinya sendiri
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string|max:50',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan' => 'required|string|max:100',
            'pendidikan' => 'nullable|in:Tidak/Belum Sekolah,SD,SMP,SMA,S1,S2,S3', // Tambahkan validasi ini
            'kewarganegaraan' => 'required|string|max:50',
            'golongan_darah' => 'nullable|string|max:5',
            'alamat_lengkap' => 'required|string|max:255',
            'hubungan_keluarga' => 'required|in:Anak,Cucu,Istri,Menantu,Suami,Saudara,Kakak,Adik,Lainnya',
            'status_kependudukan' => 'required|in:Warga Asli,Pendatang,Sementara,Pindah,Meninggal', // Fixed: 'Meninggal' added back
            'status_khusus' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $anggotaKeluarga->update([
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'pekerjaan' => $request->pekerjaan,
                'pendidikan' => $request->pendidikan, // Tambahkan ini
                'kewarganegaraan' => $request->kewarganegaraan,
                'golongan_darah' => $request->golongan_darah,
                'alamat_lengkap' => $request->alamat_lengkap,
                'hubungan_keluarga' => $request->hubungan_keluarga,
                'status_kependudukan' => $request->status_kependudukan,
                'status_khusus' => $request->status_khusus ? json_encode($request->status_khusus) : null,
            ]);

            DB::commit();
            return redirect()->route('kartu-keluarga.anggota.index', $kartuKeluarga)->with('success', 'Anggota keluarga berhasil diperbarui!');

        } catch (\Exception | \Throwable $e) { // Catch Throwable for broader error handling
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui anggota keluarga: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified family member from storage.
     */
    public function destroy(string $subdomain, KartuKeluarga $kartuKeluarga, $anggotaId) // Menggunakan $anggotaId
    {
        $user = Auth::user();
        // Check if user has permission to access this module at all
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola data anggota keluarga.');
        }

        // Retrieve Warga without global scope, then apply manual checks
        $anggotaKeluarga = Warga::withoutGlobalScopes()
            ->where('id', $anggotaId)
            ->firstOrFail();

        // Manual check for desa_id for Admin Desa
        if ($user->isAdminDesa()) {
            if ($anggotaKeluarga->desa_id !== $user->desa_id) {
                abort(403, 'Anggota keluarga ini tidak milik desa Anda.');
            }
            if ($kartuKeluarga->desa_id !== $user->desa_id) { // Also check KK desa_id
                abort(403, 'Kartu Keluarga ini tidak milik desa Anda.');
            }
        }
        // Super Admin can access any desa's data.

        // Pengecekan integritas data: Pastikan anggota keluarga ini benar-benar terhubung dengan Kartu Keluarga yang dimaksud.
        if ($anggotaKeluarga->kartu_keluarga_id !== $kartuKeluarga->id) {
            abort(403, 'Anggota keluarga ini tidak terhubung dengan Kartu Keluarga yang dimaksud.');
        }

        // Pastikan tidak menghapus Kepala Keluarga jika dia satu-satunya anggota
        // Atau jika KK ini masih memiliki anggota lain
        if ($kartuKeluarga->kepalaKeluarga && $kartuKeluarga->kepalaKeluarga->id === $anggotaKeluarga->id) {
            // Jika yang dihapus adalah Kepala Keluarga, dan ada anggota lain,
            // kita harus minta user untuk menunjuk Kepala Keluarga baru terlebih dahulu.
            // Untuk kesederhanaan, saat ini kita tidak izinkan hapus KK jika dia adalah kepala keluarga
            // dan ada anggota lain.
            if ($kartuKeluarga->wargas()->count() > 1) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus Kepala Keluarga jika masih ada anggota keluarga lain. Harap tentukan Kepala Keluarga baru terlebih dahulu atau hapus semua anggota lain terlebih dahulu.');
            }
        }

        DB::beginTransaction();
        try {
            $anggotaKeluarga->delete();

            // Jika yang dihapus adalah Kepala Keluarga dan dia satu-satunya anggota,
            // maka kepala_keluarga_id di KK bisa di-set null atau KK juga dihapus (sesuai logic destroy KK)
            if ($kartuKeluarga->kepalaKeluarga && $kartuKeluarga->kepalaKeluarga->id === $anggotaKeluarga->id && $kartuKeluarga->wargas()->count() == 0) {
                $kartuKeluarga->update(['kepala_keluarga_id' => null]);
            }

            DB::commit();
            return redirect()->route('kartu-keluarga.anggota.index', $kartuKeluarga)->with('success', 'Anggota keluarga berhasil dihapus!');
        } catch (\Exception | \Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus anggota keluarga: ' . $e->getMessage());
        }
    }

    public function searchWarga(Request $request, string $subdomain)
    {
        $searchTerm = $request->input('q');
        $user = Auth::user();

        if (!$searchTerm) {
            return response()->json(['results' => []]);
        }

        // Mulai query dasar
        $query = Warga::query();

        // Terapkan filter berdasarkan peran pengguna
        if ($user->user_type === 'admin_rt' && $user->rt_id) {
            $query->where('rt_id', $user->rt_id);
        } elseif ($user->user_type === 'admin_rw' && $user->rw_id) {
            $query->where('rw_id', $user->rw_id);
        }
        // Untuk admin_desa, tidak perlu filter tambahan karena sudah dihandle Global Scope

        // Lanjutkan dengan query pencarian NIK atau Nama
        $wargas = $query->where(function ($q) use ($searchTerm) {
            $q->where('nama_lengkap', 'LIKE', "%{$searchTerm}%")
                ->orWhere('nik', 'LIKE', "%{$searchTerm}%");
        })
            ->with(['kartuKeluarga', 'rt', 'rw']) // Eager load untuk performa
            ->limit(10)
            ->get();

        // Format data agar sesuai dengan yang dibutuhkan Select2
        $formattedResults = $wargas->map(function ($warga) {
            return [
                'id' => $warga->id,
                'text' => sprintf(
                    '%s (NIK: %s) - RW %s / RT %s',
                    $warga->nama_lengkap,
                    $warga->nik,
                    $warga->rw->nomor_rw ?? '-',
                    $warga->rt->nomor_rt ?? '-'
                )
            ];
        });

        return response()->json(['results' => $formattedResults]);
    }
}
