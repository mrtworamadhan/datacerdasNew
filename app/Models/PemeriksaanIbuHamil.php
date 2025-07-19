<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanIbuHamil extends Model
{
    use HasFactory;
    protected $fillable = [
        'data_ibu_hamil_id', 'tanggal_pemeriksaan', 'berat_badan', 'tinggi_badan',
        'tensi_darah', 'hb', 'pemberian_fe', 'catatan_kader'
    ];
    protected $casts = ['tanggal_pemeriksaan' => 'date'];

    public function dataIbuHamil() { return $this->belongsTo(DataIbuHamil::class, 'data_ibu_hamil_id'); }
}
