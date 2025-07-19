<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Fasum extends Model
{
    use HasFactory, BelongsToDesa;

    protected $table = 'fasums';

    protected $fillable = [
        'desa_id',
        'rw_id',
        'rt_id',
        'kategori', 
        'nama_fasum',
        'deskripsi',
        'status_kondisi', 
        'latitude',
        'longitude',
        'panjang', 
        'lebar',   
        'alamat_lengkap', 
        'luas_area', 
        'kapasitas', 
        'kontak_pengelola', 
        'status_kepemilikan', 
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
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

    // Relasi ke FasumPhoto
    public function photos()
    {
        return $this->hasMany(FasumPhoto::class);
    }
}
