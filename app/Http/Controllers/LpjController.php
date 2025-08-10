<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\Kegiatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Terbilang;

class LpjController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $subdomain)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $subdomain, Kegiatan $kegiatan)
    {
        // Cek apakah kegiatan ini sudah punya LPJ, jika sudah, arahkan ke halaman edit.
        if ($kegiatan->lpj) {
            return redirect()->route('lpjs.edit', $kegiatan->lpj->id);
        }

        $penyelenggara = $kegiatan->kegiatanable;
        $lokasiKegiatan = $kegiatan->lokasi_kegiatan;
        // 2. Mengambil nama penyelenggara (dengan pengecekan)
        $namaPenyelenggara = '';
        if ($penyelenggara instanceof \App\Models\Lembaga) {
            $namaPenyelenggara = $penyelenggara->nama_lembaga;
        } elseif ($penyelenggara instanceof \App\Models\Kelompok) {
            $namaPenyelenggara = $penyelenggara->nama_kelompok;
        }

        // 3. Mengambil objek desa dari penyelenggara
        // Ini bisa dilakukan karena baik Lembaga maupun Kelompok punya relasi ke Desa
        $desa = $penyelenggara->desa;

        // 4. Mengambil nama desa
        $namaDesa = $desa->nama_desa;

        // Kirim data kegiatan ke view form
        return view('admin_desa.lpj.create', compact('kegiatan', 'lokasiKegiatan','namaPenyelenggara', 'namaDesa'));
    }

    /**
     * Menyimpan LPJ baru ke database.
     */
    public function store(Request $request, string $subdomain, Kegiatan $kegiatan)
    {
        // dd($request->all());
        $validated = $request->validate([
            'hasil_kegiatan' => 'required|string',
            'evaluasi_kendala' => 'nullable|string',
            'rekomendasi_lanjutan' => 'nullable|string',
            'tanggal_pelaporan' => 'required|date',
            
            // Tambahkan validasi untuk field narasi baru
            'latar_belakang_lpj' => 'nullable|string',
            'tujuan_lpj' => 'nullable|string',
            'deskripsi_lpj' => 'nullable|string',
            'penutup_lpj' => 'nullable|string',
        ]);

        // Hitung total realisasi anggaran dari data pengeluaran yang ada
        $totalRealisasi = $kegiatan->pengeluarans()->sum('jumlah');

        // Buat record LPJ baru
        $kegiatan->lpj()->create([
            'hasil_kegiatan' => $validated['hasil_kegiatan'],
            'evaluasi_kendala' => $validated['evaluasi_kendala'],
            'rekomendasi_lanjutan' => $validated['rekomendasi_lanjutan'],
            'tanggal_pelaporan' => $validated['tanggal_pelaporan'],
            'realisasi_anggaran' => $totalRealisasi,
            
            // Simpan data narasi baru
            'latar_belakang_lpj' => $validated['latar_belakang_lpj'],
            'tujuan_lpj' => $validated['tujuan_lpj'] ?? '',
            'deskripsi_lpj' => $validated['deskripsi_lpj']?? '',
            'penutup_lpj' => $validated['penutup_lpj']?? '',
        ]);

        // Update status kegiatan induknya menjadi "Selesai" atau "LPJ Dibuat"
        $kegiatan->update(['status' => 'LPJ Dibuat']);

        // Arahkan ke halaman detail kegiatan agar bisa langsung dicetak
        return redirect()->route('kegiatans.show', $kegiatan->id)
                         ->with('success', 'Laporan Pertanggungjawaban (LPJ) berhasil disiapkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $subdomain, Lpj $lpj)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $subdomain, Lpj $lpj)
    {
        // Kita gunakan $lpj->kegiatan untuk mengirim data kegiatan induknya juga
        return view('admin_desa.lpj.edit', [
            'lpj' => $lpj,
            'kegiatan' => $lpj->kegiatan 
        ]);
    }

    /**
     * Mengupdate data LPJ di database.
     */
    public function update(Request $request, string $subdomain, Lpj $lpj)
    {
        $validated = $request->validate([
            'hasil_kegiatan' => 'required|string',
            'evaluasi_kendala' => 'nullable|string',
            'rekomendasi_lanjutan' => 'nullable|string',
            'tanggal_pelaporan' => 'required|date',
        ]);

        // Hitung ulang realisasi anggaran untuk memastikan data tetap sinkron
        $totalRealisasi = $lpj->kegiatan->pengeluarans()->sum('jumlah');
        $validated['realisasi_anggaran'] = $totalRealisasi;

        // Update record LPJ
        $lpj->update($validated);

        // Arahkan kembali ke halaman detail kegiatan
        return redirect()->route('kegiatans.show', $lpj->kegiatan_id)
                        ->with('success', 'Laporan Pertanggungjawaban (LPJ) berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $subdomain, Lpj $lpj)
    {
        //
    }

    public function generateLpj(string $subdomain, Kegiatan $kegiatan)
    {
        // 1. Otorisasi (bisa disempurnakan dengan Spatie nanti)
        // $this->authorize('view', $kegiatan);

        $desa = app('tenant');
        // 2. Eager load semua relasi yang kita butuhkan untuk laporan
        $kegiatan->load([
            'kegiatanable', 
            'pengeluarans' => fn($q) => $q->with('detailBarangs')
        ]);
        $penyelenggara = $kegiatan->kegiatanable;

        $lpj = $kegiatan->lpj;
        if (!$lpj) {
            // Jika LPJ belum dibuat, beri pesan error atau arahkan ke halaman create
            return redirect()->route('lpjs.create', $kegiatan->id)->with('error', 'LPJ belum dibuat untuk kegiatan ini.');
        }

        // 3. Lakukan perhitungan total realisasi
        $totalRealisasi = $kegiatan->pengeluarans->sum('jumlah');
        $sisaAnggaran = $kegiatan->anggaran_biaya - $totalRealisasi;

        // 4. Siapkan semua data yang akan dikirim ke "cetakan" PDF
        $data = [
            'desa' => $desa,
            'kegiatan' => $kegiatan,
            'lpj' => $lpj,
            'penyelenggara' => $kegiatan->kegiatanable,
            'pengeluarans' => $kegiatan->pengeluarans,
            'totalRealisasi' => $totalRealisasi,
            'sisaAnggaran' => $sisaAnggaran,
            'terbilangRealisasi' => Terbilang::make($totalRealisasi),
            'tanggalCetak' => now(),
        ];
        $data['ketua'] = $penyelenggara->pengurus()->where('jabatan', 'Ketua')->first();

        // 5. Tentukan cetakan mana yang akan digunakan berdasarkan tipe kegiatan
        $viewPath = $kegiatan->tipe_kegiatan === 'Pembangunan'
            ? 'admin_desa.laporan.keuangan.lpj_pembangunan_pdf'
            : 'admin_desa.laporan.keuangan.lpj_acara_pdf'; // Template default/sederhana

        $pdf = Pdf::loadView($viewPath, $data);
        $pdf->setPaper('a4', 'portrait'); // Atur kertas menjadi potrait

        $namaFile = 'LPJ - ' . \Illuminate\Support\Str::slug($kegiatan->nama_kegiatan) . '.pdf';
        return $pdf->stream($namaFile);
    }
}
