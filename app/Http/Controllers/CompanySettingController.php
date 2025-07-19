<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule; // Import Rule

class CompanySettingController extends Controller
{
    /**
     * Display a listing of company settings.
     * Accessible only by Super Admin.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola pengaturan perusahaan.');
        }

        // Ambil semua pengaturan yang ada. Jika belum ada, buat default.
        $settings = CompanySetting::all()->keyBy('key');

        // Definisikan pengaturan yang diharapkan dan nilai default-nya
        $defaultSettings = [
            'whatsapp_number' => [
                'value' => '6281234567890',
                'description' => 'Nomor WhatsApp untuk kontak dan perpanjangan langganan (gunakan format internasional tanpa +)',
            ],
            'company_email' => [
                'value' => 'info@datacerdas.id',
                'description' => 'Email kontak resmi perusahaan',
            ],
            'company_address' => [
                'value' => 'Jl. Contoh No. 1, Kota Contoh, Provinsi Contoh',
                'description' => 'Alamat kantor pusat perusahaan',
            ],
            'facebook_url' => [
                'value' => 'https://facebook.com/datacerdas',
                'description' => 'URL profil Facebook perusahaan',
            ],
            'instagram_url' => [
                'value' => 'https://instagram.com/dataacerdas',
                'description' => 'URL profil Instagram perusahaan',
            ],
            'tiktok_url' => [
                'value' => 'https://tiktok.com/dataacerdas',
                'description' => 'URL profil Tiktok perusahaan',
            ],
            'youtube_url' => [
                'value' => 'https://youtube.com/dataacerdas',
                'description' => 'URL profil Youtube perusahaan',
            ],
            'privacy_policy_url' => [
                'value' => '/privacy-policy',
                'description' => 'URL halaman Kebijakan Privasi',
            ],
            'terms_of_service_url' => [
                'value' => '/terms-of-service',
                'description' => 'URL halaman Syarat & Ketentuan Layanan',
            ],
            // Tambahkan pengaturan default lainnya di sini jika diperlukan
        ];

        // Gabungkan pengaturan yang ada dengan default, agar semua field muncul di form
        foreach ($defaultSettings as $key => $data) {
            if (!$settings->has($key)) {
                $settings->put($key, new CompanySetting(['key' => $key, 'value' => $data['value'], 'description' => $data['description']]));
            } else {
                // Update deskripsi jika ada perubahan di defaultSettings
                $settings[$key]->description = $data['description'];
            }
        }
        
        return view('superadmin.company_settings.index', compact('settings'));
    }

    /**
     * Update company settings in storage.
     * Accessible only by Super Admin.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui pengaturan perusahaan.');
        }

        // Ambil semua pengaturan dari request yang diawali dengan 'setting_'
        $settingsData = $request->except('_token', '_method'); // Kecualikan token dan method spoofing

        DB::beginTransaction();
        try {
            foreach ($settingsData as $key => $value) {
                // Hapus prefix 'setting_' untuk mendapatkan kunci asli
                $originalKey = Str::after($key, 'setting_');

                // Cari pengaturan berdasarkan kunci, atau buat baru jika tidak ada
                $setting = CompanySetting::firstOrNew(['key' => $originalKey]);
                
                // Update nilai dan simpan
                $setting->value = $value;
                // Description tidak diupdate dari form, hanya dari defaultSettings di index()
                $setting->save();
            }

            DB::commit();
            return redirect()->route('company-settings.index')->with('success', 'Pengaturan perusahaan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui pengaturan perusahaan: ' . $e->getMessage())->withInput();
        }
    }
}