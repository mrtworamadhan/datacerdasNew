<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKependudukan extends Model
{
    protected $table = 'status_kependudukans';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
