<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataIbuHamil extends Model
{
    use HasFactory;
    protected $fillable = ['warga_id', 'kehamilan_ke', 'hpht', 'hpl', 'jarak_kehamilan', 'memiliki_bpjs'];
    protected $casts = ['hpht' => 'date', 'hpl' => 'date'];

    public function warga() { return $this->belongsTo(Warga::class); }
    public function riwayatPemeriksaan() { return $this->hasMany(PemeriksaanIbuHamil::class); }
}


