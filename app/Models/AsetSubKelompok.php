<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetSubKelompok extends Model
{
    public function kelompok() { return $this->belongsTo(AsetKelompok::class, 'aset_kelompok_id'); }
public function subSubKelompoks() { return $this->hasMany(AsetSubSubKelompok::class); }
}
