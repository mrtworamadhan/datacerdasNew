<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory, BelongsToDesa;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Kita gunakan $guarded agar lebih mudah, mengizinkan semua kolom diisi
    // kecuali yang kita tentukan. Array kosong berarti semua bisa diisi.
    protected $guarded = [];

    /**
     * Mendefinisikan relasi ke AsetSubSubKelompok.
     * Ini adalah hubungan ke "kamus" kodifikasi level terdalam.
     */
    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    public function subSubKelompok()
    {
        return $this->belongsTo(AsetSubSubKelompok::class, 'aset_sub_sub_kelompok_id');
    }
}