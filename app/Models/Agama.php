<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $table = 'agamas';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
