<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\PengurusLembaga; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk upload file

class LembagaController extends Controller
{
    public function index()
    {
        // Global scope 'desa_id' akan otomatis memfilter
        $lembagas = Lembaga::with('pengurus')->get(); // Load relasi pengurus
        return view('admin_desa.lembaga.index', compact('lembagas'));
    }

    public function create()
    {
        return view('admin_desa.lembaga.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sk_kepala_desa' => 'nullable|file|mimes:pdf|max:2048', // Validasi PDF max 2MB
            'pengurus.*.nama_pengurus' => 'required|string|max:255', // Validasi array pengurus
            'pengurus.*.jabatan' => 'required|string|max:255',
        ]);

        $skPath = null;
        if ($request->hasFile('sk_kepala_desa')) {
            $skPath = $request->file('sk_kepala_desa')->store('sk_lembaga');
            $skPath = Storage::url($skPath); // Dapatkan URL yang bisa diakses publik
        }

        // desa_id akan otomatis terisi oleh Global Scope Trait BelongsToDesa
        $lembaga = Lembaga::create([
            'nama_lembaga' => $request->nama_lembaga,
            'deskripsi' => $request->deskripsi,
            'sk_kepala_desa_path' => $skPath,
        ]);

        // Simpan data pengurus
        if ($request->has('pengurus')) {
            foreach ($request->pengurus as $pengurusData) {
                $lembaga->pengurus()->create($pengurusData);
            }
        }

        return redirect()->route('lembaga.index')->with('success', 'Lembaga desa berhasil ditambahkan!');
    }

    public function edit(Lembaga $lembaga)
    {
        // Global scope akan memastikan hanya lembaga dari desa yang sama yang bisa diakses
        return view('admin_desa.lembaga.edit', compact('lembaga'));
    }

    public function update(Request $request, Lembaga $lembaga)
    {
        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sk_kepala_desa' => 'nullable|file|mimes:pdf|max:2048',
            'pengurus.*.nama_pengurus' => 'required|string|max:255',
            'pengurus.*.jabatan' => 'required|string|max:255',
        ]);

        // Update SK Kepala Desa jika ada file baru
        if ($request->hasFile('sk_kepala_desa')) {
            // Hapus SK lama jika ada
            if ($lembaga->sk_kepala_desa_path) {
                Storage::delete(str_replace('/storage/', 'public/', $lembaga->sk_kepala_desa_path));
            }
            $skPath = $request->file('sk_kepala_desa')->store('sk_lembaga');
            $lembaga->sk_kepala_desa_path = Storage::url($skPath);
        }

        $lembaga->update([
            'nama_lembaga' => $request->nama_lembaga,
            'deskripsi' => $request->deskripsi,
        ]);

        // Update pengurus: hapus yang lama, tambahkan yang baru (bisa lebih canggih nanti)
        $lembaga->pengurus()->delete(); // Hapus semua pengurus lama
        if ($request->has('pengurus')) {
            foreach ($request->pengurus as $pengurusData) {
                $lembaga->pengurus()->create($pengurusData);
            }
        }

        return redirect()->route('lembaga.index')->with('success', 'Data lembaga desa berhasil diperbarui!');
    }

    public function destroy(Lembaga $lembaga)
    {
        // Hapus file SK jika ada
        if ($lembaga->sk_kepala_desa_path) {
            Storage::delete(str_replace('/storage/', 'public/', $lembaga->sk_kepala_desa_path));
        }
        $lembaga->delete(); // Ini akan otomatis menghapus pengurus karena onDelete('cascade')
        return redirect()->route('lembaga.index')->with('success', 'Lembaga desa berhasil dihapus!');
    }
}