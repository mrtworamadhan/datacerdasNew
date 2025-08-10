<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\SuratSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WargaController extends Controller
{
    /**
     * Menampilkan halaman pencarian warga untuk diupdate.
     */
    public function index(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');

        return view('portal.warga.index', compact('desa', 'logo'));
    }

    /**
     * Menampilkan form untuk mengedit status warga.
     */
    public function edit(string $subdomain, Warga $warga)
    {
        // Otorisasi sederhana: pastikan RT/RW hanya bisa mengedit warga di wilayahnya
        // (Nanti bisa disempurnakan dengan Gate/Policy)
        $user = auth()->user();
        if (($user->isAdminRw() && $user->rw_id != $warga->rw_id) || ($user->isAdminRt() && $user->rt_id != $warga->rt_id)) {
            abort(403, 'Anda tidak berhak mengubah data warga ini.');
        }
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');

        $statusKhususOptions = ['Disabilitas', 'Lansia', 'Ibu Hamil', 'Balita', 'Penerima PKH', 'Penerima BPNT', 'Lainnya'];
        

        $warga->status_khusus = is_array($warga->status_khusus)
        ? $warga->status_khusus
        : json_decode($warga->status_khusus, true) ?? [];

        return view('portal.warga.edit', compact('warga', 'statusKhususOptions', 'desa', 'logo'));
    }

    /**
     * Mengupdate data status warga.
     */
    public function update(Request $request, string $subdomain, Warga $warga)
    {
        $validated = $request->validate([
            'status_kependudukan' => 'required|string',
            'status_khusus' => 'nullable|array',
        ]);

        $warga->update($validated);

        return redirect()->route('portal.warga.index', ['subdomain' => $subdomain])
                         ->with('success', 'Status warga ' . $warga->nama_lengkap . ' berhasil diperbarui.');
    }
}