<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\PengurusLembaga; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk upload file

class LembagaController extends Controller
{
    public function index(string $subdomain)
    {
        // Global scope 'desa_id' akan otomatis memfilter
        $lembagas = Lembaga::with('pengurus')->get(); // Load relasi pengurus
        return view('admin_desa.lembaga.index', compact('lembagas'));
    }

    public function create(string $subdomain)
    {
        return view('admin_desa.lembaga.create');
    }

    public function store(Request $request, string $subdomain)
    {
        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sk_kepala_desa' => 'nullable|file|mimes:pdf|max:2048', // Validasi PDF max 2MB
            'pengurus.*.nama_pengurus' => 'required|string|max:255',
            'pengurus.*.jabatan' => 'required|string|max:255',
            'path_kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $skPath = null;
        if ($request->hasFile('sk_kepala_desa')) {
            $skPath = $request->file('sk_kepala_desa')->store('sk_lembaga', 'public');
        }

        $kopSuratPath = null;
        if ($request->hasFile('path_kop_surat')) {
            // Simpan file logo/kop surat
            $kopSuratPath = $request->file('path_kop_surat')->store('kop_surat', 'public');
        }

        // Simpan Lembaga
        $lembaga = Lembaga::create([
            'nama_lembaga'        => $request->nama_lembaga,
            'deskripsi'           => $request->deskripsi,
            'sk_kepala_desa_path' => $skPath,
            'path_kop_surat'      => $kopSuratPath,
        ]);

        // Simpan data pengurus
        if ($request->has('pengurus')) {
            foreach ($request->pengurus as $pengurusData) {
                $lembaga->pengurus()->create($pengurusData);
            }
        }

        return redirect()->route('lembaga.index')->with('success', 'Lembaga desa berhasil ditambahkan!');
    }


    public function edit(string $subdomain, Lembaga $lembaga)
    {
        // Global scope akan memastikan hanya lembaga dari desa yang sama yang bisa diakses
        return view('admin_desa.lembaga.edit', compact('lembaga'));
    }

    public function update( Request $request, string $subdomain, Lembaga $lembaga)
    {
        $validated = $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sk_kepala_desa' => 'nullable|file|mimes:pdf|max:2048',
            'pengurus.*.nama_pengurus' => 'required|string|max:255',
            'pengurus.*.jabatan' => 'required|string|max:255',
            'path_kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // <-- Validasi baru

        ]);

        // Update SK Kepala Desa jika ada file baru
        if ($request->hasFile('sk_kepala_desa')) {
            // Hapus SK lama jika ada
            if ($lembaga->sk_kepala_desa_path) {
                Storage::delete(str_replace('/storage/', 'public/', $lembaga->sk_kepala_desa_path));
            }
            $skPath = $request->file('sk_kepala_desa')->store('sk_lembaga');
            $validated ['sk_kepala_desa_path'] = Storage::url($skPath);
        }

        if ($request->hasFile('path_kop_surat')) {
            // Hapus file lama jika ada
            if ($lembaga->path_kop_surat) {
                Storage::disk('public')->delete($lembaga->path_kop_surat);
            }
            $validated['path_kop_surat'] = $request->file('path_kop_surat')->store('kop_surat', 'public');
        }

        $lembaga->update($validated);

        // Update pengurus: hapus yang lama, tambahkan yang baru (bisa lebih canggih nanti)
        $lembaga->pengurus()->delete(); // Hapus semua pengurus lama
        if ($request->has('pengurus')) {
            foreach ($request->pengurus as $pengurusData) {
                $lembaga->pengurus()->create($pengurusData);
            }
        }

        return redirect()->route('lembaga.index')->with('success', 'Data lembaga desa berhasil diperbarui!');
    }

    public function destroy(string $subdomain, Lembaga $lembaga)
    {
        // Hapus file SK jika ada
        if ($lembaga->sk_kepala_desa_path) {
            Storage::delete(str_replace('/storage/', 'public/', $lembaga->sk_kepala_desa_path));
        }
        if ($lembaga->path_kop_surat) {
            Storage::delete(str_replace('/storage/', 'public/', $lembaga->path_kop_surat));
        }
        $lembaga->delete(); // Ini akan otomatis menghapus pengurus karena onDelete('cascade')
        return redirect()->route('lembaga.index')->with('success', 'Lembaga desa berhasil dihapus!');
    }

    public function daftarKegiatan(string $subdomain, Lembaga $lembaga)
    {
        $kegiatans = $lembaga->kegiatans()->with('photos')->latest()->paginate(10);
        return view('admin_desa.kegiatan.index', compact('lembaga', 'kegiatans'));
    }
}