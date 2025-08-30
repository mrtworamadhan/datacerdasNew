<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToDesa
{
    protected static function bootBelongsToDesa()
    {
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

        static::addGlobalScope('desa_id_and_area', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                $model = $builder->getModel();

                if ($user->desa_id && !$user->hasRole('superadmin')) {
                    $builder->where($model->getTable() . '.desa_id', $user->desa_id);
                }

                $areaSpecificModels = [
                    \App\Models\Warga::class,
                    \App\Models\Fasum::class,
                    \App\Models\KartuKeluarga::class,
                    \App\Models\Posyandu::class,
                ];

                $isAreaSpecific = in_array(get_class($model), $areaSpecificModels);

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