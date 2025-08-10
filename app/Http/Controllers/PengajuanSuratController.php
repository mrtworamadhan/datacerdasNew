<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use App\Models\SuratSetting;
use App\Models\Desa;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PengajuanSuratController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request, string $subdomain)
    {
        $user = Auth::user();
        // Cek hak akses umum untuk modul ini
        
        // Logika untuk Info Card (tetap sama)
        $stats = [
            'total' => PengajuanSurat::count(),
            'diproses' => PengajuanSurat::whereIn('status_permohonan', ['Diajukan', 'Diproses Desa'])->count(),
            'selesai' => PengajuanSurat::where('status_permohonan', 'Disetujui')->count(),
            'ditolak' => PengajuanSurat::where('status_permohonan', 'Ditolak')->count(),
        ];

        // Ambil data untuk tabel "Perlu Diproses" (tetap sama)
        $pengajuansPending = PengajuanSurat::with(['warga', 'jenisSurat', 'diajukanOleh'])
                                    ->whereIn('status_permohonan', ['Diajukan', 'Diproses Desa'])
                                    ->latest()
                                    ->paginate(10, ['*'], 'pending_page');

        // Query dasar untuk tabel "Riwayat Selesai"
        $finishedQuery = PengajuanSurat::with(['warga', 'jenisSurat'])
                                    ->whereIn('status_permohonan', ['Disetujui', 'Ditolak'])
                                    ->latest();

        // LOGIKA BARU: Terapkan filter pencarian jika ada
        if ($request->filled('search_nik')) {
            $nik = $request->search_nik;
            $finishedQuery->whereHas('warga', function ($q) use ($nik) {
                $q->where('nik', 'like', "%{$nik}%");
            });
        }

        $pengajuansFinished = $finishedQuery->paginate(10, ['*'], 'finished_page');
        
        // LOGIKA BARU: Handle request AJAX untuk live search
        if ($request->ajax()) {
            return view('admin_desa.pengajuan_surat._riwayat_table', compact('pengajuansFinished'))->render();
        }

        return view('admin_desa.pengajuan_surat.index', compact('pengajuansPending', 'pengajuansFinished', 'stats'));
    }

    public function create(string $subdomain)
    {
        $user = Auth::user();
        
        // Cek hak akses untuk membuat pengajuan surat (Admin RT ke atas)
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat pengajuan surat.');
        }
        // Ambil jenis surat yang aktif untuk desa ini (Global scope akan memfilter)
        $jenisSurats = JenisSurat::orderBy('nama_surat')->get();

        // Ambil warga untuk desa ini (Global scope akan memfilter sesuai user)
        $wargas = Warga::with('kartuKeluarga')->get(); // Diperlukan untuk dropdown warga

        return view('admin_desa.pengajuan_surat.create', compact('jenisSurats', 'wargas'));
    }

    public function reprint(string $subdomain, PengajuanSurat $pengajuanSurat)
    {        
        $user = Auth::user();
        // Cek hak akses untuk membuat pengajuan surat (Admin RT ke atas)
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() ) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat pengajuan surat.');
        }

        // Pastikan hanya surat yang disetujui yang bisa dicetak ulang
        if ($pengajuanSurat->status_permohonan !== 'Disetujui') {
            return redirect()->back()->with('error', 'Hanya surat yang sudah disetujui yang bisa dicetak ulang.');
        }

        $suratSetting = SuratSetting::firstOrCreate(['desa_id' => auth()->user()->desa_id]);
        $warga = $pengajuanSurat->warga;
        $desa = $warga->desa;
        $dataToReplace = [
            '[nama_warga]' => $warga->nama_lengkap,
            '[nik_warga]' => $warga->nik,
            '[tempat_lahir]' => $warga->tempat_lahir,
            '[tanggal_lahir]' => $warga->tanggal_lahir->translatedFormat('d F Y'),
            '[jenis_kelamin]' => $warga->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            '[alamat_lengkap]' => $warga->alamat_lengkap,
            '[agama]' => $warga->agama,
            '[status_perkawinan]' => $warga->status_perkawinan,
            '[pekerjaan]' => $warga->pekerjaan,
            '[kewarganegaraan]' => $warga->kewarganegaraan,
            '[nama_kepala_keluarga]' => $warga->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-',
            '[nomor_kk]' => $warga->kartuKeluarga->nomor_kk ?? '-',
            '[alamat_kk]' => $warga->kartuKeluarga->alamat ?? '-',
            '[tanggal_surat]' => now()->translatedFormat("d F Y"),
            '[jabatan_kades]' => $suratSetting->penanda_tangan_jabatan ?? 'Kepala Desa',
            '[nama_kades]' => $suratSetting->penanda_tangan_nama ?? 'Nama Kepala Desa',
            '[nama_desa]' => $desa->nama_desa ?? 'Nama Desa',
            '[kecamatan]' => $desa->kecamatan ?? 'Nama Kecamatan',
            '[nama_kota]' => $desa->kota ?? 'Nama Kota',
        ];

        // Ganti juga variabel custom
        if (is_array($pengajuanSurat->detail_tambahan)) {
            foreach ($pengajuanSurat->detail_tambahan as $key => $value) {
                $variableName = '[custom_' . strtolower($key) . ']';
                $dataToReplace[$variableName] = $value;
            }
        }
        $processedContent = str_replace(array_keys($dataToReplace), array_values($dataToReplace), $pengajuanSurat->jenisSurat->isi_template);

        $kopSuratBase64 = null;

        if ($suratSetting->path_kop_surat) {
            $path = public_path('storage/' . $suratSetting->path_kop_surat);
            if (file_exists($path)) {
                $type = mime_content_type($path);
                $data = base64_encode(file_get_contents($path));
                $kopSuratBase64 = "data:$type;base64,$data";
            }
        }
        $ttdBase64 = null;
        if ($suratSetting->path_ttd) {
            $path = public_path('storage/' . $suratSetting->path_ttd);
            if (file_exists($path)) {
                $type = mime_content_type($path);
                $data = base64_encode(file_get_contents($path));
                $ttdBase64 = "data:$type;base64,$data";
            }
        }
        // 4. Generate PDF
        $pdf = Pdf::loadView('admin_desa.pengajuan_surat.cetak', [
            'suratSetting' => $suratSetting,
            'pengajuanSurat' => $pengajuanSurat,
            'processedContent' => $processedContent,
            'desa' => $desa,
            'kopSuratBase64' => $kopSuratBase64,
            'ttdBase64' => $ttdBase64,
            'isReprint' => true,
        ]);

        $fileName = 'arsip-surat-' . \Illuminate\Support\Str::slug($pengajuanSurat->jenisSurat->nama_surat) . '.pdf';
        return $pdf->stream($fileName);
    }

    public function store(Request $request, string $subdomain)
    { 
        $validated = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'persyaratan_terpenuhi' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            
        ]);

        // Tentukan jalur pengajuan berdasarkan role user
        $jalur = (Auth::user()->user_type == 'admin_desa') ? 'langsung_desa' : 'rt_rw';
        $status = ($jalur == 'langsung_desa') ? 'Diproses Desa' : 'Diajukan';

        PengajuanSurat::create([
            'warga_id' => $validated['warga_id'],
            'jenis_surat_id' => $validated['jenis_surat_id'],
            'diajukan_oleh_user_id' => Auth::id(),
            'tanggal_pengajuan' => now(),
            'jalur_pengajuan' => $jalur,
            'status_permohonan' => $status,
            'persyaratan_terpenuhi' => $validated['persyaratan_terpenuhi'] ?? null,
            'detail_tambahan' => $validated['custom_fields'] ?? null,
        ]);

        // Nanti kita akan arahkan ke halaman daftar pengajuan
        return redirect()->route('pengajuan-surat.index')->with('success', 'Pengajuan surat berhasil dibuat.');
    }

    public function show(string $subdomain, PengajuanSurat $pengajuanSurat)
    {
        // Eager load relasi yang dibutuhkan
        $pengajuanSurat->load(['warga', 'jenisSurat', 'diajukanOleh']);

        return view('admin_desa.pengajuan_surat.show', compact('pengajuanSurat'));
    }
    // Method untuk API
    public function getJenisSuratDetails(string $subdomain, JenisSurat $jenisSurat)
    {
        return response()->json([
            'persyaratan' => $jenisSurat->persyaratan ?? [],
            'custom_fields' => $jenisSurat->custom_fields ?? [],
        ]);
    }

    public function approveAndPrint(string $subdomain, PengajuanSurat $pengajuanSurat)
    {
        // 1. Generate Nomor Surat (LOGIKA BARU YANG LEBIH AMAN)
        $tahun = date('Y');
        $klasifikasiKode = $pengajuanSurat->jenisSurat->klasifikasi->kode;
        $bulanRomawi = $this->getRomanMonth(date('n'));
        $desaId = $pengajuanSurat->desa_id;

        $lastNomorUrut = PengajuanSurat::where('desa_id', $desaId)
                                       ->whereYear('tanggal_selesai', $tahun)
                                       ->max('nomor_urut');
        
        $nomorUrutBaru = $lastNomorUrut + 1;
        $nomorSurat = "{$klasifikasiKode} / {$nomorUrutBaru} / {$bulanRomawi} / {$tahun}";

        // 2. Update status pengajuan
        $pengajuanSurat->update([
            'status_permohonan' => 'Disetujui',
            'nomor_urut' => $nomorUrutBaru,
            'nomor_surat' => $nomorSurat,
            'tanggal_selesai' => now(),
        ]);
        
        // 3. Siapkan data untuk dicetak
        $suratSetting = SuratSetting::firstOrCreate(['desa_id' => auth()->user()->desa_id]);
        $warga = $pengajuanSurat->warga;
        $desa = $warga->desa; // Ambil data desa dari warga

        $dataToReplace = [
            '[nama_warga]' => $warga->nama_lengkap,
            '[nik_warga]' => $warga->nik,
            '[tempat_lahir]' => $warga->tempat_lahir,
            '[tanggal_lahir]' => $warga->tanggal_lahir->translatedFormat('d F Y'),
            '[jenis_kelamin]' => $warga->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            '[alamat_lengkap]' => $warga->alamat_lengkap,
            '[agama]' => $warga->agama,
            '[status_perkawinan]' => $warga->status_perkawinan,
            '[pekerjaan]' => $warga->pekerjaan,
            '[kewarganegaraan]' => $warga->kewarganegaraan,
            '[nama_kepala_keluarga]' => $warga->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-',
            '[nomor_kk]' => $warga->kartuKeluarga->nomor_kk ?? '-',
            '[alamat_kk]' => $warga->kartuKeluarga->alamat ?? '-',
            '[tanggal_surat]' => now()->translatedFormat("d F Y"),
            '[jabatan_kades]' => $suratSetting->penanda_tangan_jabatan ?? 'Kepala Desa',
            '[nama_kades]' => $suratSetting->penanda_tangan_nama ?? 'Nama Kepala Desa',
            '[nama_desa]' => $desa->nama_desa ?? 'Nama Desa',
            '[kecamatan]' => $desa->kecamatan ?? 'Nama Kecamatan',
            '[nama_kota]' => $desa->kota ?? 'Nama Kota',
        ];

        // Ganti juga variabel custom
        if (is_array($pengajuanSurat->detail_tambahan)) {
            foreach ($pengajuanSurat->detail_tambahan as $key => $value) {
                $variableName = '[custom_' . strtolower($key) . ']';
                $dataToReplace[$variableName] = $value;
            }
        }

        $processedContent = $pengajuanSurat->jenisSurat->isi_template;
        foreach ($dataToReplace as $variable => $value) {
            $processedContent = str_replace($variable, "{$value}", $processedContent);
        }
        
        $kopSuratBase64 = null;

        if ($suratSetting->path_kop_surat) {
            $path = public_path('storage/' . $suratSetting->path_kop_surat);
            if (file_exists($path)) {
                $type = mime_content_type($path);
                $data = base64_encode(file_get_contents($path));
                $kopSuratBase64 = "data:$type;base64,$data";
            }
        }

        $ttdBase64 = null;
        if ($suratSetting->path_ttd) {
            $path = public_path('storage/' . $suratSetting->path_ttd);
            if (file_exists($path)) {
                $type = mime_content_type($path);
                $data = base64_encode(file_get_contents($path));
                $ttdBase64 = "data:$type;base64,$data";
            }
        }
        // 4. Generate PDF
        $pdf = Pdf::loadView('admin_desa.pengajuan_surat.cetak', [
            'suratSetting' => $suratSetting,
            'pengajuanSurat' => $pengajuanSurat,
            'processedContent' => $processedContent,
            'desa' => $desa,
            'kopSuratBase64' => $kopSuratBase64,
            'ttdBase64' => $ttdBase64,
        ]);

        $fileName = 'surat-' . \Illuminate\Support\Str::slug($pengajuanSurat->jenisSurat->nama_surat) . '-' . \Illuminate\Support\Str::slug($warga->nama_lengkap) . '.pdf';

        // Tampilkan PDF di browser
        return $pdf->stream($fileName);
    }

    public function reject(Request $request, string $subdomain, PengajuanSurat $pengajuanSurat)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $pengajuanSurat->update([
            'status_permohonan' => 'Ditolak',
            'catatan_penolakan' => $request->catatan_penolakan,
            'tanggal_selesai' => now(),
        ]);

        return redirect()->route('pengajuan-surat.index')->with('success', 'Permohonan surat berhasil ditolak.');
    }

    // Helper function untuk bulan romawi
    private function getRomanMonth($month)
    {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $returnValue = '';
        while ($month > 0) {
            foreach ($map as $roman => $int) {
                if ($month >= $int) {
                    $month -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public function generatePengantar(Request $request, string $subdomain)
    {
        $data = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'keperluan' => 'required|string',
        ]);

        $pengajuan = new PengajuanSurat($data);
        $pengajuan->warga = Warga::find($data['warga_id']);
        $desa = $pengajuan->warga->desa;
        $suratSetting = SuratSetting::firstOrCreate(['desa_id' => $desa->id]);

        $pdf = Pdf::loadView('admin_desa.pengajuan_surat.cetak_pengantar', compact('pengajuan', 'desa', 'suratSetting'));
        return $pdf->stream('surat-pengantar.pdf');
    }

}