<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\SuratSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama untuk portal.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');
        // Di sini kita bisa tambahkan logika untuk mengambil data ringkas
        // yang relevan bagi user, misalnya jumlah pengajuan surat terakhir, dll.

        return view('portal.dashboard', compact('user', 'desa', 'logo'));
    }
}