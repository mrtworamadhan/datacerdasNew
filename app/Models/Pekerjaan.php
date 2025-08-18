<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pekerjaans';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
