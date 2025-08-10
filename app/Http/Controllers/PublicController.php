<?php

namespace App\Http\Controllers;

use App\Models\Fasum;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // Untuk path gambar

class PublicController extends Controller
{
    /**
     * Display a listing of Fasum for public view.
     * Accessible by anyone (public).
     */
     public function indexPublic(Request $request,)
    {
        // Untuk halaman publik, kita tidak menerapkan Global Scope BelongsToDesa secara otomatis
        // karena kita ingin menampilkan Fasum dari desa tertentu berdasarkan parameter atau semua desa.
        // Namun, jika kita ingin menampilkan Fasum dari desa yang sudah login (jika ada), kita bisa.

        $fasumsQuery = Fasum::with('desa', 'rw', 'rt', 'photos'); // Eager load relasi dan photos

        $currentDesa = null;
        $allDesas = Desa::all(); // Ambil semua desa untuk filter dropdown

        // Filter berdasarkan desa_id jika ada di request
        if ($request->filled('desa_id')) {
            $fasumsQuery->where('desa_id', $request->desa_id);
            $currentDesa = Desa::find($request->desa_id);
        }

        // Tambahkan filter pencarian nama/alamat
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $fasumsQuery->where(function($q) use ($searchTerm) {
                $q->where('nama_fasum', 'like', '%' . $searchTerm . '%')
                  ->orWhere('alamat_lengkap', 'like', '%' . $searchTerm . '%');
            });
        }

        // Tambahkan filter jenis_fasum
        if ($request->filled('jenis_fasum')) {
            $fasumsQuery->where('kategori', $request->jenis_fasum);
        }

        $fasums = $fasumsQuery->latest()->paginate(10); // Paginasi

        return view('public.fasum.index', compact('fasums', 'currentDesa', 'allDesas'));
    }

    /**
     * Menampilkan halaman detail publik untuk sebuah Fasum.
     */
    public function showFasum(Fasum $fasum)
    {
        // Global Scope BelongsToDesa tidak akan diterapkan di sini karena ini public access.
        // Jika Fasum tidak ditemukan, Laravel akan otomatis 404.
        // Kita hanya perlu memastikan relasi di-load.
        $fasum->load('desa', 'rw', 'rt', 'photos');

        return view('public.fasum.show', compact('fasum'));
    }

    public function indexDesa(Request $request)
    {
        // Ambil hanya desa yang subscription_status-nya 'active' atau 'trial'
        $desasQuery = Desa::whereIn('subscription_status', ['active', 'trial']);

        // Filter pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $desasQuery->where(function($q) use ($searchTerm) {
                $q->where('nama_desa', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nama_kades', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kecamatan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kota', 'like', '%' . $searchTerm . '%')
                  ->orWhere('provinsi', 'like', '%' . $searchTerm . '%');
            });
        }

        $desas = $desasQuery->latest()->paginate(10); // Paginasi

        return view('public.desas.index', compact('desas'));
    }
}
