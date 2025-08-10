<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // <-- Tambahkan ini
use Illuminate\Http\Client\ConnectionException; // <-- Tambahkan ini

class AiController extends Controller
{
    public function generateReportText(Request $request, string $subdomain,)
    {
        try {
            // --- PERBAIKAN UTAMA: VALIDASI DINAMIS ---
            $section = $request->input('section');

            // Aturan validasi dasar yang berlaku untuk semua
            $baseRules = [
                'context' => 'required|string',
                'section' => 'required|string',
                'penyelenggara_nama' => 'required|string',
                'nama_desa' => 'required|string',
            ];

            $additionalRules = [];
            // Tentukan aturan tambahan berdasarkan section
            if (str_contains(strtolower($section), 'lpj') || str_contains(strtolower($section), 'hasil') || str_contains(strtolower($section), 'evaluasi') || str_contains(strtolower($section), 'rekomendasi')) {
                // Untuk semua section LPJ (e.g., 'Latar Belakang LPJ', 'Hasil Kegiatan')
                $additionalRules = [
                    'original_text' => 'nullable|string',
                ];
            } else {
                // Untuk semua section Proposal (default)
                $additionalRules = [
                    'lokasi_kegiatan' => 'required|string',
                    'anggaran' => 'nullable|string',
                    'tanggal' => 'required|string',
                ];
            }
            
            // Gabungkan dan jalankan validasi
            $validated = $request->validate(array_merge($baseRules, $additionalRules));
            
            $context = $validated['context'];
            $namaPenyelenggara = $validated['penyelenggara_nama'];
            $namaDesa = $validated['nama_desa'];
            $lokasiKegiatan = $validated['lokasi_kegiatan'] ?? 'tidak disebutkan';
            $anggaran = $validated['anggaran'] ?? 'tidak ditentukan';
            $tanggal = $validated['tanggal'] ?? 'tidak ditentukan';
            $originalText = $validated['original_text'] ?? '';
            $prompt = "";

            // PERUBAHAN: Prompt yang lebih spesifik
            switch ($section) {
                case 'Latar Belakang':
                    $prompt = "Buatkan narasi untuk bagian 'Latar Belakang' dari sebuah proposal pertanggungjawaban bernama '{$context}'. 
                    minimal 1 halaman ukuran 1 4 dengan 5 paragraf, 
                    dengan menyebutkan di dalamnya nama desa '{$namaDesa}' kegiatan ini dilaksanakan oleh '{$namaPenyelenggara}'. 
                    buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.";
                    break;
                case 'Latar Belakang LPJ':
                    $originalText = $request->input('original_text');
                    $prompt = "Anda adalah seorang penulis laporan profesional. Tugas Anda adalah menulis ulang (rewrite) teks 
                    'Latar Belakang' dari sebuah proposal menjadi narasi untuk Laporan Pertanggungjawaban (LPJ). 
                    Ubah bahasanya menjadi bentuk lampau (past tense) dan fokus pada apa yang telah melatarbelakangi terlaksananya kegiatan. 
                    Jangan gunakan markdown.\n\nTEKS PROPOSAL ASLI:\n\"{$originalText}\"\n\nTEKS LPJ HASIL REWRITE:";
                    break;
                case 'Tujuan Kegiatan':
                    $prompt = "Buatkan poin pin untuk bagian 'Tujuan Kegiatan' dari sebuah proposal kegiatan bernama '{$context}'. 
                    Hasilnya harus dalam format daftar bernomor (numbered list) yang jelas dan ringkas. 
                    buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                case 'Deskripsi Kegiatan':
                    $prompt = "Buatkan narasi deskripsi yang panjang dan detail untuk bagian 'Deskripsi Lengkap Kegiatan' dari sebuah proposal 
                    bernama '{$context}'. yang dilaksanakan oleh '{$namaPenyelenggara}' desa '{$namaDesa}' berlokasi di '{$lokasiKegiatan}', pada tanggal '{$tanggal}'. 
                    Jelaskan kemungkinan urutan acara dari awal hingga akhir. 
                    buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                case 'Rincian Anggaran':
                    $prompt = "Buatkan Rencana Anggaran Biaya dalam bentuk tabel dalam sebuah proposal kegiatan. 
                    Nama kegiatannya adalah '{$context}'. dengan jumlah anggaran '{$anggaran}'. 
                    Hasilnya berupa rincian dengan nomor urut, keterangan, satuan, harga, jumlah contoh: 1. Pembuatan Banner, 25.000, 3m, 75.000.
                    jangan gunakan tabel, gunakan (numbered list). berikan respon tanpa kata kata pembuka dan penutup dari mu. jangan gunakan font bold. gunakan font resmi saja.
                    ";
                    break;
                case 'Hasil Kegiatan':
                    $originalText = $request->input('original_text');
                    $prompt = "Anda adalah seorang penulis laporan profesional. Tugas Anda adalah menulis ulang (rewrite) teks 
                    'Deskripsi Kegiatan' dari sebuah proposal menjadi narasi untuk Laporan Pertanggungjawaban (LPJ). 
                    Ubah bahasanya menjadi bentuk lampau (past tense) dan fokus pada apa yang telah dilakukan pada kegiatan ini. 
                    Jangan gunakan markdown.\n\nTEKS PROPOSAL ASLI:\n\"{$originalText}\"\n\nTEKS LPJ HASIL REWRITE. buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                case 'Evaluasi Kendala':
                    $originalText = $request->input('original_text');
                    $prompt = "Anda adalah seorang penulis laporan profesional. Tugas anda adalah menulis, Evaluasi dari kegiatan yang telah dilaksanakan,
                    dengan memperhatikan tujuan kegiatan berikut {$originalText}, dan deskripsi kegiatan yang dilaksanakan {$originalText}.
                    Jangan gunakan markdown. buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                case 'Rekomendasi Kegiatan':
                    $prompt = "Anda adalah seorang penulis laporan profesional. Tugas anda adalah menulis 2 paragraph, paragraph pertama adalah Rekomendasi atau saran dari kegiatan yang telah dilaksanakan,
                    kegiatan ini bernama '{$context}'. dan paragraph kedua adalah penutup dari sebuah Laporan Pertanggung Jawaban dari kegiatan tersebut. Jangan gunakan markdown.
                    buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                default:
                    $prompt = "Buatkan narasi untuk bagian '{$section}' dalam sebuah proposal kegiatan. Nama kegiatannya adalah '{$context}', 
                    diselenggarakan oleh {$namaPenyelenggara} di Desa {$namaDesa}. Gunakan bahasa Indonesia yang formal, baku, dan jelas. tidak usah ada tambahan salam dadn tanda tangan,
                    cukup hanya paragraf penutup.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                    
            }
            $apiKey = config('services.gemini.api_key');
            if (!$apiKey) {
                // Jika API Key tidak ada, kembalikan error yang jelas
                return response()->json(['message' => 'API Key untuk layanan AI belum diatur.'], 500);
            }
            $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ]
                ]
            ];

            $response = Http::withOptions(['verify' => false])->timeout(60)->post($apiUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();
                $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, terjadi kesalahan saat menghasilkan teks. Coba lagi.';
                return response()->json(['text' => $generatedText]);
            } else {
                // Log error dari API ke laravel.log
                Log::error('AI API Error: ' . $response->body());
                return response()->json(['message' => 'Layanan AI merespons dengan error. Silakan coba lagi nanti.'], 500);
            }

        } catch (ConnectionException $e) {
            // Log error koneksi (seperti SSL atau timeout) ke laravel.log
            Log::error('AI Connection Error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal terhubung ke layanan AI. Periksa konfigurasi SSL dan koneksi internet Anda.'], 500);
        } catch (\Exception $e) {
            // Log error umum lainnya ke laravel.log
            Log::error('General AI Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan umum pada sistem. Silakan hubungi administrator.'], 500);
        }
    }
}