<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BelongsToDesa
{
    /**
     * The "booted" method of the trait.
     *
     * @return void
     */
    protected static function bootBelongsToDesa()
    {
        static::addGlobalScope('desa_id_and_area', function (Builder $builder) {
            // Ambil nama tabel dari model yang sedang di-query
            $tableName = $builder->getModel()->getTable(); // DIPINDAHKAN KE SINI

            // Jika ada user yang login, terapkan filter sesuai peran
            if (Auth::check()) {
                $user = Auth::user();

                // 1. Super Admin: Tidak perlu scope, bisa lihat semua
                if ($user->user_type === 'super_admin') {
                    return; // Jangan terapkan scope untuk super admin
                }

                // Mulai dengan filter desa_id (untuk semua user non-Super Admin)
                if ($user->desa_id) {
                    $builder->where($tableName . '.desa_id', $user->desa_id); // Kualifikasi kolom
                } else {
                    // Jika user non-Super Admin tidak punya desa_id (seharusnya tidak terjadi), tampilkan kosong
                    $builder->where($tableName . '.id', null); // Kualifikasi kolom
                    return; // Hentikan penerapan scope lebih lanjut
                }

                // 2. Admin RW: Tambah filter rw_id, HANYA JIKA tabel memiliki kolom 'rw_id'
                if ($user->user_type === 'admin_rw') {
                    if ($user->rw_id && Schema::hasColumn($tableName, 'rw_id')) {
                        $builder->where($tableName . '.rw_id', $user->rw_id); // Kualifikasi kolom
                    } else if (Schema::hasColumn($tableName, 'rw_id')) { // Jika kolom ada tapi user.rw_id null
                        $builder->where($tableName . '.id', null); // Tampilkan kosong
                    }
                    return; // Hentikan penerapan scope lebih lanjut
                }

                // 3. Admin RT: Tambah filter rt_id, HANYA JIKA tabel memiliki kolom 'rt_id'
                if ($user->user_type === 'admin_rt') {
                    // Pastikan juga filter RW sudah diterapkan jika ada kolomnya
                    if (Schema::hasColumn($tableName, 'rw_id') && $user->rw_id) { // Cek kolom sebelum pakai $user->rw_id
                        $builder->where($tableName . '.rw_id', $user->rw_id); // Kualifikasi kolom
                    }

                    if ($user->rt_id && Schema::hasColumn($tableName, 'rt_id')) {
                        $builder->where($tableName . '.rt_id', $user->rt_id); // Kualifikasi kolom
                    } else if (Schema::hasColumn($tableName, 'rt_id')) { // Jika kolom ada tapi user.rt_id null
                        $builder->where($tableName . '.id', null); // Kualifikasi kolom
                    }
                    return; // Hentikan penerapan scope lebih lanjut
                }

                // 4. Kader Posyandu: Tambah filter rw_id dan rt_id jika ada dan kolomnya ada
                if ($user->user_type === 'kader_posyandu') {
                    if ($user->rt_id && Schema::hasColumn($tableName, 'rt_id')) {
                        $builder->where($tableName . '.rt_id', $user->rt_id); // Kualifikasi kolom
                    } elseif ($user->rw_id && Schema::hasColumn($tableName, 'rw_id')) {
                        $builder->where($tableName . '.rw_id', $user->rw_id); // Kualifikasi kolom
                    }
                    // Jika tidak ada RW/RT ID, hanya filter berdasarkan desa_id (yang sudah diterapkan di awal)
                }

            } else {
                
                if (Schema::hasColumn($tableName, 'desa_id')) {
                    // Jika model punya desa_id, tapi tidak ada user login, kita bisa default ke null
                    // atau biarkan PublicController@indexPublic yang memfilter desa_id
                    // Untuk saat ini, biarkan saja tanpa filter tambahan jika tidak login.
                }
            }
        });

        // Otomatis mengisi desa_id, rw_id, rt_id saat membuat data baru
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->user_type !== 'super_admin') {
                    $model->desa_id = $user->desa_id;
                    // Hanya isi rw_id/rt_id jika model memiliki kolom tersebut dan user memiliki rw_id/rt_id
                    if (Schema::hasColumn($model->getTable(), 'rw_id') && $user->rw_id) {
                        $model->rw_id = $user->rw_id;
                    }
                    if (Schema::hasColumn($model->getTable(), 'rt_id') && $user->rt_id) {
                        $model->rt_id = $user->rt_id;
                    }
                }
                // Jika super admin yang membuat data, desa_id/rw_id/rt_id harus diisi manual di form
            }
        });
    }

    // Opsional: Method untuk nonaktifkan scope sementara
    public static function withoutAreaScope()
    {
        return static::withoutGlobalScope('desa_id_and_area');
    }
}
