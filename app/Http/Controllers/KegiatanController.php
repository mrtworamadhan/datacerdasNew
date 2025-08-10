<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\Kelompok;
use App\Models\Kegiatan;
use App\Models\KegiatanPhoto;
use App\Http\Requests\StoreProposalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class KegiatanController extends Controller
{
    public function index(string $subdomain)
    {
        $kegiatans = Kegiatan::with('kegiatanable')->latest()->paginate(20);

        return view('admin_desa.kegiatan.index', compact('kegiatans'));
    }

    public function create(string $subdomain)
    {
        $lembagas = Lembaga::all();
        $kelompoks = Kelompok::all();
        return view('admin_desa.kegiatan.create', compact('lembagas', 'kelompoks'));
    }

    public function store(string $subdomain,StoreProposalRequest $request)
    {
        $validated = $request->validated();

        $penyelenggaraType = $validated['penyelenggara_type'];
        $penyelenggaraId = $validated['penyelenggara_id'];

        $modelPenyelenggara = $penyelenggaraType === 'lembaga' 
            ? Lembaga::findOrFail($penyelenggaraId) 
            : Kelompok::findOrFail($penyelenggaraId);

        // Laravel akan otomatis mengisi 'kegiatanable_id' dan 'kegiatanable_type' di sini
        $kegiatan = $modelPenyelenggara->kegiatans()->create([
            'desa_id' => auth()->user()->desa_id,
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tipe_kegiatan' => $validated['tipe_kegiatan'],
            'lokasi_kegiatan' => $validated['lokasi_kegiatan'],
            'deskripsi_kegiatan' => $validated['deskripsi_kegiatan'],
            'latar_belakang' => $validated['latar_belakang'] ?? null,
            'tujuan_kegiatan' => $validated['tujuan_kegiatan'] ?? null,
            'laporan_dana' => $validated['laporan_dana'] ?? null, // Ganti dari 'rab'
            'anggaran_biaya' => $validated['anggaran_biaya'] ?? null,
            'sumber_dana' => $validated['sumber_dana'] ?? null,
            'penutup' => $validated['penutup'] ?? null,
            'status' => 'Proposal Diajukan',
        ]);

        return redirect()->route('kegiatans.index')->with('success', 'Proposal kegiatan berhasil dibuat.');
    }

    public function show(string $subdomain, Lembaga $lembaga, Kegiatan $kegiatan)
    {
        return view('admin_desa.kegiatan.show', compact('lembaga', 'kegiatan'));
    }

    public function edit(string $subdomain, Lembaga $lembaga, Kegiatan $kegiatan)
    {
        return view('admin_desa.kegiatan.edit', compact('lembaga', 'kegiatan'));
    }

    public function update(Request $request, string $subdomain, Lembaga $lembaga, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|string|max:255',
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

    public function destroy(string $subdomain, Lembaga $lembaga, Kegiatan $kegiatan)
    {
        foreach ($kegiatan->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $kegiatan->delete();
        return redirect()->route('lembaga.kegiatan.index', $lembaga)->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function destroyPhoto(string $subdomain, KegiatanPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return back()->with('success', 'Foto kegiatan berhasil dihapus.');
    }

    public function cetakLaporan(string $subdomain, Lembaga $lembaga, Kegiatan $kegiatan)
    {
        // Load view khusus untuk PDF dan passing datanya
        $pdf = Pdf::loadView('admin_desa.kegiatan.cetak_laporan', compact('lembaga', 'kegiatan'));

        // Buat nama file yang dinamis
        $fileName = 'lpj-' . \Illuminate\Support\Str::slug($kegiatan->nama_kegiatan) . '.pdf';

        // Tampilkan PDF di browser
        return $pdf->stream($fileName);
    }
}