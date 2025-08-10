<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetGolongan extends Model
{
    protected $table = 'aset_golongans';
    public function bidangs() { return $this->hasMany(AsetBidang::class); }
}
