<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // <-- Tambahkan ini
use Illuminate\Http\Client\ConnectionException; // <-- Tambahkan ini

class AiController extends Controller
{
    public function generateReportText(Request $request)
    {
        try {
            $validated = $request->validate([
            'context' => 'required|string',
            'section' => 'required|string',
            'nama_lembaga' => 'required|string',
            'nama_desa' => 'required|string',
            'lokasi_kegiatan' =>'required|string',
            'anggaran' =>'required|string',
            'tanggal' =>'required|string',
        ]);

            $context = $validated['context'];
            $section = $validated['section'];
            $namaLembaga = $validated['nama_lembaga'];
            $namaDesa = $validated['nama_desa'];
            $lokasiKegiatan = $validated['lokasi_kegiatan'];
            $anggaran = $validated['anggaran'];
            $tanggal = $validated['tanggal'];
            $prompt = "";

            // PERUBAHAN: Prompt yang lebih spesifik
            switch ($section) {
                case 'Latar Belakang':
                    $prompt = "Buatkan narasi untuk bagian 'Latar Belakang' dari sebuah laporan pertanggungjawaban bernama '{$context}'. 
                    minimal 1 halaman ukuran 1 4 dengan 5 paragraf, 
                    dengan menyebutkan di dalamnya nama desa '{$namaDesa}' kegiatan ini dilaksanakan oleh '{$namaLembaga}'. 
                    buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.";
                    break;
                case 'Tujuan Kegiatan':
                    $prompt = "Buatkan poin pin untuk bagian 'Tujuan Kegiatan' dari sebuah laporan kegiatan bernama '{$context}'. 
                    Hasilnya harus dalam format daftar bernomor (numbered list) yang jelas dan ringkas. 
                    buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                case 'Deskripsi Kegiatan':
                    $prompt = "Buatkan narasi deskripsi yang panjang dan detail untuk bagian 'Deskripsi Lengkap Kegiatan' dari sebuah laporan pertanggungjawaban 
                    bernama '{$context}'. yang dilaksanakan oleh '{$namaLembaga}' desa '{$namaDesa}' berlokasi di '{$lokasiKegiatan}' 
                    Jelaskan kemungkinan urutan acara dari awal hingga akhir. 
                    buatkan tanpa membuat format bold dan lain sebagainya, gunakan bahasa resmi dan text resmi.berikan respon tanpa kata kata pembuka dan penutup dari mu.";
                    break;
                case 'Rincian Anggaran':
                    $prompt = "Buatkan laporan keuangan dalam bentuk tabel dalam sebuah laporan pertanggungjawaban kegiatan. 
                    Nama kegiatannya adalah '{$context}'. dengan jumlah anggaran '{$anggaran}' dengan saldo 0. 
                    Hasilnya berupa rincian dengan nomor urut, keterangan, satuan, harga, jumlah contoh: 1. Pembuatan Banner, 25.000, 3m, 75.000.
                    jangan gunakan tabel, gunakan (numbered list). berikan respon tanpa kata kata pembuka dan penutup dari mu. jangan gunakan font bold. gunakan font resmi saja.
                    acara berlangsung pada '{$tanggal}'";
                    break;
                default:
                    $prompt = "Buatkan narasi untuk bagian '{$section}' dalam sebuah laporan pertanggungjawaban kegiatan. Nama kegiatannya adalah '{$context}', 
                    diselenggarakan oleh {$namaLembaga} di Desa {$namaDesa}. Gunakan bahasa Indonesia yang formal, baku, dan jelas. tidak usah ada tambahan salam dadn tanda tangan,
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