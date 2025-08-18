<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPerkawinan extends Model
{
    protected $table = 'status_perkawinans';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
