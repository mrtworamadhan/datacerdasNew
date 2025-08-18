<?php

namespace App\Http\Controllers\Portal;

use App\Models\SuratSetting;
use App\Models\DataKesehatanAnak;
use App\Models\PemeriksaanAnak;
use App\Models\Warga;
use App\Services\StuntingCalculatorService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PosyanduReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class PosyanduController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama untuk kader posyandu.
     */
    public function index(string $subdomain)
    {
        $user = Auth::user();
        $posyandu = $user->posyandu;

        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');

        // Siapkan daftar 6 bulan terakhir untuk pilihan sesi
        $daftarBulan = [];
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $daftarBulan[] = [
                'tahun' => $date->year,
                'bulan' => $date->month,
                'nama' => $date->isoFormat('MMMM YYYY'),
            ];
        }

        return view('portal.posyandu.index', compact('posyandu', 'subdomain', 'daftarBulan', 'desa', 'logo'));
    }

    public function showSesi(string $subdomain, $tahun, $bulan)
    {
        $user = Auth::user();
        $posyandu = $user->posyandu;
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        
        $tanggalSesi = Carbon::createFromDate($tahun, $bulan);
        $isSesiSaatIni = $tanggalSesi->isCurrentMonth();

        // Ambil ID dari data kesehatan anak yang sudah diperiksa di sesi ini
        $idAnakSudahDiperiksa = PemeriksaanAnak::where('posyandu_id', $posyandu->id)
            ->whereMonth('tanggal_pemeriksaan', $bulan)
            ->whereYear('tanggal_pemeriksaan', $tahun)
            ->pluck('data_kesehatan_anak_id');

        // Ambil semua anak yang terpantau di posyandu ini
        $semuaAnakTerpantau = DataKesehatanAnak::where('posyandu_id', $posyandu->id)
                                             ->with('warga')
                                             ->get();
        
        // Pisahkan mana yang sudah dan belum
        $anakBelumDiperiksa = $semuaAnakTerpantau->whereNotIn('id', $idAnakSudahDiperiksa);
        $pemeriksaanBulanIni = PemeriksaanAnak::with('warga')->whereIn('data_kesehatan_anak_id', $idAnakSudahDiperiksa)->get();
        $totalAnakTerdata = DataKesehatanAnak::where('posyandu_id', $posyandu->id)->count();
        $jumlahHadir = $pemeriksaanBulanIni->count();
        $jumlahBelumHadir = $totalAnakTerdata - $jumlahHadir;

        return view('portal.posyandu.sesi', compact(
            'posyandu', 'tanggalSesi', 'isSesiSaatIni', 'anakBelumDiperiksa', 
            'pemeriksaanBulanIni', 'desa','subdomain',
            'totalAnakTerdata', 'jumlahHadir', 'jumlahBelumHadir' // <-- Variabel baru
        ));
    }

    // app/Http/Controllers/Portal/PosyanduController.php

    public function createPemeriksaan(string $subdomain, Warga $anak)
    {
        $user = Auth::user();

        // 1. Otorisasi dasar: Pastikan anak ini dari desa yang sama
        if ($anak->desa_id != $user->desa_id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // 2. Cek apakah anak sudah terdaftar untuk dipantau di posyandu mana pun
        $dataKesehatanAnak = DataKesehatanAnak::where('warga_id', $anak->id)->first();
        
        // 3. JIKA BELUM, BUAT OTOMATIS!
        if (!$dataKesehatanAnak) {
            $dataKesehatanAnak = DataKesehatanAnak::create([
                'warga_id' => $anak->id,
                'posyandu_id' => $user->posyandu_id,
                'tanggal_lahir' => $anak->tanggal_lahir,
            ]);
        }

        // 4. Otorisasi kedua: Pastikan data kesehatan ini milik posyandu kader
        if ($dataKesehatanAnak->posyandu_id != $user->posyandu_id) {
            // Ini terjadi jika anak terdaftar di posyandu lain
            return redirect()->back()->with('error', 'Anak ini terdaftar di posyandu lain.');
        }

        $desa = $user->desa;
        
        return view('portal.posyandu.pemeriksaan_create', compact(
            'anak', 
            'dataKesehatanAnak',
            'desa', 
            'subdomain'
        ));
    }

    /**
     * (Method untuk menampilkan halaman pemeriksaan dan menyimpan data
     * akan kita isi di langkah selanjutnya)
     */
    public function pemeriksaan(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
    
        // Ambil daftar anak yang terdaftar di posyandu kader ini
        $anakDiPosyandu = DataKesehatanAnak::where('posyandu_id', $user->posyandu_id)
                                          ->with('warga')
                                          ->get();

        return view('portal.posyandu.pemeriksaan', compact('anakDiPosyandu', 'desa', 'subdomain'));
    }

    public function storePemeriksaan(Request $request, string $subdomain, StuntingCalculatorService $calculator)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'data_kesehatan_anak_id' => 'required|exists:data_kesehatan_anaks,id',
            'tanggal_pemeriksaan' => 'required|date',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'lila' => 'nullable|numeric|min:0',
            'dapat_vitamin_a' => 'nullable|boolean',
            'dapat_imunisasi_polio' => 'nullable|boolean',
            'catatan' => 'nullable|string',
        ]);

        $dataKesehatanAnak = DataKesehatanAnak::with('warga')->find($validated['data_kesehatan_anak_id']);
        
        $warga = $dataKesehatanAnak->warga; // Relasi ini aman karena diambil dari data induk
        $jenisKelamin = $warga->jenis_kelamin;
        $tanggalLahir = Carbon::parse($warga->tanggal_lahir);
        $tanggalPengukuran = Carbon::parse($validated['tanggal_pemeriksaan']);
        $umurDalamHari = $tanggalLahir->diffInDays($tanggalPengukuran);
        $beratBadan = $validated['berat_badan'];
        $tinggiBadan = $validated['tinggi_badan'];

        // --- PANGGIL SEMUA KALKULATOR ---
        $zscore_tb_u = $calculator->calculateHaz($jenisKelamin, $umurDalamHari, $tinggiBadan);
        $zscore_bb_u = $calculator->calculateWaz($jenisKelamin, $umurDalamHari, $beratBadan);
        $zscore_bb_tb = $calculator->calculateWhz($jenisKelamin, $tinggiBadan, $beratBadan);

        // --- TENTUKAN SEMUA STATUS GIZI ---
        $status_stunting = $this->getStatusStunting($zscore_tb_u);
        $status_underweight = $this->getStatusUnderweight($zscore_bb_u);
        $status_wasting = $this->getStatusWasting($zscore_bb_tb);

        $age = Carbon::parse($tanggalLahir)->diff($tanggalPengukuran);
        $usiaBulan = $age->y * 12 + $age->m;
        $usiaHari = $age->d;
        $usiaFormatted = "{$usiaBulan} bulan, {$usiaHari} hari";
        // Buat record pemeriksaan baru dengan semua data lengkap
        $dataKesehatanAnak->riwayatPemeriksaan()->create([
            'tanggal_pemeriksaan'   => $validated['tanggal_pemeriksaan'],
            'posyandu_id'           => $dataKesehatanAnak->posyandu_id,
            'berat_badan'           => $beratBadan,
            'tinggi_badan'          => $tinggiBadan,
            'lila'                  => $validated['lila'] ?? null,
            
            'zscore_tb_u'           => $zscore_tb_u,
            'status_stunting'       => $status_stunting,
            'zscore_bb_u'           => $zscore_bb_u,
            'status_underweight'    => $status_underweight,
            'zscore_bb_tb'          => $zscore_bb_tb,
            'status_wasting'        => $status_wasting,
            
            'dapat_vitamin_a'       => $request->boolean('dapat_vitamin_a'),
            'dapat_obat_cacing'     => $request->boolean('dapat_obat_cacing'),
            'dapat_imunisasi_polio' => $request->boolean('dapat_imunisasi_polio'),
            'catatan_kader'         => $validated['catatan_kader'] ?? null,
            'usia_saat_periksa'     => $umurDalamHari,
        ]);

        return redirect()->route('portal.posyandu.index', ['subdomain' => $subdomain])
                         ->with('success', 'Data pemeriksaan untuk ' . $dataKesehatanAnak->warga->nama_lengkap . ' berhasil disimpan!');
    }

    // app/Http/Controllers/Portal/PosyanduController.php

    /**
     * Menampilkan form untuk mengedit data pemeriksaan.
     */
    public function editPemeriksaan(string $subdomain, PemeriksaanAnak $pemeriksaan)
    {
        // Otorisasi: Pastikan kader hanya bisa mengedit data dari posyandunya
        if ($pemeriksaan->posyandu_id !== Auth::user()->posyandu_id) {
            abort(403);
        }

        $user = Auth::user();
        $desa = $user->desa;

        $pemeriksaan->load('dataAnak.warga');

        return view('portal.posyandu.pemeriksaan_edit', compact(
            'pemeriksaan', 
            'desa', 
            'subdomain'
        ));
    }

    /**
     * Memperbarui data pemeriksaan di database.
     */
    public function updatePemeriksaan(Request $request, string $subdomain, PemeriksaanAnak $pemeriksaan, StuntingCalculatorService $stuntingCalculator)
    {
        // Otorisasi: Pastikan kader hanya bisa mengupdate data dari posyandunya
        if ($pemeriksaan->posyandu_id !== Auth::user()->posyandu_id) {
            abort(403);
        }

        // Validasi input dari form
        $validated = $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'lila' => 'nullable|numeric|min:0',
            'dapat_vitamin_a' => 'nullable|boolean',
            'dapat_imunisasi_polio' => 'nullable|boolean',
            'catatan' => 'nullable|string',
        ]);

        // Ambil data anak untuk perhitungan ulang
        $warga = $pemeriksaan->dataAnak->warga;
        $tanggalLahir = Carbon::parse($warga->tanggal_lahir);
        $tanggalPengukuran = Carbon::parse($validated['tanggal_pemeriksaan']);

        // Hitung ulang semua Z-Score dan status gizi
        $usiaDalamHari = $tanggalLahir->diffInDays($tanggalPengukuran);
        $zscore_tb_u = $stuntingCalculator->calculateHaz($warga->jenis_kelamin, $usiaDalamHari, $validated['tinggi_badan']);
        $zscore_bb_u = $stuntingCalculator->calculateWaz($warga->jenis_kelamin, $usiaDalamHari, $validated['berat_badan']);
        $zscore_bb_tb = $stuntingCalculator->calculateWhz($warga->jenis_kelamin, $validated['tinggi_badan'], $validated['berat_badan']);
        $status_stunting = $this->getStatusStunting($zscore_tb_u);
        $status_underweight = $this->getStatusUnderweight($zscore_bb_u);
        $status_wasting = $this->getStatusWasting($zscore_bb_tb);

        // Update record pemeriksaan dengan semua data lengkap yang baru
        $pemeriksaan->update([
            'tanggal_pemeriksaan'  => $validated['tanggal_pemeriksaan'],
            'berat_badan'          => $validated['berat_badan'],
            'tinggi_badan'         => $validated['tinggi_badan'],
            'lila'                  => $validated['lila'] ?? null,
            
            'zscore_tb_u'          => $zscore_tb_u,
            'status_stunting'      => $status_stunting,
            'zscore_bb_u'          => $zscore_bb_u,
            'status_underweight'   => $status_underweight,
            'zscore_bb_tb'         => $zscore_bb_tb,
            'status_wasting'       => $status_wasting,
            
            'dapat_vitamin_a'      => $request->boolean('dapat_vitamin_a'),
            'dapat_imunisasi_polio'=> $request->boolean('dapat_imunisasi_polio'),
            'catatan'              => $validated['catatan'] ?? null,
            'usia_saat_periksa_hari' => $usiaDalamHari,
        ]);

        // Redirect kembali ke halaman sesi agar kader bisa lihat hasilnya
        return redirect()->route('portal.posyandu.sesi.show', [
            'subdomain' => $subdomain,
            'tahun' => $tanggalPengukuran->year,
            'bulan' => $tanggalPengukuran->month,
        ])->with('success', 'Data pemeriksaan untuk ' . $warga->nama_lengkap . ' berhasil diperbarui!');
    }

    public function laporan(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');

        // Ambil semua data pemeriksaan unik berdasarkan bulan dan tahun
        $daftarLaporan = PemeriksaanAnak::where('posyandu_id', $user->posyandu_id)
            ->selectRaw('YEAR(tanggal_pemeriksaan) as tahun, MONTH(tanggal_pemeriksaan) as bulan')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
            
        return view('portal.posyandu.laporan_index', compact('daftarLaporan', 'subdomain','desa', 'logo'));
    }
    
    public function generateLaporan(string $subdomain, $tahun, $bulan, PosyanduReportController $reportController)
    {
        $user = Auth::user();
        $posyandu = $user->posyandu;

        // "Jembatani" request ke controller yang sudah ada
        return $reportController->generatePdf($subdomain, $posyandu, $bulan, $tahun);
    }

    public function findAnak(Request $request, string $subdomain)
    {
        $search = $request->query('q');

        // Cari anak di posyandu ini saja
        $anakIds = DataKesehatanAnak::where('posyandu_id', Auth::user()->posyandu_id)->pluck('warga_id');

        $anak = Warga::whereIn('id', $anakIds)
                    ->where(function ($query) use ($search) {
                        $query->where('nama_lengkap', 'LIKE', "%{$search}%")
                            ->orWhere('nik', 'LIKE', "%{$search}%");
                    })
                    ->limit(20)
                    ->get();
        
        // Format data untuk Select2
        $formatted_anak = $anak->map(function ($item) use ($subdomain) {
            $dataKesehatanAnak = DataKesehatanAnak::where('warga_id', $item->id)->first();
            return [
                // 'id' di sini adalah URL ke halaman detail
                'id'   => route('portal.posyandu.rekam_medis.show', ['subdomain' => $subdomain, 'kesehatanAnak' => $dataKesehatanAnak->id]),
                'text' => $item->nama_lengkap . ' - (NIK: ' . $item->nik . ')',
            ];
        }); 

        return response()->json($formatted_anak);
    }

    public function findAnakBySesi(Request $request, string $subdomain, $tahun, $bulan)
    {
        // 1. Ambil dulu ID semua Warga yang sudah diperiksa di sesi ini
        $wargaIdsSudahDiperiksa = PemeriksaanAnak::whereYear('pemeriksaan_anaks.tanggal_pemeriksaan', $tahun)
                                        ->whereMonth('pemeriksaan_anaks.tanggal_pemeriksaan', $bulan)
                                        ->where('pemeriksaan_anaks.posyandu_id', Auth::user()->posyandu_id)
                                        ->join('data_kesehatan_anaks', 'pemeriksaan_anaks.data_kesehatan_anak_id', '=', 'data_kesehatan_anaks.id')
                                        ->pluck('data_kesehatan_anaks.warga_id');

        $search = $request->query('q');

        // 2. Lakukan pencarian seperti biasa, TAPI tambahkan filter whereNotIn
        $anak = Warga::where('desa_id', Auth::user()->desa_id)
                    ->whereNotIn('id', $wargaIdsSudahDiperiksa) // <-- INI KUNCINYA
                    ->where(function ($query) use ($search) {
                        $query->where('nama_lengkap', 'LIKE', "%{$search}%")
                            ->orWhere('nik', 'LIKE', "%{$search}%");
                    })
                    ->where('tanggal_lahir', '>=', now()->subYears(5)->toDateString())
                    ->limit(20)
                    ->get();
        
        // Format data agar sesuai dengan yang dibutuhkan Select2
        $formatted_anak = $anak->map(function ($item) use ($subdomain) {
            return [
                'id'   => route('portal.posyandu.pemeriksaan.create', ['subdomain' => $subdomain, 'anak' => $item->id]),
                'text' => $item->nama_lengkap . ' - (NIK: ' . $item->nik . ')',
            ];
        });

        return response()->json($formatted_anak);
    }

    // app/Http/Controllers/Portal/PosyanduController.php

    public function storeAnakBaru(Request $request, string $subdomain)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nama_ibu_kandung' => 'required|string|max:255', // Ini dari kode sebelumnya, kita ubah
            'nik_ibu' => 'required|string|digits:16|exists:wargas,nik',
            'nama_ayah_kandung' => 'nullable|string|max:255',
        ]);

        // Cari data ibu dan Kartu Keluarganya
        $ibu = Warga::where('nik', $validated['nik_ibu'])->first();
        $kartuKeluarga = $ibu->kartuKeluarga;

        if (!$kartuKeluarga) {
            return redirect()->back()->with('error', 'Data Kartu Keluarga dari NIK Ibu tidak ditemukan.');
        }

        // Buat record Warga baru dengan semua data yang dibutuhkan
        $anakBaru = Warga::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'tempat_lahir' => $validated['tempat_lahir'], // Data baru
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'], // Data sudah sesuai enum
            
            'nama_ibu_kandung' => $ibu->nama_lengkap, 
            'nama_ayah_kandung' => $validated['nama_ayah_kandung'] ?? null,
            
            'desa_id' => $kartuKeluarga->desa_id,
            'kartu_keluarga_id' => $kartuKeluarga->id,
            'rw_id' => $kartuKeluarga->rw_id,
            'rt_id' => $kartuKeluarga->rt_id,
            
            'nik' => null,
            'status_data' => 'Data Sementara',
            
            // Mengambil data dari ibu untuk mengisi kolom wajib lainnya
            'agama' => $ibu->agama,
            'kewarganegaraan' => $ibu->kewarganegaraan,
            'alamat_lengkap' => $kartuKeluarga->alamat_lengkap, // Ambil alamat dari KK
            
            // Nilai default
            'status_perkawinan' => 'Belum Kawin',
            'pekerjaan' => 'Belum/Tidak Bekerja',
            'pendidikan' => 'Tidak/Belum Sekolah',
            'hubungan_keluarga' => 'Anak',
        ]);

        // Redirect ke halaman input pemeriksaan untuk anak yang baru saja dibuat
        return redirect()->route('portal.posyandu.pemeriksaan.create', [
            'subdomain' => $subdomain,
            'anak' => $anakBaru->id,
        ])->with('success', 'Data anak baru berhasil dibuat! Silakan lanjutkan dengan input pemeriksaan.');
    }

    public function showRekamMedisDetail(string $subdomain, DataKesehatanAnak $kesehatanAnak)
    {
        // Otorisasi: pastikan kader hanya bisa melihat data posyandunya
        if ($kesehatanAnak->posyandu_id !== Auth::user()->posyandu_id) {
            abort(403);
        }
        // Ambil semua data dasar
        $user = Auth::user();
        $desa = $user->desa;

        $riwayatPemeriksaan = $kesehatanAnak->riwayatPemeriksaan()
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->get();

        // --- TAMBAHKAN BLOK LOGIKA INI ---
        $statusTerakhir = 'Normal'; // Default
        $pemeriksaanTerakhir = $riwayatPemeriksaan->last();

        if ($pemeriksaanTerakhir) {
            $statusStunting = $pemeriksaanTerakhir->status_stunting;
            $statusWasting = $pemeriksaanTerakhir->status_wasting;

            // Prioritaskan status yang paling berisiko
            if (stripos($statusWasting, 'Buruk') !== false || stripos($statusStunting, 'Berat') !== false) {
                $statusTerakhir = 'Perhatian Khusus (Merah)';
            } elseif (stripos($statusStunting, 'Pendek') !== false || stripos($pemeriksaanTerakhir->status_underweight, 'Kurang') !== false || stripos($statusWasting, 'Kurang') !== false) {
                $statusTerakhir = 'Berisiko (Kuning)';
            }
        }
        $chartLabels = $riwayatPemeriksaan->pluck('tanggal_pemeriksaan')->map(fn($date) => $date->format('d M Y'));
        $chartDataBeratBadan = $riwayatPemeriksaan->pluck('berat_badan');
        $chartDataTinggiBadan = $riwayatPemeriksaan->pluck('tinggi_badan');
        $chartDataHaz = $riwayatPemeriksaan->pluck('zscore_tb_u');

        return view('portal.posyandu.rekam_medis_show', compact(
            'kesehatanAnak', 'riwayatPemeriksaan', 'chartLabels', 
            'chartDataBeratBadan', 'chartDataTinggiBadan', 'chartDataHaz',
            'desa', 'subdomain', 'statusTerakhir'
        ));
    }

    public function showRekamMedisSearch(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa;
        // Cukup tampilkan view dengan data dasar
        return view('portal.posyandu.rekam_medis_search', compact('desa', 'subdomain'));
    }

    private function getStatusStunting($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Stunting Berat';
        if ($zscore < -2) return 'Stunting';
        return 'Normal';
    }

    private function getStatusUnderweight($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'BB SangatKurang';
        if ($zscore < -2) return 'BB Kurang';
        return 'BB Normal';
    }

    private function getStatusWasting($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Gizi Buruk';
        if ($zscore < -2) return 'Gizi Kurang';
        if ($zscore > 2) return 'Gizi Lebih';
        if ($zscore > 3) return 'Obesitas';
        return 'Gizi Baik';
    }

    
}