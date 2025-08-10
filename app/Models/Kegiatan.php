<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory, BelongsToDesa;

    /**
     * The attributes that are mass assignable.
     */
     protected $fillable = [
        'desa_id',
        'nama_kegiatan',
        'tipe_kegiatan',
        'tanggal_kegiatan',
        'latar_belakang',
        'tujuan_kegiatan',
        'deskripsi_kegiatan',
        'lokasi_kegiatan',
        'anggaran_biaya',
        'laporan_dana',
        'sumber_dana',
        'penutup',
        'status',
        // Kolom polymorphic ('kegiatanable_id', 'kegiatanable_type')
        // diisi otomatis oleh relasi, jadi tidak perlu ada di sini.
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'anggaran_biaya' => 'decimal:2',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    /**
     * Relasi polymorphic ke Lembaga atau Kelompok.
     */
    public function kegiatanable()
    {
        return $this->morphTo();
    }

    public function photos()
    {
        return $this->hasMany(KegiatanPhoto::class);
    }

    public function lpj()
    {
        return $this->hasOne(Lpj::class);
    }

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'kegiatan_id');
    }
}