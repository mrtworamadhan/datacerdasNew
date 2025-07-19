<?php

namespace App\Http\Controllers;

use App\Models\PerangkatDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk mengakses user yang login

class PerangkatDesaController extends Controller
{
    public function index()
    {
        // Global scope 'desa_id' akan otomatis memfilter berdasarkan desa_id user yang login
        $perangkatDesas = PerangkatDesa::all();
        return view('admin_desa.perangkat_desa.index', compact('perangkatDesas'));
    }

    public function create()
    {
        // Tidak perlu lagi mengirim $users ke view karena user_id akan otomatis
        return view('admin_desa.perangkat_desa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            // 'user_id' tidak lagi divalidasi karena akan diisi otomatis
        ]);

        // desa_id akan otomatis terisi oleh Global Scope Trait BelongsToDesa
        // user_id akan diisi dengan ID user yang sedang login
        PerangkatDesa::create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'user_id' => Auth::id(), // Otomatis tautkan ke user yang sedang login
        ]);

        return redirect()->route('perangkat-desa.index')->with('success', 'Perangkat desa berhasil ditambahkan!');
    }

    public function edit(PerangkatDesa $perangkatDesa)
    {
        // Tidak perlu lagi mengirim $users ke view
        return view('admin_desa.perangkat_desa.edit', compact('perangkatDesa'));
    }

    public function update(Request $request, PerangkatDesa $perangkatDesa)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            // 'user_id' tidak lagi divalidasi karena akan diisi otomatis
        ]);

        // desa_id tidak perlu di-set ulang karena sudah ada
        // user_id akan diupdate dengan ID user yang sedang login
        $perangkatDesa->update([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'user_id' => Auth::id(), // Otomatis tautkan ke user yang sedang login
        ]);

        return redirect()->route('perangkat-desa.index')->with('success', 'Data perangkat desa berhasil diperbarui!');
    }

    public function destroy(PerangkatDesa $perangkatDesa)
    {
        $perangkatDesa->delete();
        return redirect()->route('perangkat-desa.index')->with('success', 'Perangkat desa berhasil dihapus!');
    }
}
