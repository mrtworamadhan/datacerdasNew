<?php

namespace App\Observers;

use App\Models\Warga;
use App\Models\LogKependudukan;
use App\Models\StatusKependudukan;
use Illuminate\Support\Facades\Auth;

class WargaObserver
{
    /**
     * Menangani event "created" (dibuat) pada model Warga.
     * Mencatat peristiwa 'Lahir', 'Datang', atau 'Data Baru'.
     */
    public function created(Warga $warga): void
    {
        //
    }

    public function updated(Warga $warga): void
    {
        if ($warga->isDirty('status_kependudukan_id')) {
            $idStatusLama = $warga->getOriginal('status_kependudukan_id');
            $statusLama = StatusKependudukan::find($idStatusLama)->nama ?? 'Tidak Diketahui';
            
            // Kita tetap ambil nama status baru untuk deskripsi log
            $status_baru_model = StatusKependudukan::find($warga->status_kependudukan_id);
            $status_baru_nama = $status_baru_model->nama ?? 'Tidak Diketahui';

            $jenisPeristiwa = 'Perubahan Status';
            
            // Bandingkan dengan ID menggunakan konstanta
            if ($warga->status_kependudukan_id == StatusKependudukan::MENINGGAL) {
                $jenisPeristiwa = 'Meninggal';
            } elseif ($warga->status_kependudukan_id == StatusKependudukan::PINDAH) {
                $jenisPeristiwa = 'Pindah';
            }

            $keterangan = "Status kependudukan '{$warga->nama_lengkap}' diubah dari '{$statusLama}' menjadi '{$status_baru_nama}'.";
            $this->catatLog($warga, $jenisPeristiwa, $keterangan);
        }
    }

    /**
     * Helper method untuk membuat entri log agar tidak duplikasi kode.
     */
    private function catatLog(Warga $warga, string $jenisPeristiwa, string $keterangan): void
    {
        LogKependudukan::create([
            'desa_id' => $warga->desa_id,
            'warga_id' => $warga->id,
            'jenis_peristiwa' => $jenisPeristiwa,
            'tanggal_peristiwa' => now()->toDateString(), // Cukup simpan tanggalnya saja
            'keterangan' => $keterangan,
            'dicatat_oleh_user_id' => Auth::id(),
        ]);
    }

    /**
     * Handle the Warga "deleted" event.
     */
    public function deleted(Warga $warga): void
    {
        // Opsional: Jika Anda ingin mencatat penghapusan data
        $keterangan = "Data warga '{$warga->nama_lengkap}' (NIK: {$warga->nik}) telah dihapus dari sistem.";
        $this->catatLog($warga, 'Data Dihapus', $keterangan);
    }
}