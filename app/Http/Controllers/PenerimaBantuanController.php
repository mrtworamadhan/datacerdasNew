<?php

namespace App\Http\Controllers;

use App\Models\PenerimaBantuan;
use App\Models\KategoriBantuan;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Models\PenerimaBantuanPhoto;
use App\Exports\PenerimaBantuanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class PenerimaBantuanController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the recipients for a specific Kategori Bantuan.
     */
    public function index(KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses umum untuk modul ini
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola penerima bantuan.');
        }
        // Global scope pada KategoriBantuan sudah memastikan $kategoriBantuan milik desa user.
        // Jika tidak, Route Model Binding akan 404.

        // Statistik untuk Info Card (Global scope akan otomatis memfilter data)
        $stats = [
            'total' => PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuan->id)->count(),
            'diajukan' => PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuan->id)->where('status_permohonan', 'Diajukan')->count(),
            'disetujui' => PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuan->id)->where('status_permohonan', 'Disetujui')->count(),
            'ditolak' => PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuan->id)->where('status_permohonan', 'Ditolak')->count(),
        ];

        // Query dasar untuk penerima bantuan kategori ini (Global scope akan memfilter)
        $queryPenerima = PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuan->id)
                                          ->with('warga.kartuKeluarga.kepalaKeluarga', 'kartuKeluarga.kepalaKeluarga', 'diajukanOleh', 'disetujuiOleh');

        // Filter berdasarkan peran pengguna (untuk daftar yang ditampilkan)
        if ($user->isAdminRw()) {
            $queryPenerima->where(function($q) use ($user) {
                $q->where('diajukan_oleh_user_id', $user->id)
                  ->orWhereHas('diajukanOleh', function($q2) use ($user) {
                      $q2->where('user_type', 'admin_rt')->where('rw_id', $user->rw_id);
                  });
            });
        } elseif ($user->isAdminRt()) {
            $queryPenerima->where('diajukan_oleh_user_id', $user->id);
        }

        $penerimaBantuans = $queryPenerima->latest()->get();

        return view('admin_desa.penerima_bantuan.index', compact('kategoriBantuan', 'penerimaBantuans', 'stats'));
    }

    /**
     * Show the form for assigning new recipients to a specific Kategori Bantuan.
     * Accessible by Admin Desa, Admin RW, Admin RT.
     */
    public function create(KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses: Admin Desa, Admin RW, Admin RT bisa mengajukan
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengajukan penerima bantuan.');
        }
        // Global scope pada KategoriBantuan sudah memastikan $kategoriBantuan milik desa user.

        // Pastikan kategori ini aktif untuk pengajuan
        if (!$kategoriBantuan->is_active_for_submission) {
            return redirect()->route('kategori-bantuan.penerima.index', $kategoriBantuan)->with('error', 'Kategori bantuan ini sedang tidak aktif untuk pengajuan.');
        }

        $kriteria = $kategoriBantuan->kriteria_json;
        $kriteria = is_string($kriteria) ? json_decode($kriteria, true) : ($kriteria ?? []);

        // Calon penerima tidak lagi diambil di sini, tapi melalui AJAX
        $calonPenerimaWarga = [];
        $calonPenerimaKK = [];

        // Ambil required_additional_fields_json untuk diteruskan ke view
        $requiredAdditionalFields = $kategoriBantuan->required_additional_fields_json ?? [];
        if (is_string($requiredAdditionalFields)) {
            $requiredAdditionalFields = json_decode($requiredAdditionalFields, true) ?? [];
        }
        if (!is_array($requiredAdditionalFields)) {
            $requiredAdditionalFields = [];
        }

        return view('admin_desa.penerima_bantuan.create', compact('kategoriBantuan', 'calonPenerimaWarga', 'calonPenerimaKK', 'requiredAdditionalFields'));
    }

    /**
     * Store newly assigned recipients.
     * Accessible by Admin Desa, Admin RW, Admin RT.
     */
    public function store(Request $request, KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses untuk menyimpan pengajuan penerima bantuan
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melakukan aksi ini.');
        }
        // Global scope pada KategoriBantuan sudah memastikan $kategoriBantuan milik desa user.

        // Pastikan kategori ini aktif untuk pengajuan
        if (!$kategoriBantuan->is_active_for_submission) {
            return redirect()->back()->with('error', 'Kategori bantuan ini sedang tidak aktif untuk pengajuan.');
        }

        $request->validate([
            'warga_ids' => 'nullable|array',
            'warga_ids.*' => 'exists:wargas,id',
            'kartu_keluarga_ids' => 'nullable|array',
            'kartu_keluarga_ids.*' => 'exists:kartu_keluargas,id',
            'tanggal_menerima' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'recipient_type' => 'required|in:warga,kk',
            'additional_fields' => 'nullable|array',
        ]);

        if ($request->recipient_type === 'warga' && empty($request->warga_ids)) {
            return redirect()->back()->with('error', 'Anda harus memilih setidaknya satu warga sebagai penerima.')->withInput();
        }
        if ($request->recipient_type === 'kk' && empty($request->kartu_keluarga_ids)) {
            return redirect()->back()->with('error', 'Anda harus memilih setidaknya satu Kartu Keluarga sebagai penerima.')->withInput();
        }
        if ($request->recipient_type === 'warga' && !empty($request->kartu_keluarga_ids)) {
             return redirect()->back()->with('error', 'Anda memilih tipe "Individu Warga" tetapi juga memilih Kartu Keluarga.')->withInput();
        }
        if ($request->recipient_type === 'kk' && !empty($request->warga_ids)) {
             return redirect()->back()->with('error', 'Anda memilih tipe "Kartu Keluarga" tetapi juga memilih Individu Warga.')->withInput();
        }

        // Validasi field tambahan berdasarkan konfigurasi kategori
        $requiredAdditionalFields = $kategoriBantuan->required_additional_fields_json ?? [];
        if (is_string($requiredAdditionalFields)) {
            $requiredAdditionalFields = json_decode($requiredAdditionalFields, true) ?? [];
        }
        if (!is_array($requiredAdditionalFields)) {
            $requiredAdditionalFields = [];
        }

        $uploadedFiles = [];
        $additionalData = [];

        foreach ($requiredAdditionalFields as $fieldConfig) {
            $fieldName = Str::slug($fieldConfig['name'], '_');
            if ($fieldConfig['type'] === 'file') {
                if ($fieldConfig['required'] && !$request->hasFile($fieldName)) {
                    return redirect()->back()->with('error', "File '{$fieldConfig['name']}' wajib diunggah.")->withInput();
                }
                if ($request->hasFile($fieldName)) {
                    $path = $request->file($fieldName)->store('public/penerima_bantuan_files');
                    $uploadedFiles[] = [
                        'photo_name' => $fieldConfig['name'],
                        'file_path' => Storage::url($path),
                    ];
                }
            } else {
                if ($fieldConfig['required'] && !isset($request->additional_fields[$fieldName]) && $fieldConfig['type'] !== 'checkbox') {
                    return redirect()->back()->with('error', "Field '{$fieldConfig['name']}' wajib diisi.")->withInput();
                }
                if ($fieldConfig['type'] === 'checkbox') {
                    $additionalData[$fieldConfig['name']] = isset($request->additional_fields[$fieldName]) ? true : false;
                } elseif (isset($request->additional_fields[$fieldName])) {
                    $additionalData[$fieldConfig['name']] = $request->additional_fields[$fieldName];
                }
            }
        }

        DB::beginTransaction();
        try {
            $selectedWargaIds = $request->warga_ids ?? [];
            $selectedKKIds = $request->kartu_keluarga_ids ?? [];

            if (!$kategoriBantuan->allow_multiple_recipients_per_kk) {
                $existingWargaRecipients = PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuan->id)
                                                          ->whereIn('warga_id', $selectedWargaIds)
                                                          ->exists();
                if ($existingWargaRecipients) {
                    throw new \Exception('Beberapa warga yang dipilih sudah terdaftar sebagai penerima bantuan ini dan kategori tidak mengizinkan penerima ganda per KK.');
                }
                $existingKKRecipients = PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuan->id)
                                                        ->whereIn('kartu_keluarga_id', $selectedKKIds)
                                                        ->exists();
                if ($existingKKRecipients) {
                    throw new \Exception('Beberapa Kartu Keluarga yang dipilih sudah terdaftar sebagai penerima bantuan ini dan kategori tidak mengizinkan penerima ganda per KK.');
                }
            }

            if ($request->recipient_type === 'warga') {
                foreach ($selectedWargaIds as $wargaId) {
                    $warga = Warga::where('id', $wargaId)->firstOrFail();
                    $penerima = PenerimaBantuan::create([
                        'desa_id' => $user->desa_id,
                        'kategori_bantuan_id' => $kategoriBantuan->id,
                        'warga_id' => $warga->id,
                        'kartu_keluarga_id' => $warga->kartu_keluarga_id,
                        'tanggal_menerima' => $request->tanggal_menerima,
                        'keterangan' => $request->keterangan,
                        'status_permohonan' => 'Diajukan',
                        'diajukan_oleh_user_id' => Auth::id(),
                        'detail_tambahan' => !empty($additionalData) ? json_encode($additionalData) : null,
                    ]);
                    foreach ($uploadedFiles as $file) {
                        $penerima->photos()->create($file);
                    }
                }
            } elseif ($request->recipient_type === 'kk') {
                foreach ($selectedKKIds as $kkId) {
                    $kk = KartuKeluarga::where('id', $kkId)->firstOrFail();
                    $penerima = PenerimaBantuan::create([
                        'desa_id' => $user->desa_id,
                        'kategori_bantuan_id' => $kategoriBantuan->id,
                        'warga_id' => null,
                        'kartu_keluarga_id' => $kk->id,
                        'tanggal_menerima' => $request->tanggal_menerima,
                        'keterangan' => $request->keterangan,
                        'status_permohonan' => 'Diajukan',
                        'diajukan_oleh_user_id' => Auth::id(),
                        'detail_tambahan' => !empty($additionalData) ? json_encode($additionalData) : null,
                    ]);
                    foreach ($uploadedFiles as $file) {
                        $penerima->photos()->create($file);
                    }
                }
            }

            DB::commit();
            return redirect()->route('kategori-bantuan.penerima.index', $kategoriBantuan)->with('success', 'Penerima bantuan berhasil diajukan untuk verifikasi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengajukan penerima bantuan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified recipient.
     * Accessible by Admin Desa, Admin RW, Admin RT.
     */
    public function edit(KategoriBantuan $kategoriBantuan, PenerimaBantuan $penerimaBantuan)
    {
        $user = Auth::user();
        // Cek hak akses: Admin Desa, Super Admin, Admin RW, Admin RT bisa mengedit detail
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit penerima bantuan.');
        }

        // Pengecekan integritas data: Pastikan penerima bantuan ini benar-benar terhubung dengan kategori yang dimaksud.
        if ($penerimaBantuan->kategori_bantuan_id !== $kategoriBantuan->id) {
            abort(403, 'Penerima bantuan ini tidak terhubung dengan kategori yang dimaksud.');
        }
        // Cek wilayah RW/RT untuk Admin RW/RT
        if ($user->isAdminRw() && ($penerimaBantuan->warga->rw_id ?? null) !== $user->rw_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RW Anda.');
        }
        if ($user->isAdminRt() && ($penerimaBantuan->warga->rt_id ?? null) !== $user->rt_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RT Anda.');
        }
        // Super Admin has full access.

        $penerimaBantuan->load('warga.kartuKeluarga.kepalaKeluarga', 'kartuKeluarga.kepalaKeluarga', 'photos');

        $selectedWarga = $penerimaBantuan->warga;
        $selectedKK = $penerimaBantuan->kartuKeluarga;

        $requiredAdditionalFields = $kategoriBantuan->required_additional_fields_json ?? [];
        if (is_string($requiredAdditionalFields)) {
            $requiredAdditionalFields = json_decode($requiredAdditionalFields, true) ?? [];
        }
        if (!is_array($requiredAdditionalFields)) {
            $requiredAdditionalFields = [];
        }
        
        $existingAdditionalData = $penerimaBantuan->detail_tambahan ?? [];
        if (is_string($existingAdditionalData)) {
            $existingAdditionalData = json_decode($existingAdditionalData, true) ?? [];
        }
        if (!is_array($existingAdditionalData)) {
            $existingAdditionalData = [];
        }

        return view('admin_desa.penerima_bantuan.edit', compact('kategoriBantuan', 'penerimaBantuan', 'selectedWarga', 'selectedKK', 'requiredAdditionalFields', 'existingAdditionalData'));
    }

    /**
     * Update the specified recipient in storage.
     * Accessible by Admin Desa, Admin RW, Admin RT.
     */
    public function update(Request $request, KategoriBantuan $kategoriBantuan, PenerimaBantuan $penerimaBantuan)
    {
        $user = Auth::user();
        // Cek hak akses: Admin Desa, Super Admin, Admin RW, Admin RT bisa mengupdate
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui penerima bantuan.');
        }

        // Pengecekan integritas data: Pastikan penerima bantuan ini benar-benar terhubung dengan kategori yang dimaksud.
        if ($penerimaBantuan->kategori_bantuan_id !== $kategoriBantuan->id) {
            abort(403, 'Penerima bantuan ini tidak terhubung dengan kategori yang dimaksud.');
        }
        // Cek wilayah RW/RT untuk Admin RW/RT
        if ($user->isAdminRw() && ($penerimaBantuan->warga->rw_id ?? null) !== $user->rw_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RW Anda.');
        }
        if ($user->isAdminRt() && ($penerimaBantuan->warga->rt_id ?? null) !== $user->rt_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RT Anda.');
        }
        // Super Admin has full access.

        $request->validate([
            'tanggal_menerima' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'additional_fields' => 'nullable|array',
            'additional_files' => 'nullable|array',
            'additional_files.*' => 'file|max:2048',
            'existing_files_to_keep' => 'nullable|array',
        ]);

        $requiredAdditionalFields = $kategoriBantuan->required_additional_fields_json ?? [];
        $additionalData = [];
        $uploadedFiles = [];

        foreach ($requiredAdditionalFields as $fieldConfig) {
            $fieldName = Str::slug($fieldConfig['name'], '_');
            if ($fieldConfig['type'] === 'file') {
                if ($fieldConfig['required'] && !$request->hasFile($fieldName) && !in_array($fieldName, ($request->existing_files_to_keep ?? []))) {
                    return redirect()->back()->with('error', "File '{$fieldConfig['name']}' wajib diunggah.")->withInput();
                }
                if ($request->hasFile($fieldName)) {
                    $path = $request->file($fieldName)->store('public/penerima_bantuan_files');
                    $uploadedFiles[] = [
                        'photo_name' => $fieldConfig['name'],
                        'file_path' => Storage::url($path),
                    ];
                }
            } else {
                if ($fieldConfig['required'] && !isset($request->additional_fields[$fieldName]) && $fieldConfig['type'] !== 'checkbox') {
                    return redirect()->back()->with('error', "Field '{$fieldConfig['name']}' wajib diisi.")->withInput();
                }
                if ($fieldConfig['type'] === 'checkbox') {
                    $additionalData[$fieldConfig['name']] = isset($request->additional_fields[$fieldName]) ? true : false;
                } elseif (isset($request->additional_fields[$fieldName])) {
                    $additionalData[$fieldConfig['name']] = $request->additional_fields[$fieldName];
                }
            }
        }

        DB::beginTransaction();
        try {
            $penerimaBantuan->update([
                'tanggal_menerima' => $request->tanggal_menerima,
                'keterangan' => $request->keterangan,
                'detail_tambahan' => !empty($additionalData) ? json_encode($additionalData) : null,
            ]);

            $existingFilesToKeep = $request->existing_files_to_keep ?? [];
            foreach ($penerimaBantuan->photos as $photo) {
                if (!in_array($photo->id, $existingFilesToKeep)) {
                    Storage::delete(str_replace('/storage/', 'public/', $photo->file_path));
                    $photo->delete();
                }
            }

            foreach ($uploadedFiles as $file) {
                $penerimaBantuan->photos()->create($file);
            }

            DB::commit();
            return redirect()->route('kategori-bantuan.penerima.index', $kategoriBantuan)->with('success', 'Penerima bantuan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui penerima bantuan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the detail of a specific recipient (for Admin Desa to approve/reject).
     * This method will be used for approval/rejection UI.
     */
    public function show(KategoriBantuan $kategoriBantuan, $penerimaBantuanId) // Ubah parameter menjadi ID
    {
        $user = Auth::user();
        // Cek hak akses: Admin Desa, Super Admin, Admin RW, Admin RT bisa melihat detail
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat detail verifikasi penerima bantuan.');
        }

        // Ambil PenerimaBantuan tanpa global scope, lalu filter manual
        $penerimaBantuan = PenerimaBantuan::withoutGlobalScopes()
            ->where('id', $penerimaBantuanId)
            ->firstOrFail();

        // Pengecekan integritas data: Pastikan penerima bantuan ini benar-benar terhubung dengan kategori yang dimaksud.
        if ($penerimaBantuan->kategori_bantuan_id !== $kategoriBantuan->id) {
            abort(403, 'Penerima bantuan ini tidak terhubung dengan kategori yang dimaksud.');
        }
        // Cek wilayah RW/RT untuk Admin RW/RT
        if ($user->isAdminRw() && ($penerimaBantuan->warga->rw_id ?? null) !== $user->rw_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RW Anda.');
        }
        if ($user->isAdminRt() && ($penerimaBantuan->warga->rt_id ?? null) !== $user->rt_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RT Anda.');
        }
        // Super Admin has full access.

        $penerimaBantuan->load('warga.kartuKeluarga.kepalaKeluarga', 'kartuKeluarga.kepalaKeluarga', 'diajukanOleh', 'photos'); // Load photos

        return view('admin_desa.penerima_bantuan.show', compact('kategoriBantuan', 'penerimaBantuan'));
    }

    /**
     * Update the status of a recipient (Approve/Reject).
     * Accessible by Admin Desa, Admin RW, Admin RT.
     */
    public function updateStatus(Request $request, KategoriBantuan $kategoriBantuan, $penerimaBantuanId) // Ubah parameter menjadi ID
    {
        $user = Auth::user();
        // Cek hak akses: Admin Desa (approve/reject), Admin RW (verifikasi RW), Admin RT (verifikasi RT)
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui status penerima bantuan.');
        }

        // Ambil PenerimaBantuan tanpa global scope, lalu filter manual
        $penerimaBantuan = PenerimaBantuan::withoutGlobalScopes()
            ->where('id', $penerimaBantuanId)
            ->firstOrFail();

        // Pengecekan integritas data: Pastikan penerima bantuan ini benar-benar terhubung dengan kategori yang dimaksud.
        if ($penerimaBantuan->kategori_bantuan_id !== $kategoriBantuan->id) {
            abort(403, 'Penerima bantuan ini tidak terhubung dengan kategori yang dimaksud.');
        }
        // Cek wilayah RW/RT untuk Admin RW/RT
        if ($user->isAdminRw() && ($penerimaBantuan->warga->rw_id ?? null) !== $user->rw_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RW Anda.');
        }
        if ($user->isAdminRt() && ($penerimaBantuan->warga->rt_id ?? null) !== $user->rt_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RT Anda.');
        }
        // Super Admin has full access.

        $request->validate([
            'status' => 'required|in:Diajukan,Diverifikasi RT,Diverifikasi RW,Disetujui,Ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'catatan_persetujuan_penolakan' => $request->catatan,
            ];

            if ($request->status === 'Diverifikasi RT' && $user->isAdminRt()) {
                $updateData['status_permohonan'] = 'Diverifikasi RT';
                $updateData['disetujui_oleh_user_id'] = Auth::id();
                $updateData['tanggal_verifikasi'] = Carbon::now();
            } elseif ($request->status === 'Diverifikasi RW' && $user->isAdminRw()) {
                $updateData['status_permohonan'] = 'Diverifikasi RW';
                $updateData['disetujui_oleh_user_id'] = Auth::id();
                $updateData['tanggal_verifikasi'] = Carbon::now();
            } elseif (($request->status === 'Disetujui' || $request->status === 'Ditolak') && $user->isAdminDesa()) {
                $updateData['status_permohonan'] = $request->status;
                $updateData['disetujui_oleh_user_id'] = Auth::id();
                $updateData['tanggal_verifikasi'] = Carbon::now();
            } else {
                abort(403, 'Anda tidak diizinkan untuk mengubah status ke tahap ini.');
            }

            $penerimaBantuan->update($updateData);
            DB::commit();
            return redirect()->route('kategori-bantuan.penerima.index', $kategoriBantuan)->with('success', 'Status penerima bantuan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status penerima bantuan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified recipient from storage.
     * Accessible only by Admin Desa (for cleanup).
     */
    public function destroy(KategoriBantuan $kategoriBantuan, $penerimaBantuanId) // Ubah parameter menjadi ID
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus penerima bantuan.');
        }

        // Ambil PenerimaBantuan tanpa global scope, lalu filter manual
        $penerimaBantuan = PenerimaBantuan::withoutGlobalScopes()
            ->where('id', $penerimaBantuanId)
            ->firstOrFail();

        // Pengecekan integritas data: Pastikan penerima bantuan ini benar-benar terhubung dengan kategori yang dimaksud.
        if ($penerimaBantuan->kategori_bantuan_id !== $kategoriBantuan->id) {
            abort(403, 'Penerima bantuan ini tidak terhubung dengan kategori yang dimaksud.');
        }
        // Cek wilayah RW/RT untuk Admin RW/RT
        if ($user->isAdminRw() && ($penerimaBantuan->warga->rw_id ?? null) !== $user->rw_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RW Anda.');
        }
        if ($user->isAdminRt() && ($penerimaBantuan->warga->rt_id ?? null) !== $user->rt_id) {
            abort(403, 'Data penerima bantuan ini bukan milik wilayah RT Anda.');
        }
        // Super Admin has full access.

        DB::beginTransaction();
        try {
            foreach ($penerimaBantuan->photos as $photo) {
                Storage::delete(str_replace('/storage/', 'public/', $photo->file_path));
                $photo->delete();
            }
            $penerimaBantuan->delete();
            DB::commit();
            return redirect()->route('kategori-bantuan.penerima.index', $kategoriBantuan)->with('success', 'Penerima bantuan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus penerima bantuan: ' . $e->getMessage());
        }
    }

    /**
     * Export recipients to PDF.
     * Accessible by Admin Desa, Super Admin.
     */
    public function exportPdf(KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh PDF.');
        }
        // Global scope pada KategoriBantuan sudah memastikan $kategoriBantuan milik desa user.

        // Ambil semua data penerima untuk kategori ini (Global scope akan memfilter)
        $penerimas = PenerimaBantuan::with('warga.kartuKeluarga', 'warga.rt', 'warga.rw', 'kartuKeluarga.kepalaKeluarga', 'kartuKeluarga.rt', 'kartuKeluarga.rw')
            ->where('kategori_bantuan_id', $kategoriBantuan->id)
            ->get();

        // Load view PDF dan passing datanya
        // Kita juga perlu mengambil SuratSetting dan Desa untuk kop surat
        $suratSetting = \App\Models\SuratSetting::where('desa_id', $user->desa_id)->firstOrCreate();
        $desa = $suratSetting->desa; // Asumsi SuratSetting memiliki relasi ke Desa

        // Pisahkan penerima berdasarkan status
        $penerimasDiajukan = $penerimas->where('status_permohonan', 'Diajukan');
        $penerimasDisetujui = $penerimas->where('status_permohonan', 'Disetujui');
        $penerimasDitolak = $penerimas->where('status_permohonan', 'Ditolak');


        $pdf = Pdf::loadView('admin_desa.penerima_bantuan.pdf', compact('penerimasDiajukan', 'penerimasDisetujui', 'penerimasDitolak', 'kategoriBantuan', 'suratSetting', 'desa'));

        // Buat nama file yang dinamis
        $fileName = 'daftar-penerima-' . Str::slug($kategoriBantuan->nama_kategori) . '.pdf';

        // Download file PDF
        return $pdf->download($fileName);
    }

    /**
     * Export recipients to Excel.
     * Accessible by Admin Desa, Super Admin.
     */
    public function exportExcel(KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh Excel.');
        }
        // Global scope pada KategoriBantuan sudah memastikan $kategoriBantuan milik desa user.

        $fileName = 'daftar-penerima-' . Str::slug($kategoriBantuan->nama_kategori) . '.xlsx';

        return Excel::download(new PenerimaBantuanExport($kategoriBantuan->id), $fileName);
    }

    public function searchWarga(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            return response()->json(['results' => []], 403);
        }

        $searchTerm = $request->query('term');
        $kategoriBantuanId = $request->query('kategori_id');

        if (!$kategoriBantuanId) {
            return response()->json(['results' => []]);
        }

        $kategoriBantuan = KategoriBantuan::where('id', $kategoriBantuanId)->first();
        if (!$kategoriBantuan) {
            return response()->json(['results' => []]);
        }

        $kriteria = $kategoriBantuan->kriteria_json;
        $kriteria = is_string($kriteria) ? json_decode($kriteria, true) : ($kriteria ?? []);

        $queryWarga = Warga::with(['kartuKeluarga', 'rt', 'rw']);

        $isAnyCriteriaApplied = false;

        if (!empty($kriteria['status_keluarga'])) {
            $queryWarga->whereHas('kartuKeluarga', function ($q) use ($kriteria) {
                $q->whereIn('klasifikasi', $kriteria['status_keluarga']);
            });
            $isAnyCriteriaApplied = true;
        }

        if (!empty($kriteria['hubungan_keluarga'])) {
            $queryWarga->whereIn('hubungan_keluarga', $kriteria['hubungan_keluarga']);
            $isAnyCriteriaApplied = true;
        }

        if (isset($kriteria['memiliki_balita']) && $kriteria['memiliki_balita'] === true) {
            $queryWarga->whereJsonContains('status_khusus', 'Balita');
            $isAnyCriteriaApplied = true;
        }

        if (isset($kriteria['min_usia']) && is_numeric($kriteria['min_usia']) && isset($kriteria['max_usia']) && is_numeric($kriteria['max_usia'])) {
            $maxBirthDate = Carbon::now()->subYears($kriteria['min_usia'])->endOfDay();
            $minBirthDate = Carbon::now()->subYears($kriteria['max_usia'] + 1)->startOfDay();
            $queryWarga->whereBetween('tanggal_lahir', [$minBirthDate, $maxBirthDate]);
            $isAnyCriteriaApplied = true;
        } elseif (isset($kriteria['min_usia']) && is_numeric($kriteria['min_usia'])) {
            $maxBirthDate = Carbon::now()->subYears($kriteria['min_usia'])->endOfDay();
            $queryWarga->where('tanggal_lahir', '<=', $maxBirthDate);
            $isAnyCriteriaApplied = true;
        } elseif (isset($kriteria['max_usia']) && is_numeric($kriteria['max_usia'])) {
            $minBirthDate = Carbon::now()->subYears($kriteria['max_usia'] + 1)->startOfDay();
            $queryWarga->where('tanggal_lahir', '>=', $minBirthDate);
            $isAnyCriteriaApplied = true;
        }

        if (!empty($kriteria['jenis_kelamin'])) {
            $queryWarga->where('jenis_kelamin', $kriteria['jenis_kelamin']);
            $isAnyCriteriaApplied = true;
        }

        if (!empty($kriteria['status_khusus'])) {
            foreach ($kriteria['status_khusus'] as $status) {
                $queryWarga->whereJsonContains('status_khusus', $status);
            }
            $isAnyCriteriaApplied = true;
        }

        if (!$isAnyCriteriaApplied && empty($kriteria['hubungan_keluarga'])) {
             $queryWarga->where('hubungan_keluarga', 'Kepala Keluarga');
        }

        $existingRecipientWargaIds = PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuanId)
                                                    ->whereNotNull('warga_id')
                                                    ->pluck('warga_id')
                                                    ->toArray();
        if (!$kategoriBantuan->allow_multiple_recipients_per_kk) {
            $queryWarga->whereNotIn('id', $existingRecipientWargaIds);
        }

        if ($searchTerm) {
            $queryWarga->where(function($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nik', 'LIKE', "%{$searchTerm}%");
            });
        }

        $wargas = $queryWarga->limit(10)->get();

        $formattedResults = $wargas->map(function ($warga) {
            return [
                'id' => $warga->id,
                'text' => sprintf(
                    '%s (NIK: %s) - KK: %s (RW %s/RT %s)',
                    $warga->nama_lengkap,
                    $warga->nik,
                    $warga->kartuKeluarga->nomor_kk ?? '-',
                    $warga->rw->nomor_rw ?? '-',
                    $warga->rt->nomor_rt ?? '-'
                )
            ];
        });

        return response()->json(['results' => $formattedResults]);
    }

    public function searchKK(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            return response()->json(['results' => []], 403);
        }

        $searchTerm = $request->input('q');
        $kategoriBantuanId = $request->input('kategori_id');

        if (!$kategoriBantuanId) {
            return response()->json(['results' => []]);
        }

        $kategoriBantuan = KategoriBantuan::where('id', $kategoriBantuanId)->first();
        if (!$kategoriBantuan) {
            return response()->json(['results' => []]);
        }

        $kriteria = $kategoriBantuan->kriteria_json;
        $kriteria = is_string($kriteria) ? json_decode($kriteria, true) : ($kriteria ?? []);

        $queryKK = KartuKeluarga::with('kepalaKeluarga', 'rw', 'rt');

        $isAnyCriteriaApplied = false;

        if (!empty($kriteria['status_keluarga'])) {
            $queryKK->whereIn('klasifikasi', $kriteria['status_keluarga']);
            $isAnyCriteriaApplied = true;
        }

        if (isset($kriteria['memiliki_balita']) && is_numeric($kriteria['memiliki_balita']) && $kriteria['memiliki_balita'] === true) {
            $queryKK->whereHas('wargas', function ($q) {
                $q->whereJsonContains('status_khusus', 'Balita');
            });
            $isAnyCriteriaApplied = true;
        }

        if (isset($kriteria['min_usia']) && is_numeric($kriteria['min_usia']) && isset($kriteria['max_usia']) && is_numeric($kriteria['max_usia'])) {
            $maxBirthDate = Carbon::now()->subYears($kriteria['min_usia'])->endOfDay();
            $minBirthDate = Carbon::now()->subYears($kriteria['max_usia'] + 1)->startOfDay();
            $queryKK->whereHas('wargas', function ($q) use ($minBirthDate, $maxBirthDate) {
                $q->whereBetween('tanggal_lahir', [$minBirthDate, $maxBirthDate]);
            });
            $isAnyCriteriaApplied = true;
        } elseif (isset($kriteria['min_usia']) && is_numeric($kriteria['min_usia'])) {
            $maxBirthDate = Carbon::now()->subYears($kriteria['min_usia'])->endOfDay();
            $queryKK->whereHas('wargas', function ($q) use ($maxBirthDate) {
                $q->where('tanggal_lahir', '<=', $maxBirthDate);
            });
            $isAnyCriteriaApplied = true;
        } elseif (isset($kriteria['max_usia']) && is_numeric($kriteria['max_usia'])) {
            $minBirthDate = Carbon::now()->subYears($kriteria['max_usia'] + 1)->startOfDay();
            $queryKK->whereHas('wargas', function ($q) use ($minBirthDate) {
                $q->where('tanggal_lahir', '>=', $minBirthDate);
            });
            $isAnyCriteriaApplied = true;
        }

        if (!empty($kriteria['jenis_kelamin'])) {
            $queryKK->whereHas('wargas', function ($q) use ($kriteria) {
                $q->where('jenis_kelamin', $kriteria['jenis_kelamin']);
            });
            $isAnyCriteriaApplied = true;
        }

        if (!empty($kriteria['status_khusus'])) {
            foreach ($kriteria['status_khusus'] as $status) {
                $queryKK->whereHas('wargas', function ($q) use ($status) {
                    $q->whereJsonContains('status_khusus', $status);
                });
            }
            $isAnyCriteriaApplied = true;
        }

        if (!$isAnyCriteriaApplied && empty($kriteria['hubungan_keluarga'])) {
             $queryKK->whereHas('wargas', function($q) {
                 $q->where('hubungan_keluarga', 'Kepala Keluarga');
             });
        }

        $existingRecipientKKIds = PenerimaBantuan::where('kategori_bantuan_id', $kategoriBantuanId)
                                                 ->whereNotNull('kartu_keluarga_id')
                                                 ->pluck('kartu_keluarga_id')
                                                 ->toArray();
        if (!$kategoriBantuan->allow_multiple_recipients_per_kk) {
            $queryKK->whereNotIn('id', $existingRecipientKKIds);
        }

        if ($searchTerm) {
            $queryKK->where(function($q) use ($searchTerm) {
                $q->where('nomor_kk', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('kepalaKeluarga', function($q2) use ($searchTerm) {
                      $q2->where('nama_lengkap', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $kartuKeluargas = $queryKK->limit(10)->get();

        $formattedResults = $kartuKeluargas->map(function ($kk) {
            return [
                'id' => $kk->id,
                'text' => sprintf(
                    'KK: %s (Kepala: %s) (RW %s/RT %s) (%s)',
                    $kk->nomor_kk,
                    $kk->kepalaKeluarga->nama_lengkap ?? '-',
                    $kk->rw->nomor_rw ?? '-',
                    $kk->rt->nomor_rt ?? '-',
                    $kk->klasifikasi ?? '-'
                )
            ];
        });

        return response()->json(['results' => $formattedResults]);
    }
}
