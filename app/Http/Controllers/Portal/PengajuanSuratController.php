<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Warga;
use App\Models\PengajuanSurat;
use App\Models\SuratSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanSuratController extends Controller
{
    public function index(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        
        $pengajuans = PengajuanSurat::where('diajukan_oleh_user_id', Auth::id())
                                  ->with('warga', 'jenisSurat')
                                  ->latest()
                                  ->paginate(10);

        return view('portal.surat.index', compact('pengajuans', 'desa'));
    }
    public function create(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
                
        $jenisSurats = JenisSurat::orderBy('nama_surat')->get();
        $wargas = Warga::with('kartuKeluarga', 'rt', 'rw')->get();

        return view('portal.surat.pilih_warga', compact('jenisSurats', 'wargas','desa'));
    }


    public function verifikasi(Request $request, string $subdomain)
    {
        $validated = $request->validate([
            'nik' => 'required|numeric|digits:16'
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
        ]);
        
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user

        // Di sini nanti kita akan tambahkan logika Middleware Subdomain
        // Untuk sementara, kita filter manual berdasarkan desa pertama
        $warga = Warga::with('kartuKeluarga','rw', 'rt')
            ->where('nik', $validated['nik'])->first();

        if (!$warga) {
            return back()->withErrors(['nik' => 'Data warga dengan NIK tersebut tidak ditemukan, Atau bukan dalam Wilayah Anda']);
        }

        $request->session()->put('warga_id_mandiri', $warga->id);
        return redirect()->route('portal.surat.pilihSurat');
    }

    public function pilihJenisSurat(Request $request, string $subdomain)
    {
        // Validasi input dari form sebelumnya
        $validated = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
        ]);
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        
        // Ambil data warga yang dipilih
        $warga = Warga::findOrFail($validated['warga_id']);

        // Ambil semua jenis surat yang tersedia
        $jenisSurats = JenisSurat::orderBy('nama_surat')->get();

        // Kirim data ke view baru
        return view('portal.surat.pilih_jenis_surat', compact('warga', 'jenisSurats','desa'));
    }

    public function isiDetail(Request $request, string $subdomain)
    {
        // Validasi input dari form sebelumnya
        $validated = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
        ]);
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $suratSetting = $desa ? SuratSetting::where('desa_id', $desa->id)->first() : null;
        $logo = $suratSetting ? asset('storage/' . $suratSetting->path_logo_pemerintah) : asset('images/logo/logo-putih-trp.png');

        // Ambil data warga dan jenis surat
        $warga = Warga::findOrFail($validated['warga_id']);
        $jenisSurat = JenisSurat::findOrFail($validated['jenis_surat_id']);

        return view('portal.surat.isi_detail', compact('warga', 'jenisSurat', 'desa','logo'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan surat baru.
     */

    public function buatSurat(Request $request, string $subdomain, JenisSurat $jenisSurat)
    {
        // Keamanan: Pastikan warga sudah terverifikasi
        if (!$request->session()->has('warga_id_mandiri')) {
            return redirect()->route('anjungan.index')->withErrors(['nik' => 'Sesi Anda telah berakhir.']);
        }

        // Keamanan: Pastikan surat yang diminta memang untuk layanan mandiri
        if (!$jenisSurat->is_mandiri) {
            abort(403, 'Surat ini tidak tersedia untuk layanan mandiri.');
        }
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        
        $warga = Warga::findOrFail($request->session()->get('warga_id_mandiri'));

        // Kirim data warga dan jenis surat ke view
        return view('portal.surat.buat_surat', compact('warga', 'jenisSurat', 'desa'));
    }

    public function store(Request $request, string $subdomain)
    {
        // 1. Validasi semua input dari form
        $validated = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'keperluan' => 'required|string',
            'custom_fields' => 'nullable|array',
        ]);

        // 2. Tentukan status awal pengajuan
        // Karena diajukan oleh RT/RW, statusnya 'Diajukan' untuk menunggu approval Admin Desa
        $status = 'Diajukan';
        $jalur = 'rt_rw';

        // 3. Gabungkan semua data tambahan menjadi satu
        $detailTambahan = array_merge(
            $validated['custom_fields'] ?? [], 
            ['keperluan' => $validated['keperluan']]
        );

        // 4. Buat record PengajuanSurat baru di database
        PengajuanSurat::create([
            'desa_id' => auth()->user()->desa_id, // Ambil desa_id dari user RT/RW yang login
            'warga_id' => $validated['warga_id'],
            'jenis_surat_id' => $validated['jenis_surat_id'],
            'diajukan_oleh_user_id' => Auth::id(), // ID dari user RT/RW yang membuat
            'tanggal_pengajuan' => now(),
            'jalur_pengajuan' => $jalur,
            'status_permohonan' => $status,
            'detail_tambahan' => $detailTambahan,
        ]);

        // 5. Kembalikan ke halaman riwayat di portal dengan pesan sukses
        return redirect()->route('portal.surat.index', ['subdomain' => $subdomain])
                        ->with('success', 'Pengajuan surat berhasil dibuat dan telah diteruskan ke Admin Desa untuk diproses.');
    }

    public function buatPengantar(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        $wargas = Warga::with('kartuKeluarga', 'rt', 'rw')->get();

        return view('portal.surat.buat_pengantar', compact( 'wargas','desa'));
    }

    public function generatePengantar(Request $request, string $subdomain)
    {
        $data = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'keperluan' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $warga = Warga::findOrFail($data['warga_id']);

        if ($user->hasROle('admin_rt') && $warga->rt_id !== $user->rt_id) {
            abort(403);
        }
        if ($user->hasRole('admin_rw') && $warga->rw_id !== $user->rw_id) {
            abort(403);
        }

        $pengajuan = new PengajuanSurat($data);
        $pengajuan->warga = $warga;
        $desa = $warga->desa;
        $suratSetting = SuratSetting::withoutGlobalScopes()
                            ->where('desa_id', $desa->id)
                            ->first();
        $kopSuratBase64 = null;
        if (!empty($suratSetting->path_logo_pemerintah)) {
            $imagePath = storage_path('app/public/' . $suratSetting->path_logo_pemerintah);
            if (file_exists($imagePath)) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);
                $kopSuratBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $namaFile = 'Surat-Pengantar-' . Str::slug($warga->nama_lengkap) . '-' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('admin_desa.pengajuan_surat.cetak_pengantar', compact('pengajuan', 'desa', 'suratSetting', 'kopSuratBase64'));

        return $pdf->download($namaFile);
    }

}
