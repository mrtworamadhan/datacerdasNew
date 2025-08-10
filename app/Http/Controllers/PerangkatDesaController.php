<?php

namespace App\Http\Controllers;

use App\Models\PerangkatDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk mengakses user yang login
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PerangkatDesaController extends Controller
{
    public function index(string $subdomain)
    {
        // Global scope 'desa_id' akan otomatis memfilter berdasarkan desa_id user yang login
        $perangkatDesas = PerangkatDesa::all();
        return view('admin_desa.perangkat_desa.index', compact('perangkatDesas'));
    }

    public function create(string $subdomain)
    {
        // Tidak perlu lagi mengirim $users ke view karena user_id akan otomatis
        return view('admin_desa.perangkat_desa.create');
    }

    public function show(string $subdomain)
    {
        // Tidak perlu lagi mengirim $users ke view karena user_id akan otomatis
    }

    public function store(Request $request, string $subdomain)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'foto_path' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        // dd($request->all()); // Debugging line to check request data --- IGNORE ---

        $fileName = null;

        if ($request->hasFile('foto_path')) {
            $image = $request->file('foto_path');
            $fileName = time() . '_' . $image->getClientOriginalName();

            // Resize menggunakan Intervention Image v3
            $manager = new ImageManager(new Driver());
            $resizedImage = $manager->read($image)->scale(width: 800);

            Storage::disk('public')->put('foto_perangkat/' . $fileName, (string) $resizedImage->toJpeg(80));
        }

        PerangkatDesa::create([
            'desa_id' => auth()->user()->desa_id,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'foto_path' => $fileName ? 'foto_perangkat/' . $fileName : null,
        ]);

        return redirect()->route('perangkat-desa.index')->with('success', 'Perangkat desa berhasil ditambahkan!');
    }

    public function edit(string $subdomain, PerangkatDesa $perangkatDesa)
    {
        // Tidak perlu lagi mengirim $users ke view
        return view('admin_desa.perangkat_desa.edit', compact('perangkatDesa'));
    }

    public function update(Request $request, string $subdomain, PerangkatDesa $perangkatDesa)
    {
        $validated = $request->validate([
            'nama' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'foto_path' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);
    
        if ($request->hasFile('foto_path')) {
            // Hapus foto lama jika ada
            if ($perangkatDesa->foto_path) {
                Storage::disk('public')->delete($perangkatDesa->foto_path);
            }

            $image = $request->file('foto_path');
            $fileName = time() . '_' . $image->getClientOriginalName();

            $manager = new ImageManager(new Driver());
            $resizedImage = $manager->read($image)->scale(width: 800);

            Storage::disk('public')->put('foto_perangkat/' . $fileName, (string) $resizedImage->toJpeg(80));
            $validated['foto_path'] = 'foto_perangkat/' . $fileName;
        }

        $perangkatDesa->update($validated);

        return redirect()->route('perangkat-desa.index')->with('success', 'Data perangkat desa berhasil diperbarui!');
    }



    public function destroy(string $subdomain, PerangkatDesa $perangkatDesa)
    {
        $perangkatDesa->delete();
        return redirect()->route('perangkat-desa.index')->with('success', 'Perangkat desa berhasil dihapus!');
    }
}
