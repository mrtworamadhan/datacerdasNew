<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\Kegiatan;
use App\Models\KegiatanPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 

class KegiatanController extends Controller
{
    public function index(Lembaga $lembaga)
    {
        $kegiatans = $lembaga->kegiatans()->with('photos')->latest()->paginate(10);
        return view('admin_desa.kegiatan.index', compact('lembaga', 'kegiatans'));
    }

    public function create(Lembaga $lembaga)
    {
        return view('admin_desa.kegiatan.create', compact('lembaga'));
    }

    public function store(Request $request, Lembaga $lembaga)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'lokasi_kegiatan' => 'required|string|max:255',
            'deskripsi_kegiatan' => 'required|string',
            'latar_belakang' => 'nullable|string',
            'tujuan_kegiatan' => 'nullable|string',
            'anggaran_biaya' => 'nullable|numeric',
            'laporan_dana' => 'nullable|string',
            'sumber_dana' => 'nullable|string',
            'penutup' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $kegiatan = $lembaga->kegiatans()->create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // PERUBAHAN: Logika resize dihapus, langsung simpan file
                $path = $photo->store('kegiatan-photos', 'public');
                $kegiatan->photos()->create(['path' => $path]);
            }
        }

        return redirect()->route('lembaga.kegiatan.index', $lembaga)->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Lembaga $lembaga, Kegiatan $kegiatan)
    {
        return view('admin_desa.kegiatan.show', compact('lembaga', 'kegiatan'));
    }

    public function edit(Lembaga $lembaga, Kegiatan $kegiatan)
    {
        return view('admin_desa.kegiatan.edit', compact('lembaga', 'kegiatan'));
    }

    public function update(Request $request, Lembaga $lembaga, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'lokasi_kegiatan' => 'required|string|max:255',
            'deskripsi_kegiatan' => 'required|string',
            'latar_belakang' => 'nullable|string',
            'tujuan_kegiatan' => 'nullable|string',
            'anggaran_biaya' => 'nullable|numeric',
            'laporan_dana' => 'nullable|string',
            'sumber_dana' => 'nullable|string',
            'penutup' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $kegiatan->update($validated);

       if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('kegiatan-photos', 'public');
                $kegiatan->photos()->create(['path' => $path]);
            }
        }

        return redirect()->route('lembaga.kegiatan.index', $lembaga)->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Lembaga $lembaga, Kegiatan $kegiatan)
    {
        foreach ($kegiatan->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $kegiatan->delete();
        return redirect()->route('lembaga.kegiatan.index', $lembaga)->with('success', 'Kegiatan berhasil dihapus.');
    }
    
    public function destroyPhoto(KegiatanPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return back()->with('success', 'Foto kegiatan berhasil dihapus.');
    }

    public function cetakLaporan(Lembaga $lembaga, Kegiatan $kegiatan)
    {
        // Load view khusus untuk PDF dan passing datanya
        $pdf = Pdf::loadView('admin_desa.kegiatan.cetak_laporan', compact('lembaga', 'kegiatan'));

        // Buat nama file yang dinamis
        $fileName = 'lpj-' . \Illuminate\Support\Str::slug($kegiatan->nama_kegiatan) . '.pdf';

        // Tampilkan PDF di browser
        return $pdf->stream($fileName);
    }
}