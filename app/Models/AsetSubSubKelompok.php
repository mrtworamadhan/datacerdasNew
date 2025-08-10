<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetSubSubKelompok extends Model
{
    public function subKelompok() { return $this->belongsTo(AsetSubKelompok::class, 'aset_sub_kelompok_id'); }
}
