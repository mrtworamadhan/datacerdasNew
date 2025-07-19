<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBantuan extends Model
{
    use HasFactory, BelongsToDesa; // Gunakan trait

    protected $table = 'kategori_bantuans'; // Pastikan nama tabel benar

    protected $fillable = [
        'desa_id',
        'nama_kategori',
        'deskripsi',
        'kriteria_json',
        'allow_multiple_recipients_per_kk',
        'is_active_for_submission',
        'required_additional_fields_json', // Tambahkan ini
    ];

    protected $casts = [
        'kriteria_json' => 'array',
        'allow_multiple_recipients_per_kk' => 'boolean',
        'is_active_for_submission' => 'boolean',
        'required_additional_fields_json' => 'array', // Tambahkan ini
    ];


    // Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke PenerimaBantuan
    public function penerimaBantuans()
    {
        return $this->hasMany(PenerimaBantuan::class);
    }
}