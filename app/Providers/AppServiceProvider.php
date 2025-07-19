<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use App\Models\CompanySetting; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        View::composer([
            'welcome', // Halaman welcome
            'layouts.public', // Layout untuk halaman publik (login, register, dll.)
            'auth.subscription-expired', // Halaman langganan berakhir
            // Tambahkan views lain yang mungkin membutuhkan company settings di masa depan
            // 'public.fasum.index', // Jika public fasum index juga butuh footer/navbar global
        ], function ($view) {
            // Ambil semua pengaturan dan key by 'key'
            $companySettings = CompanySetting::all()->keyBy('key');

            // Definisikan pengaturan yang diharapkan dan nilai default-nya
            // Ini penting agar di view tidak ada error jika setting belum ada di DB
            $defaultSettings = [
                'whatsapp_number' => ['value' => '+6281234567890', 'description' => 'Nomor WhatsApp untuk kontak dan perpanjangan langganan'],
                'company_email' => ['value' => 'info@desacerdas.id', 'description' => 'Email kontak resmi perusahaan'],
                'company_address' => ['value' => 'Jl. Contoh No. 1, Kota Contoh, Provinsi Contoh', 'description' => 'Alamat kantor pusat perusahaan'],
                'facebook_url' => ['value' => 'https://facebook.com/desacerdas', 'description' => 'URL profil Facebook perusahaan'],
                'instagram_url' => ['value' => 'https://instagram.com/desacerdas', 'description' => 'URL profil Instagram perusahaan'],
                'tiktok_url' => ['value' => 'https://facebook.com/desacerdas', 'description' => 'URL profil Facebook perusahaan'],
                'youtube_url' => ['value' => 'https://instagram.com/desacerdas', 'description' => 'URL profil Instagram perusahaan'],
                'privacy_policy_url' => ['value' => '/privacy-policy', 'description' => 'URL halaman Kebijakan Privasi'],
                'terms_of_service_url' => ['value' => '/terms-of-service', 'description' => 'URL halaman Syarat & Ketentuan Layanan'],
            ];

            // Gabungkan pengaturan yang ada dengan default, agar semua field muncul di view
            // dan memiliki nilai default jika belum ada di DB
            $finalSettings = collect($defaultSettings)->mapWithKeys(function ($data, $key) use ($companySettings) {
                return [$key => $companySettings->get($key) ? $companySettings->get($key)->value : $data['value']];
            });

            $view->with('companySettings', $finalSettings);
        });
    }
}
