<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warga;
use App\Models\JenisSurat;
use App\Models\Desa;
use App\Models\PengajuanSurat;
use App\Models\SuratSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AnjunganController extends Controller
{
    /**
     * Menampilkan halaman utama anjungan (form input NIK).
     */
    public function index(string $subdomain,)
    {
        return view('anjungan.index');
    }

    /**
     * Memverifikasi NIK yang diinput oleh warga.
     */
    public function verifikasi(Request $request, string $subdomain)
    {
        $validated = $request->validate([
            'nik' => 'required|numeric|digits:16'
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
        ]);

        // Di sini nanti kita akan tambahkan logika Middleware Subdomain
        // Untuk sementara, kita filter manual berdasarkan desa pertama
        $warga = Warga::where('nik', $validated['nik'])->first();

        if (!$warga) {
            return back()->withErrors(['nik' => 'Data warga dengan NIK tersebut tidak ditemukan.']);
        }

        $request->session()->put('warga_id_mandiri', $warga->id);
        return redirect()->route('anjungan.pilihSurat');
    }

    /**
     * Menampilkan halaman konfirmasi data dan daftar surat.
     */
    public function pilihSurat(Request $request, string $subdomain)
    {
        if (!$request->session()->has('warga_id_mandiri')) {
            return redirect()->route('anjungan.index')->withErrors(['nik' => 'Sesi Anda telah berakhir, silakan masukkan NIK kembali.']);
        }

        $warga = Warga::findOrFail($request->session()->get('warga_id_mandiri'));
        $jenisSurats = JenisSurat::where('is_mandiri', true)->get();

        return view('anjungan.pilih_surat', compact('warga', 'jenisSurats'));
    }

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

        $warga = Warga::findOrFail($request->session()->get('warga_id_mandiri'));

        // Kirim data warga dan jenis surat ke view
        return view('anjungan.buat_surat', compact('warga', 'jenisSurat'));
    }

    public function prosesSurat(Request $request, string $subdomain, JenisSurat $jenisSurat)
    {
        if (!$request->session()->has('warga_id_mandiri')) {
            return redirect()->route('anjungan.index');
        }

        $validated = $request->validate([
            'keperluan' => 'required|string',
            'custom_fields' => 'nullable|array',
            'ahli_waris' => 'nullable|array', // Validasi bahwa 'ahli_waris' adalah sebuah array
            'ahli_waris.*.nama' => 'required_with:ahli_waris|string', // Jika array ahli_waris ada, maka 'nama' di dalamnya wajib diisi
            'ahli_waris.*.nik' => 'required_with:ahli_waris|string',
            'ahli_waris.*.hubungan' => 'required_with:ahli_waris|string',
        ]);

        $warga = Warga::findOrFail($request->session()->get('warga_id_mandiri'));
        $desaId = $warga->desa_id;

        // Generate Nomor Surat
        $tahun = date('Y');
        $klasifikasiKode = $jenisSurat->klasifikasi->kode ?? '470';
        $bulanRomawi = $this->getRomanMonth(date('n'));
        $lastNomorUrut = PengajuanSurat::where('desa_id', $desaId)->whereYear('tanggal_selesai', $tahun)->max('nomor_urut');
        $nomorUrutBaru = $lastNomorUrut + 1;
        $nomorSurat = "{$klasifikasiKode} / {$nomorUrutBaru} / {$bulanRomawi} / {$tahun}";
        $detailTambahan = array_merge(
            $validated['custom_fields'] ?? [], 
            ['keperluan' => $validated['keperluan']],
            ['tabel ahli waris' => $validated['ahli_waris'] ?? []] // <-- Simpan data ahli waris
        );
        // Buat Record Pengajuan Surat
        $pengajuanSurat = PengajuanSurat::create([
            'desa_id' => $desaId,
            'warga_id' => $warga->id,
            'jenis_surat_id' => $jenisSurat->id,
            'diajukan_oleh_user_id' => null,
            'tanggal_pengajuan' => now(),
            'tanggal_selesai' => now(),
            'jalur_pengajuan' => 'mandiri',
            'status_permohonan' => 'Disetujui',
            'nomor_urut' => $nomorUrutBaru,
            'nomor_surat' => $nomorSurat,
            'detail_tambahan' => $detailTambahan,
        ]);

        // Arahkan ke halaman preview yang baru kita buat
        return redirect()->route('anjungan.showPreview', $pengajuanSurat->id);
    }

    public function showPreview(string $subdomain, PengajuanSurat $pengajuanSurat)
    {
        // (Di sini kita bisa tambahkan Gate/Policy untuk keamanan tambahan)
        
        return view('anjungan.preview_surat', compact('pengajuanSurat'));
    }

    
    public function printFinal(string $subdomain, PengajuanSurat $pengajuanSurat)
    {
        // 1. Siapkan semua "Aktor" Data
        $suratSetting = SuratSetting::firstOrCreate(['desa_id' => $pengajuanSurat->desa_id]);
        $desa = $pengajuanSurat->warga->desa;
        $warga = $pengajuanSurat->warga;
        
        // 2. Proses "Isi Surat" (mengganti placeholder)
        $dataToReplace = [
            '[nama_warga]' => $warga->nama_lengkap,
            '[nik_warga]' => $warga->nik,
            '[tempat_lahir]' => $warga->tempat_lahir,
            '[tanggal_lahir]' => $warga->tanggal_lahir->translatedFormat('d F Y'),
            '[jenis_kelamin]' => $warga->jenis_kelamin,
            '[alamat_lengkap]' => $warga->alamat_lengkap,
            '[agama]' => $warga->agama,
            '[status_perkawinan]' => $warga->status_perkawinan,
            '[pekerjaan]' => $warga->pekerjaan,
            '[kewarganegaraan]' => $warga->kewarganegaraan,
            '[nama_kepala_keluarga]' => $warga->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-',
            '[nomor_kk]' => $warga->kartuKeluarga->nomor_kk ?? '-',
            '[alamat_kk]' => $warga->kartuKeluarga->alamat ?? '-',
            '[tanggal_surat]' => $pengajuanSurat->tanggal_selesai->translatedFormat("d F Y"),
            '[jabatan_kades]' => $suratSetting->penanda_tangan_jabatan ?? 'Kepala Desa',
            '[nama_kades]' => $suratSetting->penanda_tangan_nama ?? 'Nama Kepala Desa',
            '[nama_desa]' => $desa->nama_desa ?? 'Nama Desa',
        ];

        if (is_array($pengajuanSurat->detail_tambahan)) {
            foreach ($pengajuanSurat->detail_tambahan as $key => $value) {
                // Lewati array ahli_waris untuk ditangani secara khusus nanti
                if ($key === 'tabel ahli waris') continue;
                
                $variableName = '[custom_' . Str::slug($key, '_') . ']';
                $dataToReplace[$variableName] = e($value); // Gunakan e() untuk keamanan
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
        
        $ahliWarisData = $pengajuanSurat->detail_tambahan['tabel ahli waris'] ?? [];
        if (!empty($ahliWarisData)) {
            $tabelHtml = '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" border="1">';
            $tabelHtml .= '<thead><tr style="background-color: #f2f2f2;">';
            $tabelHtml .= '<th style="padding: 5px; text-align: center;">No</th>';
            $tabelHtml .= '<th style="padding: 5px;"></th>';
            $tabelHtml .= '<th style="padding: 5px;">Nama Lengkap</th>';
            $tabelHtml .= '<th style="padding: 5px;">NIK</th>';
            $tabelHtml .= '<th style="padding: 5px;">Hubungan Keluarga</th>';
            $tabelHtml .= '</tr></thead><tbody>';
            
            $nomor = 1;
            foreach ($ahliWarisData as $waris) {
                $tabelHtml .= '<tr>';
                $tabelHtml .= '<td style="padding: 5px; text-align: center;">' . $nomor++ . '.</td>';
                $tabelHtml .= '<td style="padding: 5px; text-align: center;"></td>';
                $tabelHtml .= '<td style="padding: 5px;">' . e($waris['nama']) . '</td>';
                $tabelHtml .= '<td style="padding: 5px;">' . e($waris['nik']) . '</td>';
                $tabelHtml .= '<td style="padding: 5px;">' . e($waris['hubungan']) . '</td>';
                $tabelHtml .= '</tr>';
            }
            
            $tabelHtml .= '</tbody></table>';
            $processedContent = str_replace('[custom_tabel_ahli_waris]', $tabelHtml, $processedContent);
        }

        // dd($suratSetting);
        // Kirim ke template cetak yang SAMA dengan yang dipakai admin
        // Perhatikan: TIDAK ADA lagi flag $isAnjungan di sini
        return view('admin_desa.pengajuan_surat.cetak', [
            'pengajuanSurat' => $pengajuanSurat,
            'processedContent' => $processedContent,
            'suratSetting' => $suratSetting,
            'desa' => $desa,
            'kopSuratBase64' => $kopSuratBase64,
        ]);
    }

    /**
     * Helper function untuk mengubah angka bulan menjadi romawi.
     */
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
}