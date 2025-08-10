<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\PemeriksaanAnak;
use App\Models\DataKesehatanAnak; // <-- Tambahkan ini
use Illuminate\Http\Request;
use Carbon\Carbon;

class SesiPosyanduController extends Controller
{
    /**
     * Menampilkan halaman daftar hadir untuk sesi posyandu.
     */
    public function create(string $subdomain, Posyandu $posyandu)
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // --- PERBAIKAN UTAMA DI SINI ---
        // 1. Ambil ID dari DATA KESEHATAN ANAK yang sudah punya record pemeriksaan bulan ini.
        $idDataKesehatanAnakSudahDiperiksa = PemeriksaanAnak::where('posyandu_id', $posyandu->id)
            ->whereMonth('tanggal_pemeriksaan', $bulanIni)
            ->whereYear('tanggal_pemeriksaan', $tahunIni)
            ->pluck('data_kesehatan_anak_id'); // <-- Kunci perbaikannya

        // 2. Ambil semua anak yang terpantau di posyandu ini.
        $semuaAnakTerpantau = $posyandu->dataKesehatanAnak()->with('warga')->get();

        // 3. Pisahkan mana yang sudah dan belum hadir berdasarkan ID data kesehatan anak.
        $anakBelumDiperiksa = $semuaAnakTerpantau->whereNotIn('id', $idDataKesehatanAnakSudahDiperiksa);
        $anakSudahDiperiksa = $semuaAnakTerpantau->whereIn('id', $idDataKesehatanAnakSudahDiperiksa);

        return view('admin_desa.kesehatan_anak.sesi_posyandu', [
            'posyandu' => $posyandu,
            'anakBelumDiperiksa' => $anakBelumDiperiksa,
            'anakSudahDiperiksa' => $anakSudahDiperiksa,
        ]);
    }

    /**
     * Menyimpan data kehadiran anak dan membuat record pemeriksaan baru.
     */
    public function store(Request $request, string $subdomain)
    {
        $validated = $request->validate([
            'data_kesehatan_anak_id' => 'required|exists:data_kesehatan_anaks,id',
            'posyandu_id' => 'required|exists:posyandu,id',
        ]);

        // Ambil data anak untuk mendapatkan warga_id
        $dataAnak = DataKesehatanAnak::find($validated['data_kesehatan_anak_id']);

        // Buat record pemeriksaan baru dengan data minimal
        PemeriksaanAnak::firstOrCreate(
            [
                'data_kesehatan_anak_id' => $validated['data_kesehatan_anak_id'],
                // Cek berdasarkan bulan dan tahun agar tidak duplikat jika tombol diklik berkali-kali
                'bulan_pemeriksaan' => now()->month,
                'tahun_pemeriksaan' => now()->year,
            ],
            [
                'posyandu_id' => $validated['posyandu_id'],
                'warga_id' => $dataAnak->warga_id, // <-- Ambil dari data induknya
                'tanggal_pemeriksaan' => now(),
            ]
        );

        return back()->with('status', 'Anak berhasil ditandai hadir.');
    }
}