<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PemeriksaanAnak extends Model
{
    use HasFactory;
    protected $fillable = [
        'data_kesehatan_anak_id', 
        'tanggal_pemeriksaan', 
        'usia_saat_periksa', 
        'berat_badan',
        'tinggi_badan',
        'imunisasi_diterima', 
        'vitamin_a_diterima', 
        'posyandu_id',
        'obat_cacing_diterima', 
        'catatan_kader',
        'zscore_tb_u',
        'status_stunting',
        'zscore_bb_u',
        'status_underweight',
        'zscore_bb_tb',
        'status_wasting',
        'lila',
        'diare_2_minggu',
        'ispa_2_minggu',
        'dapat_vitamin_a',
        'dapat_obat_cacing',
        'dapat_imunisasi_polio',
        'petugas_pengukur',
        'keterangan_pemeriksaan'
    ];
    protected $casts = ['tanggal_pemeriksaan' => 'date'];

    public function dataAnak() 
    { 
        return $this->belongsTo(DataKesehatanAnak::class, 'data_kesehatan_anak_id'); 
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'posyandu_id');
    }

    public function warga()
    {
        return $this->hasOneThrough(
            Warga::class, // Model tujuan akhir
            DataKesehatanAnak::class, // Model perantara
            'id', // Foreign key di tabel perantara (data_kesehatan_anaks)
            'id', // Foreign key di tabel tujuan (wargas)
            'data_kesehatan_anak_id', // Local key di tabel ini (pemeriksaan_anaks)
            'warga_id' // Local key di tabel perantara (data_kesehatan_anaks)
        );
    }

    public function getUsiaFormattedAttribute(): string
    {
        $totalHari = $this->usia_saat_periksa;

        if (is_null($totalHari)) {
            return 'N/A';
        }

        // Konversi total hari ke periode waktu (tahun, bulan, hari)
        $interval = Carbon::now()->addDays($totalHari)->diffAsCarbonInterval(Carbon::now());
        
        $bagian = [];
        if ($interval->y > 0) {
            $bagian[] = $interval->y . ' tahun';
        }
        if ($interval->m > 0) {
            $bagian[] = $interval->m . ' bulan';
        }
        if ($interval->d > 0) {
            $bagian[] = $interval->d . ' hari';
        }
        
        return implode(', ', $bagian) ?: '0 hari';
    }

    public function getRingkasanStatusGiziAttribute(): string
    {
        $abnormalStatuses = [];

        // Cek Stunting: jika statusnya BUKAN 'Normal'
        if ($this->status_stunting && !str_contains(strtolower($this->status_stunting), 'normal')) {
            // Ambil kata pertama saja agar ringkas (misal: "Pendek" dari "Pendek (Stunting)")
            $abnormalStatuses[] = explode(' ', $this->status_stunting)[0];
        }

        // Cek Wasting: jika statusnya BUKAN 'Gizi Baik'
        if ($this->status_wasting && !str_contains(strtolower($this->status_wasting), 'baik')) {
            $abnormalStatuses[] = $this->status_wasting;
        }

        // Cek Underweight: jika statusnya BUKAN 'Berat Badan Normal'
        if ($this->status_underweight && !str_contains(strtolower($this->status_underweight), 'normal')) {
            $abnormalStatuses[] = 'BB ' . explode(' ', $this->status_underweight)[1]; // Ambil kata ketiga, misal: "Kurang"
        }

        // Jika tidak ada yang bermasalah, kembalikan 'Normal'
        if (empty($abnormalStatuses)) {
            return 'Normal';
        }

        // Jika ada, gabungkan semua status bermasalah
        return implode(' / ', $abnormalStatuses);
    }

    /**
     * Accessor untuk menentukan warna badge berdasarkan ringkasan status.
     *
     * @return string
     */
    public function getRingkasanStatusGiziBadgeAttribute(): string
    {
        $summary = strtolower($this->ringkasan_status_gizi); // Panggil accessor di atas

        if ($summary === 'normal') {
            return 'success'; // Hijau
        }

        // Jika ada kata "berat" atau "buruk", warnanya merah
        if (str_contains($summary, 'berat') || str_contains($summary, 'buruk')) {
            return 'danger'; // Merah
        }
        
        // Jika tidak, berarti hanya berisiko (kuning)
        return 'warning';
    }
}


