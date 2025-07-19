<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory, BelongsToDesa;

    protected $fillable = [
        'desa_id',
        'lembaga_id',
        'nama_kegiatan',
        'tanggal_kegiatan',
        'latar_belakang',
        'tujuan_kegiatan',
        'deskripsi_kegiatan',
        'lokasi_kegiatan',
        'anggaran_biaya',
        'laporan_dana',
        'sumber_dana',
        'penutup',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'anggaran_biaya' => 'decimal:2',
    ];

    /**
     * Relasi ke lembaga yang menyelenggarakan.
     */
    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function photos() {
        return $this->hasMany(KegiatanPhoto::class);
    }
}