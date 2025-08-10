<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory, BelongsToDesa;
    protected $fillable = ['desa_id', 'klasifikasi_surat_id', 'nama_surat', 'judul_surat', 'isi_template', 'persyaratan', 'custom_fields', 'is_mandiri'];

    // PERUBAHAN: Tambahkan casting untuk kolom JSON
    protected $casts = [
        'persyaratan' => 'array',
        'custom_fields' => 'array',
        
    ];

    public function klasifikasi() {
        return $this->belongsTo(KlasifikasiSurat::class, 'klasifikasi_surat_id');
    }
}
