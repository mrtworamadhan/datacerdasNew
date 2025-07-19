<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // Tambahkan ini
use App\Models\Desa;
use App\Models\PengurusLembaga;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembaga extends Model
{
    use HasFactory, BelongsToDesa; // Gunakan trait

    protected $fillable = [
        'desa_id', // Penting untuk global scope
        'nama_lembaga',
        'deskripsi',
        'sk_kepala_desa_path', // Untuk path file PDF
    ];

    // Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke PengurusLembaga (satu lembaga punya banyak pengurus)
    public function pengurus()
    {
        return $this->hasMany(PengurusLembaga::class);
    }

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }
}