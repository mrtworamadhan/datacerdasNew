<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\PengurusKelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk upload file

class KelompokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $subdomain)
    {
        // Global scope 'desa_id' akan otomatis memfilter
        $kelompoks = Kelompok::with('pengurus')->get(); // Load relasi pengurus
        return view('admin_desa.kelompok.index', compact('kelompoks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $subdomain)
    {
        return view('admin_desa.kelompok.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $subdomain)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sk_kepala_desa' => 'nullable|file|mimes:pdf|max:2048', // Validasi PDF max 2MB
            'pengurus.*.nama_pengurus' => 'required|string|max:255', // Validasi array pengurus
            'pengurus.*.jabatan' => 'required|string|max:255',
            'path_kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $skPath = null;
        if ($request->hasFile('sk_kepala_desa')) {
            $skPath = $request->file('sk_kepala_desa')->store('sk_kelompok');
            $skPath = Storage::url($skPath); // Dapatkan URL yang bisa diakses publik
        }

        $kopSuratPath = null;
        if ($request->hasFile('path_kop_surat')) {
            // Simpan file logo/kop surat
            $kopSuratPath = $request->file('path_kop_surat')->store('kop_surat', 'public');
        }

        // desa_id akan otomatis terisi oleh Global Scope Trait BelongsToDesa
        $kelompok = Kelompok::create([
            'nama_kelompok' => $request->nama_kelompok,
            'deskripsi' => $request->deskripsi,
            'sk_kepala_desa_path' => $skPath,
            'path_kop_surat'      => $kopSuratPath,
        ]);

        // Simpan data pengurus
        if ($request->has('pengurus')) {
            foreach ($request->pengurus as $pengurusData) {
                $kelompok->pengurus()->create($pengurusData);
            }
        }

        return redirect()->route('kelompok.index')->with('success', 'kelompok desa berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show( Kelompok $kelompok, string $subdomain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $subdomain, Kelompok $kelompok)
    {
        // Global scope akan memastikan hanya kelompok dari desa yang sama yang bisa diakses
        return view('admin_desa.kelompok.edit', compact('kelompok'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $subdomain, Kelompok $kelompok)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sk_kepala_desa' => 'nullable|file|mimes:pdf|max:2048',
            'pengurus.*.nama_pengurus' => 'required|string|max:255',
            'pengurus.*.jabatan' => 'required|string|max:255',
            'path_kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // <-- Validasi baru

        ]);

        // Update SK Kepala Desa jika ada file baru
        if ($request->hasFile('sk_kepala_desa')) {
            // Hapus SK lama jika ada
            if ($kelompok->sk_kepala_desa_path) {
                Storage::delete(str_replace('/storage/', 'public/', $kelompok->sk_kepala_desa_path));
            }
            $skPath = $request->file('sk_kepala_desa')->store('sk_kelompok');
            $validated['sk_kepala_desa_path'] = Storage::url($skPath);
        }

        if ($request->hasFile('path_kop_surat')) {
            // Hapus file lama jika ada
            if ($kelompok->path_kop_surat) {
                Storage::disk('public')->delete($kelompok->path_kop_surat);
            }
            $validated['path_kop_surat'] = $request->file('path_kop_surat')->store('kop_surat', 'public');
        }

        $kelompok->update(($validated));

        // Update pengurus: hapus yang lama, tambahkan yang baru (bisa lebih canggih nanti)
        $kelompok->pengurus()->delete(); // Hapus semua pengurus lama
        if ($request->has('pengurus')) {
            foreach ($request->pengurus as $pengurusData) {
                $kelompok->pengurus()->create($pengurusData);
            }
        }

        return redirect()->route('kelompok.index')->with('success', 'Data kelompok desa berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $subdomain, Kelompok $kelompok)
    {
        // Hapus file SK jika ada
        if ($kelompok->sk_kepala_desa_path) {
            Storage::delete(str_replace('/storage/', 'public/', $kelompok->sk_kepala_desa_path));
        }
        $kelompok->delete(); // Ini akan otomatis menghapus pengurus karena onDelete('cascade')
        return redirect()->route('kelompok.index')->with('success', 'kelompok desa berhasil dihapus!');
    }
}
