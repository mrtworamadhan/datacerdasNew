<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanAnak extends Model
{
    use HasFactory;
    protected $fillable = [
        'data_kesehatan_anak_id', 'tanggal_pemeriksaan', 'usia_saat_periksa', 'berat_badan',
        'tinggi_badan', 'status_gizi', 'imunisasi_diterima', 'vitamin_a_diterima',
        'obat_cacing_diterima', 'catatan_kader'
    ];
    protected $casts = ['tanggal_pemeriksaan' => 'date'];

    public function dataAnak() { return $this->belongsTo(DataKesehatanAnak::class, 'data_kesehatan_anak_id'); }
}


