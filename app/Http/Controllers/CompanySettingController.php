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
        $settings = CompanySetting::all()->keyBy('key');

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

        $settingsData = $request->except('_token', '_method'); 

        DB::beginTransaction();
        try {
            foreach ($settingsData as $key => $value) {
                $originalKey = Str::after($key, 'setting_');
                $setting = CompanySetting::firstOrNew(['key' => $originalKey]);
                $setting->value = $value;
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