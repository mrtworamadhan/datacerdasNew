<?php

namespace App\Http\Controllers;

use App\Models\DataKesehatanAnak;
use App\Models\Warga;
use App\Models\Posyandu;
use App\Models\PemeriksaanAnak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class DataKesehatanAnakController extends Controller
{


    public function index(Request $request, string $subdomain)
    {
        $user = Auth::user();
        $desaId = auth()->user()->desa_id; // atau ambil dari subdomain

        $selectedPosyandu = null;

        // --- 1. MEMBUAT QUERY DASAR YANG "SADAR" HAK AKSES ---
        $baseAnakTerpantauQuery = DataKesehatanAnak::query()->whereHas('warga'); // whereHas untuk memicu Trait/Scope

        if ($user->user_type === 'kader_posyandu' && $user->posyandu_id) {
            // Jika KADER, paksa query hanya untuk posyandunya
            $selectedPosyandu = Posyandu::with('rws')->find($user->posyandu_id);
            $baseAnakTerpantauQuery->where('posyandu_id', $selectedPosyandu->id);
        } elseif ($user->user_type === 'admin_desa' && $request->filled('posyandu_id')) {
            // Jika ADMIN dan MEMILIH filter
            $selectedPosyandu = Posyandu::with('rws')->find($request->posyandu_id);
            $baseAnakTerpantauQuery->where('posyandu_id', $selectedPosyandu->id);
        }
        // Jika ADMIN dan TIDAK memilih filter, query akan mengambil semua data di desanya (berkat Trait/Scope)

        // --- 2. STATISTIK PARTISIPASI (menggunakan Trait/Scope di Warga) ---
        $semuaBalitaDiWilayah = Warga::whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) < 60')->get();
        $idBalitaTerpantau = (clone $baseAnakTerpantauQuery)->pluck('warga_id');
        $totalBalitaWilayah = $semuaBalitaDiWilayah->count();
        $totalBalitaTerpantau = $idBalitaTerpantau->unique()->count();
        $totalBalitaBelumTerpantau = $totalBalitaWilayah - $totalBalitaTerpantau;
        $persenTerpantau = ($totalBalitaWilayah > 0) ? round(($totalBalitaTerpantau / $totalBalitaWilayah) * 100) : 0;
        $persenBelumTerpantau = 100 - $persenTerpantau;

        // --- 3. DATA UNTUK DASHBOARD VISUAL (menggunakan query dasar) ---
        $stats = ['stunting' => 0, 'wasting' => 0, 'underweight' => 0, 'normal' => 0, 'dapat_vitamin_a' => 0, 'dapat_imunisasi_polio' => 0, 'persen_vit_a' => 0, 'persen_imunisasi' => 0];
        $semuaAnakDiKonteks = (clone $baseAnakTerpantauQuery)->with('pemeriksaanTerakhir')->get();
        $stats['total_balita_terpantau'] = $semuaAnakDiKonteks->count();

        if ($stats['total_balita_terpantau'] > 0) {
            foreach ($semuaAnakDiKonteks as $anak) {
                if ($p = $anak->pemeriksaanTerakhir) {
                    if (str_contains($p->status_stunting, 'Pendek'))
                        $stats['stunting']++;
                    if (str_contains($p->status_wasting, 'Kurus'))
                        $stats['wasting']++;
                    if (str_contains($p->status_underweight, 'Kurang'))
                        $stats['underweight']++;
                    if ($p->dapat_vitamin_a)
                        $stats['dapat_vitamin_a']++;
                    if ($p->dapat_imunisasi_polio)
                        $stats['dapat_imunisasi_polio']++;
                }
            }
            $stats['normal'] = $stats['total_balita_terpantau'] - ($stats['stunting'] + $stats['wasting'] + $stats['underweight']);
            $stats['normal'] = $stats['normal'] < 0 ? 0 : $stats['normal'];
            $stats['persen_vit_a'] = round(($stats['dapat_vitamin_a'] / $stats['total_balita_terpantau']) * 100);
            $stats['persen_imunisasi'] = round(($stats['dapat_imunisasi_polio'] / $stats['total_balita_terpantau']) * 100);
        }

        $anakStunting = $semuaAnakDiKonteks->filter(fn($a) => $a->pemeriksaanTerakhir && str_contains($a->pemeriksaanTerakhir->status_stunting, 'Pendek'));
        $anakWasting = $semuaAnakDiKonteks->filter(fn($a) => $a->pemeriksaanTerakhir && str_contains($a->pemeriksaanTerakhir->status_wasting, 'Kurus'));
        $anakUnderweight = $semuaAnakDiKonteks->filter(fn($a) => $a->pemeriksaanTerakhir && str_contains($a->pemeriksaanTerakhir->status_underweight, 'Kurang'));

        // --- 4. DATA UNTUK GRAFIK TREN ---
        $trendData = ['labels' => [], 'stunting' => [], 'wasting' => [], 'underweight' => [], 'normal' => []];
        $tahunYangDipilih = $request->input('tahun', Carbon::now()->year);
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $trendData['labels'][] = Carbon::create()->month($bulan)->isoFormat('MMM');

            // Buat query bulanan yang juga sadar hak akses
            $queryBulanan = PemeriksaanAnak::query()
                ->whereMonth('tanggal_pemeriksaan', $bulan)
                ->whereYear('tanggal_pemeriksaan', $tahunYangDipilih);

            // Jika ada posyandu dipilih, filter berdasarkan itu
            if ($selectedPosyandu) {
                $queryBulanan->where('posyandu_id', $selectedPosyandu->id);
            } else {
                // Jika TIDAK ada posyandu dipilih (tampilan global desa),
                // pastikan query tetap di dalam lingkup desa user.
                // Trait/Scope di model Warga akan menangani ini.
                $queryBulanan->whereHas('warga');
            }

            $totalPemeriksaanBulanIni = (clone $queryBulanan)->count();
            $stuntingCount = (clone $queryBulanan)->where('status_stunting', 'like', '%Pendek%')->count();
            $wastingCount = (clone $queryBulanan)->where('status_wasting', 'like', '%Kurus%')->count();
            $underweightCount = (clone $queryBulanan)->where('status_underweight', 'like', '%Kurang%')->count();
            $normalCount = $totalPemeriksaanBulanIni - ($stuntingCount + $wastingCount + $underweightCount);

            $trendData['stunting'][] = $stuntingCount;
            $trendData['wasting'][] = $wastingCount;
            $trendData['underweight'][] = $underweightCount;
            $trendData['normal'][] = $normalCount < 0 ? 0 : $normalCount;
        }

        // --- 5. DATA UNTUK TABEL AKSI (menggunakan query dasar yang sudah ada) ---
        $anakBaru = Warga::whereRaw('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) < 60')->whereNotIn('id', $idBalitaTerpantau)->latest('tanggal_lahir')->get();
        $anakTerpantau = (clone $baseAnakTerpantauQuery)->with(['warga.kartuKeluarga'])->withCount('riwayatPemeriksaan')->latest()->paginate(15)->withQueryString();
        $anakTerpantau->getCollection()->transform(function ($anak) {
            $anak->total_sesi = round($anak->created_at->diffInMonths(now()) + 1);
            return $anak;
        });

        // --- 6. KIRIM SEMUA DATA KE VIEW ---
        $posyandus = Posyandu::with('rws')
            ->where('desa_id', $desaId)
            ->latest()
            ->get();
            
        return view('admin_desa.kesehatan_anak.index', compact(
            'anakBaru',
            'anakTerpantau',
            'stats',
            'posyandus',
            'selectedPosyandu',
            'totalBalitaWilayah',
            'totalBalitaTerpantau',
            'totalBalitaBelumTerpantau',
            'persenTerpantau',
            'persenBelumTerpantau',
            'trendData',
            'tahunYangDipilih',
            'anakStunting',
            'anakWasting',
            'anakUnderweight'
        ));
    }

    public function store(Request $request, string $subdomain, )
    {
        $validated = $request->validate([
            'warga_ids' => 'required|array',
            'warga_ids.*' => 'exists:wargas,id',
            'posyandu_id' => 'required|exists:posyandu,id'
        ]);

        foreach ($validated['warga_ids'] as $wargaId) {
            $warga = Warga::find($wargaId);

            // mencegah duplikasi jika ada pengiriman ganda
            DataKesehatanAnak::firstOrCreate(
                [
                    'warga_id' => $wargaId,
                    'posyandu_id' => $validated['posyandu_id'],
                ],
                [
                    'tanggal_lahir' => $warga->tanggal_lahir,
                    'nama_ibu' => $warga->nama_ibu_kandung,
                    'nama_ayah' => $warga->nama_ayah_kandung,
                ]
            );
        }

        return redirect()->back()->with('success', count($validated['warga_ids']) . ' anak berhasil ditambahkan ke daftar pemantauan.');
    }

    // Di dalam PemeriksaanAnakController.php

    public function show(string $subdomain, DataKesehatanAnak $kesehatanAnak)
    {
        // Ambil semua riwayat pemeriksaan, urutkan dari yang paling lama ke yang baru
        $riwayatPemeriksaan = $kesehatanAnak->riwayatPemeriksaan()
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->get();

        // Siapkan "cat warna" untuk grafik
        $chartLabels = $riwayatPemeriksaan->pluck('tanggal_pemeriksaan')->map(function ($date) {
            return $date->format('d M Y');
        });

        $chartDataBeratBadan = $riwayatPemeriksaan->pluck('berat_badan');
        $chartDataTinggiBadan = $riwayatPemeriksaan->pluck('tinggi_badan');
        $chartDataHaz = $riwayatPemeriksaan->pluck('zscore_tb_u'); // Z-score Stunting

        return view('admin_desa.kesehatan_anak.show', [
            'kesehatanAnak' => $kesehatanAnak,
            'riwayatPemeriksaan' => $riwayatPemeriksaan, // Untuk tabel riwayat
            'chartLabels' => $chartLabels,
            'chartDataBeratBadan' => $chartDataBeratBadan,
            'chartDataTinggiBadan' => $chartDataTinggiBadan,
            'chartDataHaz' => $chartDataHaz,
        ]);
    }

    public function edit(string $subdomain, DataKesehatanAnak $kesehatanAnak)
    {
        $kesehatanAnak->load('warga');
        return view('admin_desa.kesehatan_anak.edit', compact('kesehatanAnak'));
    }

    public function update(Request $request, string $subdomain, DataKesehatanAnak $kesehatanAnak)
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

    public function destroy(string $subdomain, DataKesehatanAnak $kesehatanAnak)
    {
        $kesehatanAnak->delete();
        return redirect()->route('kesehatan-anak.index')->with('success', 'Data kesehatan anak berhasil dihapus.');
    }
}
