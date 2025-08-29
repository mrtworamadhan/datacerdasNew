<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk transaksi database

class UserDirectoryController extends Controller
{
    /**
     * Display a listing of the users with filters for the current desa.
     */
    public function index(Request $request, string $subdomain)
    {
        $user = Auth::user();

        $query = User::where('desa_id', $user->desa_id)
                         ->whereIn('user_type', ['admin_desa', 'admin_rw', 'admin_rt', 'kader_posyandu'])
                         ->with('rw', 'rt');

        // Filter berdasarkan RW
        if ($request->filled('filter_rw_id')) {
            $query->where('rw_id', $request->filter_rw_id);
        }

        // Filter berdasarkan RT
        if ($request->filled('filter_rt_id')) {
            $query->where('rt_id', $request->filter_rt_id);
        }

        // Gunakan paginate() daripada get()
        $users = $query->paginate(25); // Menampilkan 15 item per halaman

        // Ambil daftar RW dan RT untuk filter dropdown (ini tetap get() karena kita butuh semua untuk dropdown)
        $rws = RW::where('desa_id', $user->desa_id)->get();
        $rts = RT::where('desa_id', $user->desa_id)->get();

        return view('admin_desa.user_directory.index', compact('users', 'rws', 'rts'));
    }

    /**
     * Update 'nama_ketua' for multiple RW/RT users.
     */
    public function updateBatch(Request $request, string $subdomain)
    {
        $user = Auth::user();

        $request->validate([
            'nama_ketua_rw.*' => 'nullable|string|max:255', // Validasi array untuk nama ketua RW
            'nama_ketua_rt.*' => 'nullable|string|max:255', // Validasi array untuk nama ketua RT
        ]);

        DB::beginTransaction();
        try {
            // Update Nama Ketua RW
            if ($request->has('nama_ketua_rw')) {
                foreach ($request->nama_ketua_rw as $rwId => $namaKetua) {
                    $rw = RW::where('desa_id', $user->desa_id)->find($rwId);
                    if ($rw) {
                        $rw->update(['nama_ketua' => $namaKetua]);
                    }
                }
            }

            // Update Nama Ketua RT
            if ($request->has('nama_ketua_rt')) {
                foreach ($request->nama_ketua_rt as $rtId => $namaKetua) {
                    $rt = RT::where('desa_id', $user->desa_id)->find($rtId);
                    if ($rt) {
                        $rt->update(['nama_ketua' => $namaKetua]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin_desa.user_directory.index')->with('success', 'Profil ketua berhasil diperbarui secara massal!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui profil ketua: ' . $e->getMessage());
        }
    }
}

