<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class RW extends Model
{
    use HasFactory, BelongsToDesa;

    protected $table = 'rws';

    protected $fillable = [
        'desa_id',
        'nomor_rw',
        'nama_ketua',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke RTs (sudah ada)
    public function rts()
    {
        return $this->hasMany(RT::class, 'rw_id');
    }

    // Relasi ke Fasums (sudah ada)
    public function fasums()
    {
        return $this->hasMany(Fasum::class, 'rw_id');
    }

    // Relasi ke Admin RW (sudah ada)
    public function adminRw()
    {
        return $this->hasOne(User::class, 'rw_id')->where('user_type', 'admin_rw');
    }

    // Relasi BARU: Users yang terhubung dengan RW ini (termasuk kader)
    public function users()
    {
        return $this->hasMany(User::class, 'rw_id');
    }

    public function posyandu()
    {
        return $this->hasMany(Posyandu::class, 'rw_id');
    }

    // Relasi Kartu Keluarga (sudah ada)
    public function kartuKeluargas()
    {
        return $this->hasManyThrough(
            KartuKeluarga::class,
            RT::class,
            'rw_id', // Foreign key di tabel rts
            'rt_id', // Foreign key di tabel kartu_keluargas
            'id',    // Local key di tabel rws
            'id'     // Local key di tabel rts
        );
    }

    // Relasi Warga (sudah ada)
    public function wargas()
    {
        return $this->hasManyThrough(
            Warga::class,
            RT::class,
            'rw_id', // Foreign key di tabel rts
            'rt_id', // Foreign key di tabel wargas
            'id',    // Local key di tabel rws
            'id'     // Local key di tabel rts
        );
    }
}
