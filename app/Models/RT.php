<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RT extends Model
{
    use HasFactory, BelongsToDesa; // Gunakan trait

    protected $table = 'rts'; // Pastikan nama tabel benar

    protected $fillable = [
        'desa_id',
        'rw_id',
        'nomor_rt',
        'nama_ketua',
    ];

    // Relasi ke RW
    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    // Relasi ke Desa (melalui RW)
    public function desa()
    {
        return $this->belongsTo(Desa::class); // Ini akan bekerja karena ada desa_id langsung
    }

    // Relasi ke User (Admin RT)
    public function adminRt()
    {
        return $this->hasOne(User::class, 'rt_id')->where('user_type', 'admin_rt');
    }

    public function kartuKeluargas()
    {
        return $this->hasMany(KartuKeluarga::class, 'rt_id');
    }

    // Relasi untuk mengambil semua Warga di bawah sebuah RT
    public function wargas()
    {
        return $this->hasMany(Warga::class, 'rt_id');
    }
}