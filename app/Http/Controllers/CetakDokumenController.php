<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Terbilang;

class CetakDokumenController extends Controller
{
    private function getCommonData(Pengeluaran $pengeluaran)
    {
        $desa = app('tenant');
        $pengeluaran->load('kegiatan.kegiatanable');
        $kegiatan = $pengeluaran->kegiatan;
        $penyelenggara = $kegiatan->kegiatanable;

        // Ambil data bendahara dari pengurus (asumsi jabatannya "Bendahara")
        $bendahara = $penyelenggara->pengurus()->where('jabatan', 'Bendahara')->first();

        return [
            'desa' => $desa,
            'pengeluaran' => $pengeluaran,
            'kegiatan' => $kegiatan,
            'penyelenggara' => $penyelenggara,
            'bendahara' => $bendahara,
        ];
    }
    /**
     * Membuat dan menampilkan PDF untuk Surat Pesanan.
     */
    public function cetakSuratPesanan(string $subdomain, Pengeluaran $pengeluaran)
    {
        // Pastikan pengeluaran ini bertipe 'Pembelian Pesanan'
        if ($pengeluaran->tipe_pengeluaran !== 'Pembelian Pesanan') {
            abort(404, 'Dokumen tidak tersedia untuk tipe pengeluaran ini.');
        }

        $desa = app('tenant');
        // Eager load relasi yang dibutuhkan
        $penyelenggara = $pengeluaran->kegiatan->kegiatanable;

        // Buat kop surat base64
        $kopSuratBase64 = null;
        if (!empty($penyelenggara->path_kop_surat)) {
            $imagePath = storage_path('app/public/' . $penyelenggara->path_kop_surat);
            if (file_exists($imagePath)) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);
                $kopSuratBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    
        $data = [
            'desa' => $desa,
            'pengeluaran' => $pengeluaran,
            'kegiatan' => $pengeluaran->kegiatan,
            'penyelenggara' => $penyelenggara,
            'detailBarangs' => $pengeluaran->detailBarangs,
            'kopSuratBase64' => $kopSuratBase64,
        ];
        // Load view, kirim data, dan buat PDF
        $pdf = Pdf::loadView('admin_desa.laporan.keuangan.surat_pesanan_pdf', $data);
        
        $namaFile = 'Surat Pesanan - ' . $pengeluaran->penyedia . '.pdf';
        return $pdf->stream($namaFile);
    }

    public function cetakKwitansi(string $subdomain, Pengeluaran $pengeluaran)
    {
        // Pastikan tipe pengeluaran sesuai
        if ($pengeluaran->tipe_pengeluaran !== 'Pembelian Pesanan') {
            abort(404, 'Dokumen tidak tersedia untuk tipe pengeluaran ini.');
        }
        Carbon::setLocale('id');

        $desa = app('tenant');
        $kegiatan = $pengeluaran->kegiatan;
        $penyelenggara = $kegiatan->kegiatanable;
        $pengeluaran->load('kegiatan');

        // Mengubah angka menjadi teks (terbilang)
        // Kita akan install library untuk ini nanti
        $terbilang = Terbilang::make($pengeluaran->jumlah);

        $data = $this->getCommonData($pengeluaran);
        $data = [
            'desa' => $desa,
            'pengeluaran' => $pengeluaran,
            'kegiatan' => $pengeluaran->kegiatan,
            'terbilang' => $terbilang,
        ];
        $data['bendahara'] = $penyelenggara->pengurus()->where('jabatan', 'Bendahara')->first();
        $data['ketua'] = $penyelenggara->pengurus()->where('jabatan', 'Ketua')->first();

        $pdf = Pdf::loadView('admin_desa.laporan.keuangan.kwitansi_pdf', $data);

        $namaFile = 'Kwitansi - ' . $pengeluaran->penyedia . '.pdf';
        return $pdf->stream($namaFile);
    }

    public function cetakBeritaAcara(string $subdomain, Pengeluaran $pengeluaran)
    {
        // Pastikan tipe pengeluaran sesuai
        if ($pengeluaran->tipe_pengeluaran !== 'Pembelian Pesanan') {
            abort(404, 'Dokumen tidak tersedia untuk tipe pengeluaran ini.');
        }

        Carbon::setLocale('id');
        $desa = app('tenant');
        $pengeluaran->load('kegiatan', 'detailBarangs');
        
        $penyelenggara = $pengeluaran->kegiatan->kegiatanable;

        // Buat kop surat base64
        $kopSuratBase64 = null;
        if (!empty($penyelenggara->path_kop_surat)) {
            $imagePath = storage_path('app/public/' . $penyelenggara->path_kop_surat);
            if (file_exists($imagePath)) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);
                $kopSuratBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $data = $this->getCommonData($pengeluaran);
        $data = [
            'desa' => $desa,
            'pengeluaran' => $pengeluaran,
            'kegiatan' => $pengeluaran->kegiatan,
            'detailBarangs' => $pengeluaran->detailBarangs,
            'tanggalBeritaAcara' => now(), // Tanggal saat dokumen dicetak
            'terbilangHari' => Terbilang::make(now()->day),
            'terbilangTahun' => Terbilang::make(now()->year),
            'kopSuratBase64' => $kopSuratBase64,
        ];
        $data['penyelenggara'] = $pengeluaran->kegiatan->kegiatanable;
        

        $pdf = Pdf::loadView('admin_desa.laporan.keuangan.berita_acara_penerimaan_pdf', $data);

        $namaFile = 'Berita Acara Penerimaan - ' . $pengeluaran->penyedia . '.pdf';
        return $pdf->stream($namaFile);
    }
}