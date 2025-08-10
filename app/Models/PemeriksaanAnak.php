<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}


