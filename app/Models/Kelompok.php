<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // Tambahkan ini
use App\Models\Desa;
use App\Models\PengurusKelompok;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory, BelongsToDesa; // Gunakan trait

    protected $fillable = [
        'desa_id', // Penting untuk global scope
        'nama_kelompok',
        'deskripsi',
        'sk_kepala_desa_path', // Untuk path file PDF
        'path_kop_surat', // Untuk path file gambar kop surat
    ];

    // Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke PengurusLembaga (satu lembaga punya banyak pengurus)
    public function pengurus()
    {
        return $this->hasMany(PengurusKelompok::class);
    }

    public function kegiatans()
    {
        return $this->morphMany(Kegiatan::class, 'kegiatanable');
    }
}