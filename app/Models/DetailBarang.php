<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBarang extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'detail_barangs';

    /**
     * Atribut yang boleh diisi secara massal.
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
        'volume' => 'float',
        'harga_satuan' => 'decimal:2',
    ];

    /**
     * Mendefinisikan relasi ke Pengeluaran.
     * Setiap detail barang "dimiliki oleh" satu record pengeluaran.
     */
    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran_id');
    }
}