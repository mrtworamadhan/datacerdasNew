<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warga;
use App\Models\Fasum;
use App\Models\KartuKeluarga;
use App\Models\PerangkatDesa;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Lembaga;
use App\Models\Kelompok;

class PublicDesaController extends Controller
{
    /**
     * Menampilkan halaman welcome publik untuk sebuah desa (tenant).
     */
    public function welcome()
    {
        // Ambil data desa yang aktif dari instance 'tenant'
        // yang sudah disiapkan oleh TenantMiddleware kita.
        $desa = app('tenant');

        // Hitung data statistik publik yang relevan untuk desa ini
        $jumlahWarga = Warga::count(); // Trait/Scope akan otomatis memfilter
        $stats = [
            'jumlah_warga' => Warga::count(), // Trait/Scope akan otomatis memfilter
            'jumlah_kk' => KartuKeluarga::count(),
            'jumlah_rw' => Rw::count(),
            'jumlah_rt' => Rt::count(),
        ];
        $jumlahKk = KartuKeluarga::count(); // Trait/Scope akan otomatis memfilter
        $fasums = Fasum::latest()->take(3)->get();
        $perangkatDesa = PerangkatDesa::get();
        $lembagas = Lembaga::whereNotNull('path_kop_surat')->get();
        $kelompoks = Kelompok::whereNotNull('path_kop_surat')->get();
        
        $mitraDesa = $lembagas->concat($kelompoks);

        // Kirim semua data ke view
        return view('public.welcome_desa', compact('desa', 
        'stats', 'fasums', 'jumlahWarga', 'jumlahKk', 'perangkatDesa', 'mitraDesa'));
    }
}