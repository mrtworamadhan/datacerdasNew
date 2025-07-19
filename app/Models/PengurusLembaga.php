<?php

namespace App\Models;

// PengurusLembaga tidak perlu BelongsToDesa trait secara langsung
// karena dia sudah terhubung ke desa melalui relasi Lembaga
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengurusLembaga extends Model
{
    use HasFactory;

    protected $table = 'pengurus_lembaga'; // Pastikan nama tabel benar

    protected $fillable = [
        'lembaga_id',
        'nama_pengurus',
        'jabatan',
    ];

    // Relasi ke Lembaga
    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }
}