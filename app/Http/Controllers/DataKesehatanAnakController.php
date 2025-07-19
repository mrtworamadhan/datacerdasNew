<?php

namespace App\Http\Controllers;

use App\Models\DataKesehatanAnak;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DataKesehatanAnakController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar untuk semua warga balita (di bawah 60 bulan)
        $baseWargaQuery = Warga::whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) < 60');

        // PERUBAHAN: Terapkan filter usia baru
        if ($request->filled('usia')) {
            switch ($request->usia) {
                case '0-12':
                    $baseWargaQuery->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) <= 12');
                    break;
                case '13-36':
                    $baseWargaQuery->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) BETWEEN 13 AND 36');
                    break;
                case '37-60':
                    $baseWargaQuery->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) BETWEEN 37 AND 60');
                    break;
            }
        }

        // Ambil ID warga yang sudah ada di data kesehatan anak
        $existingWargaIds = DataKesehatanAnak::pluck('warga_id');

        // Tabel 1: Anak yang belum dipantau
        $anakBaru = (clone $baseWargaQuery)
            ->whereNotIn('id', $existingWargaIds)
            ->latest('tanggal_lahir')
            ->get();

        // Tabel 2: Anak yang sudah dipantau
        $anakTerpantauQuery = DataKesehatanAnak::with(['warga.kartuKeluarga'])
            ->whereHas('warga', function ($q) use ($request) {
                $q->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) < 60');
                if ($request->filled('usia')) {
                    switch ($request->usia) {
                        case '0-12': $q->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) <= 12'); break;
                        case '13-36': $q->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) BETWEEN 13 AND 36'); break;
                        case '37-60': $q->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) BETWEEN 37 AND 60'); break;
                    }
                }
            });

        // PERUBAHAN: Terapkan filter pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $anakTerpantauQuery->where(function ($query) use ($searchTerm) {
                $query->where('nama_ibu', 'like', "%{$searchTerm}%")
                      ->orWhere('nama_ayah', 'like', "%{$searchTerm}%")
                      ->orWhereHas('warga', function ($q) use ($searchTerm) {
                          $q->where('nama_lengkap', 'like', "%{$searchTerm}%")
                            ->orWhere('nik', 'like', "%{$searchTerm}%");
                      });
            });
        }

        $anakTerpantau = $anakTerpantauQuery->latest()->paginate(15)->withQueryString();

        $stats = [];
        $stats['total_balita'] = DataKesehatanAnak::count();
        $stats['usia_0_12'] = DataKesehatanAnak::whereHas('warga', function ($q) { $q->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) <= 12'); })->count();
        $stats['usia_13_36'] = DataKesehatanAnak::whereHas('warga', function ($q) { $q->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) BETWEEN 13 AND 36'); })->count();
        $stats['usia_37_60'] = DataKesehatanAnak::whereHas('warga', function ($q) { $q->whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) BETWEEN 37 AND 60'); })->count();

        // Statistik Gizi dari pemeriksaan terakhir setiap anak
        $latestPemeriksaanIds = DB::table('pemeriksaan_anaks')->select(DB::raw('MAX(id) as id'))->groupBy('data_kesehatan_anak_id');
        $statusGiziCounts = DB::table('pemeriksaan_anaks')->whereIn('id', $latestPemeriksaanIds)->select('status_gizi', DB::raw('count(*) as total'))->groupBy('status_gizi')->pluck('total', 'status_gizi');

        $stats['gizi_baik'] = $statusGiziCounts->get('Naik') ?? 0;
        $stats['gizi_cukup'] = $statusGiziCounts->get('Tetap') ?? 0;
        $stats['gizi_kurang'] = $statusGiziCounts->get('Turun') ?? 0;
        $stats['bgm'] = $statusGiziCounts->get('BGM') ?? 0;

        return view('admin_desa.kesehatan_anak.index', compact('anakBaru', 'anakTerpantau', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warga_ids' => 'required|array',
            'warga_ids.*' => 'exists:wargas,id',
        ]);

        foreach ($validated['warga_ids'] as $wargaId) {
            $warga = Warga::find($wargaId);

            // Gunakan firstOrCreate untuk mencegah duplikasi jika ada pengiriman ganda
            DataKesehatanAnak::firstOrCreate(
                ['warga_id' => $wargaId],
                [
                    'tanggal_lahir' => $warga->tanggal_lahir,
                    'nama_ibu' => $warga->nama_ibu_kandung,
                    'nama_ayah' => $warga->nama_ayah_kandung,
                ]
            );
        }

        return redirect()->back()->with('success', count($validated['warga_ids']) . ' anak berhasil ditambahkan ke daftar pemantauan.');
    }

    public function show(DataKesehatanAnak $kesehatanAnak)
    {
        // Halaman detail untuk melihat riwayat pemeriksaan (akan kita buat nanti)
        $kesehatanAnak->load('warga', 'riwayatPemeriksaan');
        return view('admin_desa.kesehatan_anak.show', compact('kesehatanAnak'));
    }

    public function edit(DataKesehatanAnak $kesehatanAnak)
    {
        $kesehatanAnak->load('warga');
        return view('admin_desa.kesehatan_anak.edit', compact('kesehatanAnak'));
    }

    public function update(Request $request, DataKesehatanAnak $kesehatanAnak)
    {
        $validated = $request->validate([
            'bb_lahir' => 'nullable|numeric',
            'tb_lahir' => 'nullable|numeric',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'required|string|max:255',
        ]);

        $kesehatanAnak->update($validated);

        return redirect()->route('kesehatan-anak.index')->with('success', 'Data kesehatan anak berhasil diperbarui.');
    }

    public function destroy(DataKesehatanAnak $kesehatanAnak)
    {
        $kesehatanAnak->delete();
        return redirect()->route('kesehatan-anak.index')->with('success', 'Data kesehatan anak berhasil dihapus.');
    }
}
