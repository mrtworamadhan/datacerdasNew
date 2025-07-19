<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKesehatanAnak extends Model
{
    use HasFactory;
    protected $fillable = ['warga_id', 'tanggal_lahir', 'bb_lahir', 'tb_lahir', 'nama_ayah', 'nama_ibu'];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function warga() { return $this->belongsTo(Warga::class); }
    public function riwayatPemeriksaan() { return $this->hasMany(PemeriksaanAnak::class); }

    public function latestPemeriksaan()
    {
        return $this->hasOne(PemeriksaanAnak::class)->latestOfMany('tanggal_pemeriksaan');
    }

}



