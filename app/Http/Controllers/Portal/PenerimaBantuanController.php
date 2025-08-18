<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\KategoriBantuan;
use App\Models\PenerimaBantuan;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Models\PenerimaBantuanPhoto;
use App\Models\SuratSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PenerimaBantuanController extends Controller
{
    /**
     * Langkah 1: Menampilkan halaman untuk memilih kategori bantuan.
     */
    public function pilihBantuan(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        
        // Trait/Scope desa akan otomatis memfilter kategori bantuan
        $kategoriBantuans = KategoriBantuan::where('is_active_for_submission', 1)->get();

        return view('portal.bantuan.pilih_bantuan', compact('kategoriBantuans', 'desa'));
    }

    public function pilihWarga(string $subdomain, KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        
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
        // Kirim data Kategori Bantuan yang dipilih ke view
        $penerimaBantuans = $queryPenerima->latest()->get();
        
        return view('portal.bantuan.pilih_warga', compact('kategoriBantuan', 'penerimaBantuans', 'stats','desa','requiredAdditionalFields'));
    }

    public function create(string $subdomain, KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses: Admin Desa, Admin RW, Admin RT bisa mengajukan
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengajukan penerima bantuan.');
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

        return view('portal.bantuan.penerima_bantuan', compact('kategoriBantuan', 'calonPenerimaWarga', 'calonPenerimaKK', 'requiredAdditionalFields'));
    }
    public function store(Request $request, string $subdomain, KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses untuk menyimpan pengajuan penerima bantuan

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
            return redirect()->route('portal.bantuan.index', $kategoriBantuan)->with('success', 'Penerima bantuan berhasil diajukan untuk verifikasi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengajukan penerima bantuan: ' . $e->getMessage())->withInput();
        }
    }

}