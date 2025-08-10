<?php

namespace App\Http\Controllers\Portal;

use App\Models\SuratSetting;
use App\Models\DataKesehatanAnak;
use App\Models\PemeriksaanAnak;
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

        return view('portal.posyandu.index', compact('posyandu', 'daftarBulan', 'desa', 'logo'));
    }

    public function showSesi(string $subdomain, $tahun, $bulan)
    {
        $user = Auth::user();
        $posyandu = $user->posyandu;
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');

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

        return view('portal.posyandu.sesi', compact('posyandu', 'tanggalSesi', 'isSesiSaatIni', 'anakBelumDiperiksa', 'pemeriksaanBulanIni', 'desa', 'logo'));
    }

    public function createPemeriksaan(string $subdomain, DataKesehatanAnak $anak)
    {
        $user = Auth::user();
        // Otorisasi sederhana untuk memastikan kader tidak menginput data anak dari posyandu lain
        if ($anak->posyandu_id != Auth::user()->posyandu_id) {
            abort(403);
        }
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');


        return view('portal.posyandu.pemeriksaan_create', ['anak' => $anak, 'desa' => $desa, 'logo' => $logo]);
    }

    /**
     * (Method untuk menampilkan halaman pemeriksaan dan menyimpan data
     * akan kita isi di langkah selanjutnya)
     */
    public function pemeriksaan(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');

        // Ambil daftar anak yang terdaftar di posyandu kader ini
        $anakDiPosyandu = DataKesehatanAnak::where('posyandu_id', $user->posyandu_id)
                                          ->with('warga')
                                          ->get();

        return view('portal.posyandu.pemeriksaan', compact('anakDiPosyandu', 'desa', 'logo'));
    }

    public function storePemeriksaan(Request $request, string $subdomain, StuntingCalculatorService $stuntingCalculator)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'data_kesehatan_anak_id' => 'required|exists:data_kesehatan_anaks,id',
            'tanggal_pemeriksaan' => 'required|date',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'lingkar_kepala' => 'nullable|numeric|min:0',
            'dapat_vitamin_a' => 'nullable|boolean',
            'dapat_imunisasi_polio' => 'nullable|boolean',
            'catatan' => 'nullable|string',
        ]);

        $dataKesehatanAnak = DataKesehatanAnak::with('warga')->find($validated['data_kesehatan_anak_id']);
        
        // Hitung status gizi menggunakan service yang sudah kita buat
        $warga = $dataKesehatanAnak->warga; // Relasi ini aman karena diambil dari data induk
        $jenisKelamin = $warga->jenis_kelamin;
        $tanggalLahir = Carbon::parse($warga->tanggal_lahir);
        $tanggalPengukuran = Carbon::parse($validated['tanggal_pemeriksaan']);
        $umurDalamHari = $tanggalLahir->diffInDays($tanggalPengukuran);
        $beratBadan = $validated['berat_badan'];
        $tinggiBadan = $validated['tinggi_badan'];

        // --- PANGGIL SEMUA KALKULATOR ---
        $zscore_tb_u = $stuntingCalculator->calculateHaz($jenisKelamin, $umurDalamHari, $tinggiBadan);
        $zscore_bb_u = $stuntingCalculator->calculateWaz($jenisKelamin, $umurDalamHari, $beratBadan);
        $zscore_bb_tb = $stuntingCalculator->calculateWhz($jenisKelamin, $tinggiBadan, $beratBadan);

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
            'usia_saat_periksa'     => $usiaFormatted,
        ]);

        return redirect()->route('portal.posyandu.index', ['subdomain' => $subdomain])
                         ->with('success', 'Data pemeriksaan untuk ' . $dataKesehatanAnak->warga->nama_lengkap . ' berhasil disimpan!');
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
            
        return view('portal.posyandu.laporan_index', compact('daftarLaporan', 'desa', 'logo'));
    }
    
    public function generateLaporan(string $subdomain, $tahun, $bulan, PosyanduReportController $reportController)
    {
        $user = Auth::user();
        $posyandu = $user->posyandu;

        // "Jembatani" request ke controller yang sudah ada
        return $reportController->generatePdf($subdomain, $posyandu, $bulan, $tahun);
    }

    private function getStatusStunting($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Sangat Pendek (Stunting Berat)';
        if ($zscore < -2) return 'Pendek (Stunting)';
        return 'Normal';
    }

    private function getStatusUnderweight($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Berat Badan Sangat Kurang';
        if ($zscore < -2) return 'Berat Badan Kurang';
        return 'Berat Badan Normal';
    }

    private function getStatusWasting($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Gizi Buruk (Sangat Kurus)';
        if ($zscore < -2) return 'Gizi Kurang (Kurus)';
        if ($zscore > 2) return 'Gizi Lebih (Overweight)';
        if ($zscore > 3) return 'Obesitas';
        return 'Gizi Baik (Normal)';
    }

    
}