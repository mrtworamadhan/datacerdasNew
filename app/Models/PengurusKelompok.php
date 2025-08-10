<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengurusKelompok extends Model
{
    use HasFactory;

    protected $table = 'pengurus_kelompoks'; // Pastikan nama tabel benar

    protected $fillable = [
        'kelompok_id',
        'nama_pengurus',
        'jabatan',
    ];

    // Relasi ke Lembaga
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }
}