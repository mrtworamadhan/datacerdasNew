<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaBantuanPhoto extends Model
{
    use HasFactory;

    protected $table = 'penerima_bantuan_photos';

    protected $fillable = [
        'penerima_bantuan_id',
        'photo_name',
        'file_path',
    ];

    // Relasi ke PenerimaBantuan
    public function penerimaBantuan()
    {
        return $this->belongsTo(PenerimaBantuan::class);
    }

    public function penerimaBantuanPhotos()
    {
        return $this->hasManyThrough(PenerimaBantuanPhoto::class, PenerimaBantuan::class);
    }
}
