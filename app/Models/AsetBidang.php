<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetBidang extends Model
{
    protected $table = 'aset_bidangs';
    
    public function golongan() { return $this->belongsTo(AsetGolongan::class, 'aset_golongan_id'); }
    public function kelompoks() { return $this->hasMany(AsetKelompok::class); }
}
