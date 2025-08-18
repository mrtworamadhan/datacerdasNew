<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubunganKeluarga extends Model
{
    protected $table = 'hubungan_keluargas';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
