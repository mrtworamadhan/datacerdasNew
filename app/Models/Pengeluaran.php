<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'pengeluarans';

    /**
     * Atribut yang boleh diisi secara massal.
     * Kita gunakan $guarded agar lebih mudah.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_transaksi' => 'date',
        'tanggal_pesanan' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Mendefinisikan relasi ke Kegiatan.
     * Setiap pengeluaran "dimiliki oleh" satu kegiatan.
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function detailBarangs()
    {
        return $this->hasMany(DetailBarang::class, 'pengeluaran_id');
    }
}