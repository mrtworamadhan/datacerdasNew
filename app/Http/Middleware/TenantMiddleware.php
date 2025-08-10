<?php

namespace App\Http\Middleware;

use App\Models\Desa;
use App\Models\Warga; 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL; // <-- 1. Tambahkan use statement ini
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Builder; // Impor Builder


class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            $desa = Desa::where('subdomain', $subdomain)->first();

            if ($desa) {
                // Simpan instance desa sebagai "tenant"
                app()->instance('tenant', $desa);

                // =================================================================
                // === INI "JURUS PAMUNGKAS"-NYA: Atur parameter default ===
                // =================================================================
                URL::defaults(['subdomain' => $desa->subdomain]);
                // =================================================================
                Warga::addGlobalScope('tenant', function (Builder $builder) use ($desa) {
                    $builder->where('wargas.desa_id', $desa->id);
                });

            } else {
                abort(404, 'Desa tidak ditemukan.');
            }
        }
        
        return $next($request);
    }
}