<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lpj extends Model
{
    use HasFactory;
    protected $table = 'lpjs';
    protected $guarded = [];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}
