<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuKeluarga extends Model
{
    use HasFactory, BelongsToDesa; // Gunakan trait

    protected $table = 'kartu_keluargas'; // Pastikan nama tabel benar

    protected $fillable = [
        'desa_id',
        'nomor_kk',
        'rw_id',
        'rt_id',
        'alamat_lengkap',
        'klasifikasi',
        'kepala_keluarga_id',
    ];

    protected $casts = [
        'klasifikasi' => 'string', // Atau 'enum' jika Laravel 10+
    ];

    // Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke RW
    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    // Relasi ke RT
    public function rt()
    {
        return $this->belongsTo(RT::class);
    }

    // Relasi ke Warga (anggota keluarga)
    public function wargas()
    {
        return $this->hasMany(Warga::class, 'kartu_keluarga_id');
    }

    // Relasi ke Kepala Keluarga (satu warga spesifik)
    public function kepalaKeluarga()
    {
        return $this->belongsTo(Warga::class, 'kepala_keluarga_id');
    }
}