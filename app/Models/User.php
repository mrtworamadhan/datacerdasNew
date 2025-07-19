<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type', // Tambahkan ini
        'desa_id',
        'rw_id', // Tambahkan ini
        'rt_id',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke RW
    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    // Relasi ke RT
    public function rt()
    {
        return $this->belongsTo(RT::class);
    }

    // Contoh method untuk cek role (nanti bisa kita kembangkan)
    public function isSuperAdmin()
    {
        return $this->user_type === 'super_admin';
    }

    public function isAdminDesa()
    {
        return $this->user_type === 'admin_desa';
    }

    public function isAdminRw()
    {
        return $this->user_type === 'admin_rw';
    }

    public function isAdminRt()
    {
        return $this->user_type === 'admin_rt';
    }

    public function isKaderPosyandu()
    {
        return $this->user_type === 'kader_posyandu';
    }
}
