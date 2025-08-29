<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKependudukan extends Model
{
    protected $table = 'status_kependudukans';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public const WARGA_ASLI = 1;
    public const PENDATANG  = 2;
    public const SEMENTARA = 3;
    public const PINDAH     = 4;
    public const MENINGGAL  = 5;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
