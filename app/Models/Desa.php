<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    protected $table = 'desas';

    protected $fillable = [
        'nama_desa',
        'slug',
        'alamat_desa',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'nama_kades',             // Tambahkan ini
        'subscription_status',
        'subscription_ends_at',
        'trial_ends_at',
    ];

    protected $casts = [
        'subscription_ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function rws()
    {
        return $this->hasMany(RW::class);
    }
    public function rts()
    {
        return $this->hasMany(RT::class);
    }

    public function fasums()
    {
        return $this->hasMany(Fasum::class);
    }

    public function kategoriBantuans()
    {
        return $this->hasMany(KategoriBantuan::class);
    }

    public function suratSetting()
    {
        return $this->hasOne(SuratSetting::class);
    }

     public function isSubscriptionActive()
    {
        return $this->subscription_status === 'active' && 
               ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture());
    }

    public function isInTrial()
    {
        return $this->subscription_status === 'trial' && 
               ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function isSubscriptionInactive()
    {
        return $this->subscription_status === 'inactive' || 
               ($this->subscription_status === 'active' && $this->subscription_ends_at !== null && $this->subscription_ends_at->isPast()) ||
               ($this->subscription_status === 'trial' && $this->trial_ends_at !== null && $this->trial_ends_at->isPast()); // PERBAIKAN DI SINI
    }
}
