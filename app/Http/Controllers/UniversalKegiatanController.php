<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Models\Desa;
use App\Models\Kegiatan;
use App\Models\Lembaga;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class UniversalKegiatanController extends Controller
{
    /**
     * Menampilkan daftar semua kegiatan (proposal & LPJ) secara universal.
     */
    public function index(Request $request, string $subdomain)
    {
        $kegiatansQuery = Kegiatan::with('kegiatanable')->latest();

        $penyelenggaraSpesifik = null;

        if ($request->has('lembaga_id')) {
            $lembaga = Lembaga::findOrFail($request->lembaga_id);
            $kegiatansQuery->where('kegiatanable_type', Lembaga::class)
                        ->where('kegiatanable_id', $lembaga->id);
            $penyelenggaraSpesifik = $lembaga->nama_lembaga;
        } elseif ($request->has('kelompok_id')) {
            $kelompok = Kelompok::findOrFail($request->kelompok_id);
            $kegiatansQuery->where('kegiatanable_type', Kelompok::class)
                        ->where('kegiatanable_id', $kelompok->id);
            $penyelenggaraSpesifik = $kelompok->nama_kelompok;
        }

        $kegiatans = $kegiatansQuery->paginate(20)->withQueryString();

        return view('admin_desa.kegiatan.index', compact('kegiatans', 'penyelenggaraSpesifik'));
    }

    /**
     * Menampilkan form untuk membuat proposal kegiatan baru.
     */
    public function create(string $subdomain)
    {
        // Ambil daftar Lembaga dan Kelompok untuk dikirim ke form
        $lembagas = Lembaga::all();
        $kelompoks = Kelompok::all();
        return view('admin_desa.kegiatan.create', compact('lembagas', 'kelompoks'));
    }

    /**
     * Menyimpan proposal kegiatan baru ke database.
     */
    // Di dalam UniversalKegiatanController.php

    public function store(StoreProposalRequest $request, string $subdomain)
    {
        $validated = $request->validated();

        $penyelenggaraType = $validated['penyelenggara_type'];
        $penyelenggaraId = $validated['penyelenggara_id'];

        $modelPenyelenggara = $penyelenggaraType === 'lembaga' 
            ? Lembaga::findOrFail($penyelenggaraId) 
            : Kelompok::findOrFail($penyelenggaraId);

        $kegiatan = $modelPenyelenggara->kegiatans()->create([
            'desa_id' => auth()->user()->desa_id,
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'lokasi_kegiatan' => $validated['lokasi_kegiatan'],
            'deskripsi_kegiatan' => $validated['deskripsi_kegiatan'],
            'latar_belakang' => $validated['latar_belakang'] ?? null,
            'tujuan_kegiatan' => $validated['tujuan_kegiatan'] ?? null,
            'laporan_dana' => $validated['laporan_dana'] ?? null, // Menggunakan nama yang benar
            'anggaran_biaya' => $validated['anggaran_biaya'] ?? null,
            'sumber_dana' => $validated['sumber_dana'] ?? null,
            'penutup' => $validated['penutup'] ?? null,
            'status' => 'Proposal Diajukan',
        ]);

        return redirect()->route('kegiatans.index')->with('success', 'Proposal kegiatan berhasil dibuat.');
    }

    /**
     * Menampilkan halaman detail untuk sebuah kegiatan (proposal).
     */
    public function show(string $subdomain, Kegiatan $kegiatan)
    {
        $kegiatan->load('kegiatanable', 'pengeluarans'); 
        return view('admin_desa.kegiatan.show', compact('kegiatan'));
    }

    /**
     * Menampilkan form untuk mengedit proposal kegiatan.
     */
    public function edit(string $subdomain, Kegiatan $kegiatan)
    {
        $kegiatan->load('kegiatanable');

        $penyelenggaraType = '';
        if ($kegiatan->kegiatanable_type === Lembaga::class) {
            $penyelenggaraType = 'lembaga';
        } elseif ($kegiatan->kegiatanable_type === Kelompok::class) {
            $penyelenggaraType = 'kelompok';
        }

        $lembagas = Lembaga::all();
        $kelompoks = Kelompok::all();

        return view('admin_desa.kegiatan.edit', compact('kegiatan', 'lembagas', 'kelompoks', 'penyelenggaraType'));
    }

    /**
     * Mengupdate proposal kegiatan di database.
     */
    public function update(StoreProposalRequest $request, string $subdomain, Kegiatan $kegiatan)
    {
        $validated = $request->validated();
        
        $kegiatan->update($validated);

        return redirect()->route('kegiatans.index')->with('success', 'Proposal kegiatan berhasil diperbarui.');
    }

    /**
     * Menghapus proposal kegiatan.
     */
    public function destroy(string $subdomain, Kegiatan $kegiatan)
    {
        $kegiatan->delete();
        return redirect()->route('kegiatans.index')->with('success', 'Proposal kegiatan berhasil dihapus.');
    }

    public function cetakProposal(string $subdomain, Kegiatan $kegiatan)
    {

        $desa = app('tenant');
        $kegiatan->load('kegiatanable');
        $penyelenggara = $kegiatan->kegiatanable;
        $kopSuratBase64 = null;
        if (!empty($penyelenggara->path_kop_surat)) {
            $imagePath = storage_path('app/public/' . $penyelenggara->path_kop_surat);
            if (file_exists($imagePath)) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);
                $kopSuratBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $data = [
            'desa'=> $desa,
            'kegiatan' => $kegiatan,
            'penyelenggara' => $kegiatan->kegiatanable,
            'tanggalCetak' => now(),
            'kopSuratBase64' => $kopSuratBase64
        ];
        $data['ketua'] = $penyelenggara->pengurus()->where('jabatan', 'Ketua')->first();
        $pdf = Pdf::loadView('admin_desa.kegiatan.proposal_pdf', $data);

        $namaFile = 'Proposal-' . \Illuminate\Support\Str::slug($kegiatan->nama_kegiatan) . '.pdf';
        return $pdf->stream($namaFile);
    }
}