<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKhusus extends Model
{
    protected $table = 'status_khususes';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }
}
