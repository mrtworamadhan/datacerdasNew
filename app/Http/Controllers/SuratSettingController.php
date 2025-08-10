<?php

namespace App\Http\Controllers;

use App\Models\SuratSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratSettingController extends Controller
{
    public function edit(string $subdomain)
    {
        // Ambil data setting untuk desa yang aktif, atau buat baru jika belum ada
        $setting = SuratSetting::firstOrCreate(['desa_id' => auth()->user()->desa_id]);
        return view('admin_desa.surat_setting.edit', compact('setting'));
    }

    public function update(Request $request, string $subdomain)
    {
        $setting = SuratSetting::firstOrCreate(['desa_id' => auth()->user()->desa_id]);

        $validated = $request->validate([
            'penanda_tangan_nama' => 'nullable|string|max:255',
            'penanda_tangan_jabatan' => 'nullable|string|max:255',
            'path_kop_surat' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'path_logo_pemerintah' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'path_ttd' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        if ($request->hasFile('path_kop_surat')) {
            // Hapus kop surat lama jika ada
            if ($setting->path_kop_surat) {
                Storage::disk('public')->delete($setting->path_kop_surat);
            }
            // Simpan yang baru
            $validated['path_kop_surat'] = $request->file('path_kop_surat')->store('kop-surat', 'public');
        }
        if ($request->hasFile('path_logo_pemerintah')) {
            // Hapus kop surat lama jika ada
            if ($setting->path_kop_surat) {
                Storage::disk('public')->delete($setting->path_logo_pemerintah);
            }
            // Simpan yang baru
            $validated['path_logo_pemerintah'] = $request->file('path_logo_pemerintah')->store('logo_pemerintah', 'public');
        }
        if ($request->hasFile('path_ttd')) {
            // Hapus kop surat lama jika ada
            if ($setting->path_ttd) {
                Storage::disk('public')->delete($setting->path_ttd);
            }
            // Simpan yang baru
            $validated['path_ttd'] = $request->file('path_ttd')->store('path_ttd', 'public');
        }

        $setting->update($validated);

        return redirect()->back()->with('success', 'Pengaturan surat berhasil diperbarui.');
    }
}