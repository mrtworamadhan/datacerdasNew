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
        $desa = app('tenant');

        // Gunakan ID desa untuk memfilter semua query
        $desaId = $desa->id;

        // Hitung data statistik publik yang relevan untuk desa ini
        $stats = [
            'jumlah_warga' => Warga::where('desa_id', $desaId)->count(),
            'jumlah_kk' => KartuKeluarga::where('desa_id', $desaId)->count(),
            'jumlah_rw' => Rw::where('desa_id', $desaId)->count(),
            'jumlah_rt' => Rt::where('desa_id', $desaId)->count(),
        ];
        
        // Ambil data lain dengan filter desa_id
        $jumlahWarga = $stats['jumlah_warga'];
        $jumlahKk = $stats['jumlah_kk'];
        $fasums = Fasum::where('desa_id', $desaId)->latest()->take(3)->get();
        $perangkatDesa = PerangkatDesa::where('desa_id', $desaId)->get();
        $lembagas = Lembaga::where('desa_id', $desaId)->whereNotNull('path_kop_surat')->get();
        $kelompoks = Kelompok::where('desa_id', $desaId)->whereNotNull('path_kop_surat')->get();
        
        $mitraDesa = $lembagas->concat($kelompoks);

        // Kirim semua data ke view
        return view('public.welcome_desa', compact('desa', 
        'stats', 'fasums', 'jumlahWarga', 'jumlahKk', 'perangkatDesa', 'mitraDesa'));
    }
}