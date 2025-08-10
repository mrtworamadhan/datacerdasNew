<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratSetting extends Model
{
    use HasFactory, BelongsToDesa;
    protected $fillable = [
        'desa_id', 
        'path_kop_surat',
        'path_logo_pemerintah',
        'path_ttd', 
        'penanda_tangan_nama', 
        'penanda_tangan_jabatan'
    ];
}