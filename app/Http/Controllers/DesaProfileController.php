<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\RW; // Masih dibutuhkan untuk relasi count
use App\Models\RT; // Masih dibutuhkan untuk relasi count
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesaProfileController extends Controller
{
    /**
     * Show the form for editing the authenticated user's desa profile.
     */
    public function edit()
    {
        $user = Auth::user();
        // Pastikan user adalah admin_desa atau super_admin dan punya desa_id
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Load relasi RWS dan RTS untuk menghitung jumlah terdaftar
        $desa = Desa::with('rws', 'rts')->findOrFail($user->desa_id);
        return view('admin_desa.profile.edit', compact('desa'));
    }

    /**
     * Update the authenticated user's desa profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui profil ini.');
        }

        $desa = Desa::findOrFail($user->desa_id);

        $request->validate([
            'nama_kades' => 'nullable|string|max:255',
            'alamat_desa' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            // 'jumlah_rw' dan 'jumlah_rt' dihapus dari validasi karena tidak lagi diinput di sini
        ]);

        // Update profil desa
        $desa->update($request->only([
            'nama_kades', 'alamat_desa', 'kode_pos'
        ]));

        // Logic Generasi Akun RW/RT Otomatis dihapus dari sini

        return redirect()->route('admin_desa.profile.edit')->with('success', 'Profil desa berhasil diperbarui!');
    }
}
