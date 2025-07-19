<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // <-- Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerangkatDesa extends Model
{
    use HasFactory, BelongsToDesa; // <-- Gunakan trait di sini

    protected $fillable = [
        'desa_id', // WAJIB ada di fillable
        'nama',
        'jabatan',
        'user_id', // WAJIB ada di fillable
    ];

    // Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke User (opsional, jika perangkat desa punya akun login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}