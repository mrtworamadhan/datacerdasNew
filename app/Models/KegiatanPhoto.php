<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanPhoto extends Model
{
    protected $fillable = ['kegiatan_id', 'path'];
    public function kegiatan() {
        return $this->belongsTo(Kegiatan::class);
    }
}
