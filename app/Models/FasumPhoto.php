<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasumPhoto extends Model
{
    use HasFactory;

    protected $table = 'fasum_photos';

    protected $fillable = [
        'fasum_id',
        'path',
    ];

    /**
     * Relasi kembali ke model Fasum utama.
     */
    public function fasum()
    {
        return $this->belongsTo(Fasum::class);
    }
}