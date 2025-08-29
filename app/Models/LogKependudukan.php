<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogKependudukan extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'desa_id',
        'warga_id',
        'jenis_peristiwa',
        'tanggal_peristiwa',
        'keterangan',
        'dicatat_oleh_user_id',
    ];

    /**
     * Mendapatkan user yang mencatat log ini.
     */
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }

    /**
     * Mendapatkan data warga yang terkait dengan log ini.
     */
    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }
}