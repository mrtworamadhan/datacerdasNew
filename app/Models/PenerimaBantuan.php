<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaBantuan extends Model
{
    use HasFactory, BelongsToDesa; // Gunakan trait

    protected $table = 'penerima_bantuans'; // Pastikan nama tabel benar

    protected $fillable = [
        'desa_id',
        'kategori_bantuan_id',
        'warga_id',
        'kartu_keluarga_id',
        'tanggal_menerima',
        'keterangan',
        'status_permohonan', // Tambahkan ini
        'diajukan_oleh_user_id', // Tambahkan ini
        'disetujui_oleh_user_id', // Tambahkan ini
        'tanggal_verifikasi', // Tambahkan ini
        'catatan_persetujuan_penolakan', // Tambahkan ini
    ];

    protected $casts = [
        'tanggal_menerima' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'status_permohonan' => 'string',
    ];

    // Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke KategoriBantuan
    public function kategoriBantuan()
    {
        return $this->belongsTo(KategoriBantuan::class);
    }

    // Relasi ke Warga (jika penerima adalah individu)
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    // Relasi ke KartuKeluarga (jika penerima adalah KK)
    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class);
    }

    // Relasi ke User yang mengajukan
    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh_user_id');
    }

    // Relasi ke User yang menyetujui/menolak
    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh_user_id');
    }

    public function photos()
    {
        return $this->hasMany(PenerimaBantuanPhoto::class);
    }
}