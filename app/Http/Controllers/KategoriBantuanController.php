<?php

namespace App\Http\Controllers;

use App\Models\KategoriBantuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Perbaikan: dari Illuminate\Support\Facades\DB
use Illuminate\Support\Str; // Untuk Str::slug di view

class KategoriBantuanController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible by Admin Desa, Super Admin, Admin RW, Admin RT.
     */
    public function index(string $subdomain)
    {
        $user = Auth::user();
        // Cek hak akses: Admin Desa, Super Admin, Admin RW, Admin RT bisa melihat index
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat kategori bantuan.');
        }

        // Global scope 'desa_id_and_area' akan otomatis memfilter kategori bantuan berdasarkan user yang login
        $kategoriBantuans = KategoriBantuan::all();
        return view('admin_desa.kategori_bantuan.index', compact('kategoriBantuans'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible only by Admin Desa and Super Admin.
     */
    public function create(string $subdomain)
    {
        $user = Auth::user();
        // Cek hak akses: Hanya Admin Desa dan Super Admin yang bisa membuat kategori
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat kategori bantuan.');
        }

        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        $statusKhususOptions = ['Disabilitas', 'Lansia', 'Ibu Hamil', 'Balita', 'Penerima PKH', 'Penerima BPNT', 'Lainnya'];
        $hubunganKeluargaOptions = ['Kepala Keluarga', 'Anak', 'Cucu', 'Istri', 'Menantu', 'Suami', 'Saudara', 'Kakak', 'Adik', 'Lainnya'];
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
        $additionalFieldTypes = ['text', 'number', 'date', 'textarea', 'checkbox', 'file']; // Tipe field tambahan yang didukung

        return view('admin_desa.kategori_bantuan.create', compact(
            'klasifikasiOptions', 'statusKhususOptions', 'hubunganKeluargaOptions', 'jenisKelaminOptions', 'additionalFieldTypes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible only by Admin Desa and Super Admin.
     */
    public function store(Request $request, string $subdomain)
    {
        $user = Auth::user();
        // Cek hak akses: Hanya Admin Desa dan Super Admin yang bisa menyimpan kategori
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melakukan aksi ini.');
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_bantuans,nama_kategori,NULL,id,desa_id,'.$user->desa_id, // Unique per desa
            'deskripsi' => 'nullable|string',
            'allow_multiple_recipients_per_kk' => 'boolean',
            'is_active_for_submission' => 'boolean', // Validasi untuk kolom baru
            // Validasi kriteria
            'kriteria_status_keluarga' => 'nullable|array',
            'kriteria_status_keluarga.*' => 'in:Pra-Sejahtera,Sejahtera I,Sejahtera II,Sejahtera III,Sejahtera III Plus',
            'kriteria_memiliki_balita' => 'boolean',
            'kriteria_hubungan_keluarga' => 'nullable|array',
            'kriteria_hubungan_keluarga.*' => 'in:Kepala Keluarga,Anak,Cucu,Istri,Menantu,Suami,Saudara,Kakak,Adik,Lainnya',
            'kriteria_min_usia' => 'nullable|integer|min:0',
            'kriteria_max_usia' => 'nullable|integer|min:0|gte:kriteria_min_usia',
            'kriteria_jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'kriteria_status_khusus' => 'nullable|array',
            'kriteria_status_khusus.*' => 'in:Disabilitas,Lansia,Ibu Hamil,Balita,Penerima PKH,Penerima BPNT,Lainnya',
            // Validasi untuk field tambahan
            'additional_fields.*.name' => 'required|string|max:255',
            'additional_fields.*.type' => 'required|in:text,number,date,textarea,checkbox,file', // Menambahkan 'file'
            'additional_fields.*.required' => 'boolean',
        ]);

        $kriteria = [];
        if ($request->has('kriteria_status_keluarga')) {
            $kriteria['status_keluarga'] = $request->kriteria_status_keluarga;
        }
        if ($request->has('kriteria_memiliki_balita')) {
            $kriteria['memiliki_balita'] = (bool)$request->kriteria_memiliki_balita;
        }
        if ($request->has('kriteria_hubungan_keluarga')) {
            $kriteria['hubungan_keluarga'] = $request->kriteria_hubungan_keluarga;
        }
        if ($request->filled('kriteria_min_usia')) {
            $kriteria['min_usia'] = (int)$request->kriteria_min_usia;
        }
        if ($request->filled('kriteria_max_usia')) {
            $kriteria['max_usia'] = (int)$request->kriteria_max_usia; // Perbaikan: $request->max_usia
        }
        if ($request->filled('kriteria_jenis_kelamin')) {
            $kriteria['jenis_kelamin'] = $request->kriteria_jenis_kelamin;
        }
        if ($request->has('kriteria_status_khusus')) {
            $kriteria['status_khusus'] = $request->kriteria_status_khusus;
        }

        // Proses field tambahan
        $additionalFields = [];
        if ($request->has('additional_fields')) {
            foreach ($request->additional_fields as $field) {
                $additionalFields[] = [
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'required' => isset($field['required']) ? (bool)$field['required'] : false,
                ];
            }
        }

        DB::beginTransaction();
        try {
            KategoriBantuan::create([
                'desa_id' => $user->desa_id,
                'nama_kategori' => $request->nama_kategori,
                'deskripsi' => $request->deskripsi,
                'kriteria_json' => json_encode($kriteria), // Simpan sebagai JSON string
                'allow_multiple_recipients_per_kk' => (bool)$request->allow_multiple_recipients_per_kk,
                'is_active_for_submission' => (bool)$request->is_active_for_submission, // Simpan kolom baru
                'required_additional_fields_json' => json_encode($additionalFields), // Simpan field tambahan
            ]);

            DB::commit();
            return redirect()->route('kategori-bantuan.index')->with('success', 'Kategori bantuan berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan kategori bantuan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible only by Admin Desa and Super Admin.
     */
    public function edit(string $subdomain, KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses: Hanya Admin Desa dan Super Admin yang bisa mengedit kategori
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit kategori bantuan.');
        }
        // Pastikan kategori bantuan milik desa user yang login
        if ($kategoriBantuan->desa_id !== $user->desa_id) {
            abort(403, 'Kategori bantuan ini bukan milik desa Anda.');
        }

        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        $statusKhususOptions = ['Disabilitas', 'Lansia', 'Ibu Hamil', 'Balita', 'Penerima PKH', 'Penerima BPNT', 'Lainnya'];
        $hubunganKeluargaOptions = ['Kepala Keluarga', 'Anak', 'Cucu', 'Istri', 'Menantu', 'Suami', 'Saudara', 'Kakak', 'Adik', 'Lainnya'];
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
        $additionalFieldTypes = ['text', 'number', 'date', 'textarea', 'checkbox', 'file']; // Tipe field tambahan yang didukung

        return view('admin_desa.kategori_bantuan.edit', compact(
            'kategoriBantuan', 'klasifikasiOptions', 'statusKhususOptions', 'hubunganKeluargaOptions', 'jenisKelaminOptions', 'additionalFieldTypes'
        ));
    }

    /**
     * Update the specified resource in storage.
     * Accessible only by Admin Desa and Super Admin.
     */
    public function update(Request $request, string $subdomain, KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses: Hanya Admin Desa dan Super Admin yang bisa memperbarui kategori
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui data ini.');
        }
        // Pastikan kategori bantuan milik desa user yang login
        if ($kategoriBantuan->desa_id !== $user->desa_id) {
            abort(403, 'Kategori bantuan ini bukan milik desa Anda.');
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_bantuans,nama_kategori,'.$kategoriBantuan->id.',id,desa_id,'.$user->desa_id, // Unique per desa, kecuali dirinya sendiri
            'deskripsi' => 'nullable|string',
            'allow_multiple_recipients_per_kk' => 'boolean',
            'is_active_for_submission' => 'boolean', // Validasi untuk kolom baru
            // Validasi kriteria
            'kriteria_status_keluarga' => 'nullable|array',
            'kriteria_status_keluarga.*' => 'in:Pra-Sejahtera,Sejahtera I,Sejahtera II,Sejahtera III,Sejahtera III Plus',
            'kriteria_memiliki_balita' => 'boolean',
            'kriteria_hubungan_keluarga' => 'nullable|array',
            'kriteria_hubungan_keluarga.*' => 'in:Kepala Keluarga,Anak,Cucu,Istri,Menantu,Suami,Saudara,Kakak,Adik,Lainnya',
            'kriteria_min_usia' => 'nullable|integer|min:0',
            'kriteria_max_usia' => 'nullable|integer|min:0|gte:kriteria_min_usia',
            'kriteria_jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'kriteria_status_khusus' => 'nullable|array',
            'kriteria_status_khusus.*' => 'in:Disabilitas,Lansia,Ibu Hamil,Balita,Penerima PKH,Penerima BPNT,Lainnya',
            // Validasi untuk field tambahan
            'additional_fields.*.name' => 'required|string|max:255',
            'additional_fields.*.type' => 'required|in:text,number,date,textarea,checkbox,file',
            'additional_fields.*.required' => 'boolean',
        ]);

        $kriteria = [];
        if ($request->has('kriteria_status_keluarga')) {
            $kriteria['status_keluarga'] = $request->kriteria_status_keluarga;
        }
        if ($request->has('kriteria_memiliki_balita')) {
            $kriteria['memiliki_balita'] = (bool)$request->kriteria_memiliki_balita;
        }
        if ($request->has('kriteria_hubungan_keluarga')) {
            $kriteria['hubungan_keluarga'] = $request->kriteria_hubungan_keluarga;
        }
        if ($request->filled('kriteria_min_usia')) {
            $kriteria['min_usia'] = (int)$request->kriteria_min_usia;
        }
        if ($request->filled('kriteria_max_usia')) {
            $kriteria['max_usia'] = (int)$request->max_usia;
        }
        if ($request->filled('kriteria_jenis_kelamin')) {
            $kriteria['jenis_kelamin'] = $request->kriteria_jenis_kelamin;
        }
        if ($request->has('kriteria_status_khusus')) {
            $kriteria['status_khusus'] = $request->kriteria_status_khusus;
        }

        // Proses field tambahan
        $additionalFields = [];
        if ($request->has('additional_fields')) {
            foreach ($request->additional_fields as $field) {
                $additionalFields[] = [
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'required' => isset($field['required']) ? (bool)$field['required'] : false,
                ];
            }
        }

        DB::beginTransaction();
        try {
            $kategoriBantuan->update([
                'nama_kategori' => $request->nama_kategori,
                'deskripsi' => $request->deskripsi,
                'kriteria_json' => json_encode($kriteria),
                'allow_multiple_recipients_per_kk' => (bool)$request->allow_multiple_recipients_per_kk,
                'is_active_for_submission' => (bool)$request->is_active_for_submission,
                'required_additional_fields_json' => json_encode($additionalFields), // Simpan field tambahan
            ]);

            DB::commit();
            return redirect()->route('kategori-bantuan.index')->with('success', 'Kategori bantuan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui kategori bantuan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $subdomain, KategoriBantuan $kategoriBantuan)
    {
        $user = Auth::user();
        // Cek hak akses: Hanya Admin Desa dan Super Admin yang bisa menghapus kategori
        if (!$user->isAdminDesa() && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus data ini.');
        }
        // Pastikan kategori bantuan milik desa user yang login
        if ($kategoriBantuan->desa_id !== $user->desa_id) {
            abort(403, 'Kategori bantuan ini bukan milik desa Anda.');
        }

        DB::beginTransaction();
        try {
            $kategoriBantuan->delete(); // Ini akan otomatis menghapus penerima bantuan yang terhubung
            DB::commit();
            return redirect()->route('kategori-bantuan.index')->with('success', 'Kategori bantuan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus kategori bantuan: ' . $e->getMessage());
        }
    }
}
