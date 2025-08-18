<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Posyandu extends Model
{
    use HasFactory, BelongsToDesa;

    /**
     * Mendefinisikan nama tabel secara eksplisit
     */
    protected $table = 'posyandu';

    /**
     * Kolom yang bisa diisi secara massal (mass assignable)
     */
    protected $fillable = [
        'desa_id',
        'nama_posyandu',
        'rw_id',
        'alamat'
    ];

    /**
     * Relasi ke model Rw (Satu Posyandu dimiliki oleh satu RW)
     */
    public function rws(): BelongsTo
    {
        // Asumsi model RW kamu bernama 'Rw'
        return $this->belongsTo(RW::class, 'rw_id');
    }

    /**
     * Relasi ke model Penduduk (Satu posyandu memiliki banyak kader)
     */
    public function kaders(): BelongsToMany
    {
        return $this->belongsToMany(Warga::class, 'kader_posyandu', 'posyandu_id', 'warga_id')
            ->withPivot('id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'posyandu_id');
    }
    public function dataKesehatanAnak()
    {
        return $this->hasMany(DataKesehatanAnak::class, 'posyandu_id');
    }
}