<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\KartuKeluarga;
use App\Models\SuratSetting;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KkController extends Controller
{
    public function index(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
       
        $kartuKeluarga = KartuKeluarga::with('kepalaKeluarga', 'rw', 'rt')->get();
        return view('portal.keluarga.index', compact('kartuKeluarga','desa'));
    }

    public function edit(string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
                
        $rws = RW::all();
        $rts = RT::where('rw_id', $kartuKeluarga->rw_id)->get(); 

        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        
        return view('portal.keluarga.edit', compact(
            'kartuKeluarga', 'rws', 'rts', 'klasifikasiOptions', 'desa'
        ));
    }

    public function update(Request $request, string $subdomain, KartuKeluarga $kartuKeluarga)
    {
        $user = Auth::user();
        // Global scope should handle filtering. This is a safeguard.
        if ($user->isAdminDesa() && $kartuKeluarga->desa_id !== $user->desa_id) {
            abort(403, 'Kartu Keluarga ini bukan milik desa Anda.');
        } elseif ($user->isAdminRw() && ($kartuKeluarga->rw_id !== $user->rw_id || $kartuKeluarga->desa_id !== $user->desa_id)) {
            abort(403, 'Kartu Keluarga ini bukan milik wilayah RW Anda.');
        } elseif ($user->isAdminRt() && ($kartuKeluarga->rt_id !== $user->rt_id || $kartuKeluarga->rw_id !== $user->rw_id || $kartuKeluarga->desa_id !== $user->desa_id)) {
            abort(403, 'Kartu Keluarga ini bukan milik wilayah RT Anda.');
        }
        // Super Admin has full access.

        $request->validate([
            'nomor_kk' => 'required|string|max:20|unique:kartu_keluargas,nomor_kk,'.$kartuKeluarga->id.',id,desa_id,'.$user->desa_id,
            'alamat_lengkap_kk' => 'required|string|max:255',
            'klasifikasi' => 'required|in:Pra-Sejahtera,Sejahtera I,Sejahtera II,Sejahtera III,Sejahtera III Plus',
        ]);

        $kartuKeluarga->update([
            'nomor_kk' => $request->nomor_kk,
            'alamat_lengkap' => $request->alamat_lengkap_kk,
            'klasifikasi' => $request->klasifikasi,
        ]);

        return redirect()->route('portal.kartuKeluarga.index')->with('success', 'Kartu Keluarga berhasil diperbarui!');
    }
}
