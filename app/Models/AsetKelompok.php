<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetKelompok extends Model
{
    protected $table = 'aset_kelompoks';
    public function bidang() { return $this->belongsTo(AsetBidang::class, 'aset_bidang_id'); }
public function subKelompoks() { return $this->hasMany(AsetSubKelompok::class); }
}
