<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToDesa
{
    protected static function bootBelongsToDesa()
    {
        // Bagian 'creating' (otomatis isi foreign key) tidak berubah, sudah bagus.
        static::creating(function ($model) {
            if (Auth::check() && !Auth::user()->hasRole('superadmin')) {
                $user = Auth::user();
                if (in_array('desa_id', $model->getFillable())) {
                    $model->desa_id = $model->desa_id ?? $user->desa_id;
                }
                if ($user->rw_id && in_array('rw_id', $model->getFillable())) {
                    $model->rw_id = $model->rw_id ?? $user->rw_id;
                }
                if ($user->rt_id && in_array('rt_id', $model->getFillable())) {
                    $model->rt_id = $model->rt_id ?? $user->rt_id;
                }
            }
        });

        // --- GLOBAL SCOPE BARU YANG "CERDAS" ---
        static::addGlobalScope('desa_id_and_area', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                $model = $builder->getModel();

                // 1. Selalu terapkan filter desa_id untuk semua user tenant
                if ($user->desa_id && !$user->hasRole('superadmin')) {
                    $builder->where($model->getTable() . '.desa_id', $user->desa_id);
                }

                // 2. Daftar model yang memiliki data spesifik per wilayah (punya kolom rw_id/rt_id)
                $areaSpecificModels = [
                    \App\Models\Warga::class,
                    \App\Models\Fasum::class,
                    \App\Models\KartuKeluarga::class,
                    \App\Models\Posyandu::class,
                    // Tambahkan model lain di sini jika punya kolom rw_id/rt_id
                ];

                // 3. Cek apakah model saat ini ada di dalam daftar "spesifik per wilayah"
                $isAreaSpecific = in_array(get_class($model), $areaSpecificModels);

                // 4. Terapkan filter area HANYA JIKA modelnya ada di dalam daftar
                if ($isAreaSpecific) {
                    if ($user->hasRole('admin_rw') && $user->rw_id) {
                        $builder->where($model->getTable() . '.rw_id', $user->rw_id);
                    }
                    if ($user->hasRole('admin_rt') && $user->rt_id) {
                        $builder->where($model->getTable() . '.rt_id', $user->rt_id);
                    }
                }
            }
        });
    }
}