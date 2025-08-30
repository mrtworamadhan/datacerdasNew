<?php

namespace App\Http\Controllers;

use App\Models\SuratSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratSettingController extends Controller
{
    public function edit(string $subdomain)
    {
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
            if ($setting->path_kop_surat) {
                Storage::disk('public')->delete($setting->path_kop_surat);
            }
            $validated['path_kop_surat'] = $request->file('path_kop_surat')->store('kop-surat', 'public');
        }
        if ($request->hasFile('path_logo_pemerintah')) {
            if ($setting->path_kop_surat) {
                Storage::disk('public')->delete($setting->path_logo_pemerintah);
            }
            $validated['path_logo_pemerintah'] = $request->file('path_logo_pemerintah')->store('logo_pemerintah', 'public');
        }
        if ($request->hasFile('path_ttd')) {
            if ($setting->path_ttd) {
                Storage::disk('public')->delete($setting->path_ttd);
            }
            $validated['path_ttd'] = $request->file('path_ttd')->store('path_ttd', 'public');
        }

        $setting->update($validated);

        return redirect()->back()->with('success', 'Pengaturan surat berhasil diperbarui.');
    }
}