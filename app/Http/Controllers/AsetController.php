<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetGolongan; // Kita butuh ini untuk mencari di database
use App\Models\AsetBidang;
use App\Models\AsetSubSubKelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Untuk memanggil AI
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Log; 



class AsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $subdomain)
    {
        $asets = Aset::with('subSubKelompok.subKelompok.kelompok.bidang.golongan')
                    ->latest()
                    ->paginate(20); // Gunakan paginasi agar tidak berat

        return view('admin_desa.asets.index', compact('asets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $subdomain)
    {
        return view('admin_desa.asets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $subdomain,)
    {
        // 1. Validasi semua input
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'aset_sub_sub_kelompok_id' => 'required|exists:aset_sub_sub_kelompoks,id',
            'tahun_perolehan' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'nilai_perolehan' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'sumber_dana' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string|max:255',
            'foto_aset' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
        ], [
            'aset_sub_sub_kelompok_id.required' => 'Anda harus menggunakan tombol "Cari Kode (AI)" untuk menentukan kode aset terlebih dahulu.',
        ]);

        $validated['desa_id'] = auth()->user()->desa_id;
        
        $subSubKelompok = AsetSubSubKelompok::with('subKelompok.kelompok.bidang.golongan')->find($validated['aset_sub_sub_kelompok_id']);
        $kodeBagianDepan = implode('.', [
            $subSubKelompok->subKelompok->kelompok->bidang->golongan->kode_golongan,
            $subSubKelompok->subKelompok->kelompok->bidang->kode_bidang,
            $subSubKelompok->subKelompok->kelompok->kode_kelompok,
            $subSubKelompok->subKelompok->kode_sub_kelompok,
            $subSubKelompok->kode_sub_sub_kelompok,
        ]);

        $nomorTerakhir = Aset::where('desa_id', auth()->user()->desa_id)
                       ->where('kode_aset', 'like', $kodeBagianDepan . '%')
                       ->count();
                       
        $nomorUrutBaru = str_pad($nomorTerakhir + 1, 4, '0', STR_PAD_LEFT);

        $validated['kode_aset'] = "{$kodeBagianDepan}.{$nomorUrutBaru}";

        if ($request->hasFile('foto_aset')) {
            $path = $request->file('foto_aset')->store('foto_aset', 'public');
            $validated['foto_aset'] = $path;
        }

        Aset::create($validated);

        return redirect()->route('asets.index')->with('status', 'Aset baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail spesifik dari sebuah aset.
     */
    public function show(string $subdomain, Aset $aset)
    {
        $aset->load('subSubKelompok.subKelompok.kelompok.bidang.golongan');
        return view('admin_desa.asets.show', compact('aset'));
    }

    /**
     * Menampilkan form untuk mengedit aset.
     */
    public function edit(string $subdomain, Aset $aset)
    {
        return view('admin_desa.asets.edit', compact('aset'));
    }

    /**
     * Mengupdate data aset yang sudah ada di database.
     */
    public function update(Request $request, string $subdomain, Aset $aset)
    {
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'tahun_perolehan' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'nilai_perolehan' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'sumber_dana' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string|max:255',
            'foto_aset' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto_aset')) {
            if ($aset->foto_aset && \Illuminate\Support\Facades\Storage::exists($aset->foto_aset)) {
                \Illuminate\Support\Facades\Storage::delete($aset->foto_aset);
            }
            $path = $request->file('foto_aset')->store('foto_aset', 'public');
            $validated['foto_aset'] = $path;
        }

        $aset->update($validated);

        return redirect()->route('asets.index')->with('status', 'Data aset berhasil diperbarui!');
    }

    /**
     * Menghapus data aset dari database.
     */
    public function destroy(string $subdomain, Aset $aset)
    {
        // Hapus foto dari storage sebelum menghapus record
        if ($aset->foto_aset && \Illuminate\Support\Facades\Storage::exists($aset->foto_aset)) {
            \Illuminate\Support\Facades\Storage::delete($aset->foto_aset);
        }
        
        $aset->delete();

        return redirect()->route('asets.index')->with('status', 'Data aset berhasil dihapus!');
    }

    // Di dalam AsetController.php

    public function findCodeByAI(Request $request, string $subdomain,)
    {
        $request->validate(['nama_aset' => 'required|string|min:3']);
        $namaAset = $request->nama_aset;

        $daftarKodeLengkap = \Illuminate\Support\Facades\Cache::remember('daftar_kode_aset', 60, function () {
            return AsetSubSubKelompok::with('subKelompok.kelompok.bidang.golongan')->get()->map(function ($item) {
                $golongan = $item->subKelompok->kelompok->bidang->golongan;
                $bidang = $item->subKelompok->kelompok->bidang;
                $kelompok = $item->subKelompok->kelompok;
                $subKelompok = $item->subKelompok;

                $kode = "{$golongan->kode_golongan}.{$bidang->kode_bidang}.{$kelompok->kode_kelompok}.{$subKelompok->kode_sub_kelompok}.{$item->kode_sub_sub_kelompok}";
                $deskripsi = "{$golongan->nama_golongan} > {$bidang->nama_bidang} > {$kelompok->nama_kelompok} > {$subKelompok->nama_sub_kelompok} > {$item->nama_sub_sub_kelompok}";
                
                return "Kode: {$kode}, Deskripsi: {$deskripsi}";
            })->implode("\n");
        });

        $prompt = "
        Anda adalah sistem pencari kode aset desa yang sangat akurat.
        Tugas Anda: Analisis nama aset '{$namaAset}' dan pilih SATU kode yang paling relevan dari DAFTAR KODE VALID di bawah ini.

        DAFTAR KODE VALID:
        ---
        {$daftarKodeLengkap}
        ---

        KEMBALIKAN HANYA KODE LENGKAP 5 LEVEL (contoh: 02.06.01.01.002), tanpa teks atau penjelasan lain.
        Jika sama sekali tidak ada yang cocok, kembalikan teks 'TIDAK_COCOK'.
        ";

        try {
            $apiKey = env('GEMINI_API_KEY');
            if (empty($apiKey)) {
                return response()->json(['success' => false, 'message' => 'Kunci API Gemini belum diatur di file .env.']);
            }

            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json(['success' => false, 'message' => 'Gagal terhubung dengan layanan AI. Respons tidak berhasil.']);
            }

            $aiResponseJson = $response->json()['candidates'][0]['content']['parts'][0]['text'];
            $kodeDariAI = trim($aiResponseJson);

            if ($kodeDariAI == 'TIDAK_COCOK') {
                return response()->json(['success' => false, 'message' => 'AI tidak dapat menemukan kode yang cocok.']);
            }

            $pecahKode = explode('.', $kodeDariAI);
            if (count($pecahKode) !== 5) {
                 return response()->json(['success' => false, 'message' => 'AI memberikan format kode yang tidak valid.']);
            }

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan teknis saat menghubungi layanan AI.']);
        }

        try {
            $subSubKelompok = AsetSubSubKelompok::where('kode_sub_sub_kelompok', $pecahKode[4])
                ->whereHas('subKelompok', fn($q) => $q->where('kode_sub_kelompok', $pecahKode[3])
                    ->whereHas('kelompok', fn($q2) => $q2->where('kode_kelompok', $pecahKode[2])
                        ->whereHas('bidang', fn($q3) => $q3->where('kode_bidang', $pecahKode[1])
                            ->whereHas('golongan', fn($q4) => $q4->where('kode_golongan', $pecahKode[0]))
                        )
                    )
                )->firstOrFail();

            return response()->json([
                'success' => true,
                'id' => $subSubKelompok->id,
                'kode_lengkap' => $kodeDariAI,
                'nama_lengkap' => $subSubKelompok->nama_sub_sub_kelompok,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Kode dari AI valid, namun tidak ditemukan di database.']);
        }
    }
}
