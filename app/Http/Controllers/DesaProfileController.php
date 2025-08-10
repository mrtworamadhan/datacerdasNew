<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\RW; // Masih dibutuhkan untuk relasi count
use App\Models\RT; // Masih dibutuhkan untuk relasi count
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
    public function update(Request $request, string $subdomain)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui profil ini.');
        }

        $desa = Desa::findOrFail($user->desa_id);

        $validated = $request->validate([
            'nama_kades' => 'nullable|string|max:255',
            'alamat_desa' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'sambutan_kades' => 'nullable|string',
            'foto_kades_path' => 'nullable|image|mimes:jpeg,png,jpg',
            'path_logo' => 'nullable|image|mimes:png'
        ]);

        if ($request->hasFile('foto_kades_path')) {
            $image = $request->file('foto_kades_path');

            // Hapus foto lama kalau ada
            if ($desa->foto_kades_path) {
                Storage::disk('public')->delete($desa->foto_kades_path);
            }

            // Buat nama file unik
            $fileName = time() . '_' . $image->getClientOriginalName();

            // Resize menggunakan Intervention Image v3
            $manager = new ImageManager(new Driver());
            $resizedImage = $manager->read($image)->scale(width: 800);

            // Simpan hasil resize ke storage
            Storage::disk('public')->put('foto_kades/' . $fileName, (string) $resizedImage->toJpeg(80));

            // Simpan path ke validated
            $validated['foto_kades_path'] = 'foto_kades/' . $fileName;
        }
        if ($request->hasFile('path_logo')) {
            $image = $request->file('path_logo');

            // Hapus logo lama kalau ada
            if ($desa->path_logo) {
                Storage::disk('public')->delete($desa->path_logo);
            }

            // Pastikan namanya .png
            $fileName = time() . '_' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.png';

            // Simpan langsung file asli (tetap transparan)
            Storage::disk('public')->putFileAs('logo_desa', $image, $fileName);

            // Simpan path ke validated
            $validated['path_logo'] = 'logo_desa/' . $fileName;
        }

        // Update record
        $desa->update($validated);

        return redirect()->route('admin_desa.profile.edit')->with('success', 'Profil desa berhasil diperbarui!');
    }

}
