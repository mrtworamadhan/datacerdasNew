<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Cek hak akses umum untuk modul ini
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat daftar warga.');
        }

        // Query dasar untuk warga (Global scope akan otomatis memfilter sesuai user)
        $query = Warga::with('kartuKeluarga.kepalaKeluarga', 'rw', 'rt');

        // Terapkan filter pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nik', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('kartuKeluarga', function($q2) use ($searchTerm) {
                      $q2->where('nomor_kk', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $wargas = $query->latest()->paginate(15); // Paginasi 15 data per halaman

        return view('admin_desa.warga.index', compact('wargas'));
    }
    
    public function searchWarga(Request $request)
    {
        $searchTerm = $request->input('q');
        $user = Auth::user();

        if (!$searchTerm) {
            return response()->json(['results' => []]);
        }

        // Mulai query dasar
        $query = Warga::query();

        // Terapkan filter berdasarkan peran pengguna
        if ($user->user_type === 'admin_rt' && $user->rt_id) {
            $query->where('rt_id', $user->rt_id);
        } elseif ($user->user_type === 'admin_rw' && $user->rw_id) {
            $query->where('rw_id', $user->rw_id);
        }
        // Untuk admin_desa, tidak perlu filter tambahan karena sudah dihandle Global Scope

        // Lanjutkan dengan query pencarian NIK atau Nama
        $wargas = $query->where(function($q) use ($searchTerm) {
            $q->where('nama_lengkap', 'LIKE', "%{$searchTerm}%")
              ->orWhere('nik', 'LIKE', "%{$searchTerm}%");
        })
        ->with(['kartuKeluarga', 'rt', 'rw']) // Eager load untuk performa
        ->limit(10)
        ->get();

        // Format data agar sesuai dengan yang dibutuhkan Select2
        $formattedResults = $wargas->map(function ($warga) {
            return [
                'id' => $warga->id,
                'text' => sprintf(
                    '%s (NIK: %s) - RW %s / RT %s',
                    $warga->nama_lengkap,
                    $warga->nik,
                    $warga->rw->nomor_rw ?? '-',
                    $warga->rt->nomor_rt ?? '-'
                )
            ];
        });

        return response()->json(['results' => $formattedResults]);
    }
}