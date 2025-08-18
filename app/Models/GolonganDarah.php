<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GolonganDarah extends Model
{
    protected $table = 'golongan_darahs';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
