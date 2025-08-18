<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema; // Tetap diperlukan untuk creating event

trait BelongsToDesa
{
    protected static function bootBelongsToDesa()
    {
        // Global Scope untuk memfilter data saat SELECT
        static::addGlobalScope('desa_id_and_area', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                $tableName = $builder->getModel()->getTable();

                // Bypass untuk Super Admin
                if ($user->hasRole('super_admin')) { // Lebih baik cek pakai role dari spatie
                    return;
                }

                // Terapkan scope dasar untuk semua user non-superadmin
                if ($user->desa_id) {
                    $builder->where($tableName . '.desa_id', $user->desa_id);
                } else {
                    // Jika non-superadmin tapi tidak punya desa_id, blok akses data
                    $builder->whereRaw('1 = 0'); // Cara aman untuk tidak menampilkan hasil
                    return;
                }
                
                // Tambahkan filter spesifik berdasarkan hierarki
                // Admin RT adalah yang paling spesifik, kita cek duluan.
                if ($user->hasRole('admin_rt') && $user->rt_id) {
                    $builder->where($tableName . '.rt_id', $user->rt_id);
                } 
                // Jika bukan admin RT, mungkin dia admin RW.
                elseif (($user->hasRole('admin_rw') || $user->hasRole('kader_posyandu')) && $user->rw_id) {
                    $builder->where($tableName . '.rw_id', $user->rw_id);
                }
                // Jika tidak cocok, user tersebut (misal: admin desa) hanya difilter berdasarkan desa_id.
            }
        });

        // Otomatis mengisi foreign key saat INSERT
        static::creating(function ($model) {
            if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
                $user = Auth::user();
                
                // Selalu isi desa_id
                $model->desa_id = $user->desa_id;

                // Cek kolom sebelum mengisi, ini tidak apa-apa karena hanya terjadi sekali saat INSERT
                if ($user->rw_id && Schema::hasColumn($model->getTable(), 'rw_id')) {
                    $model->rw_id = $user->rw_id;
                }
                if ($user->rt_id && Schema::hasColumn($model->getTable(), 'rt_id')) {
                    $model->rt_id = $user->rt_id;
                }
            }
        });
    }
}