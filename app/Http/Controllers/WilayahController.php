<?php

namespace App\Http\Controllers;

use App\Models\Rw; 
use App\Models\Rt; 
use App\Models\Warga;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen wilayah yang berisi daftar RW.
     */
    public function index()
    {
        // Ambil semua data RW untuk desa yang sedang aktif (Global Scope akan bekerja di sini).
        // Kita juga akan langsung menghitung jumlah RT, KK, dan Warga di setiap RW
        // menggunakan withCount() agar lebih efisien.
        $rws = Rw::withCount(['rts', 'kartuKeluargas', 'wargas'])
                 ->paginate(10); // Gunakan paginasi untuk jaga-jaga jika RW banyak

        // Kirim data ke view
        return view('admin_desa.wilayah.index', compact('rws'));
    }

    public function showRw(Rw $rw)
    {
        // Mengambil data RT yang berada di bawah RW yang dipilih.
        // Kita juga langsung hitung jumlah KK dan Warga di setiap RT.
        $rts = Rt::where('rw_id', $rw->id)
                 ->withCount(['kartuKeluargas', 'wargas'])
                 ->paginate(10);

        // Kirim data RW dan daftar RT ke view
        return view('admin_desa.wilayah.show_rw', compact('rw', 'rts'));
    }

    public function showRt(Rt $rt)
    {
        // Mengambil data warga yang berada di bawah RT yang dipilih.
        // Kita juga load relasi kartuKeluarga agar bisa ditampilkan di tabel.
        $wargas = Warga::where('rt_id', $rt->id)
                      ->with('kartuKeluarga')
                      ->paginate(20); // Paginasi untuk daftar warga

        // Kirim data RT dan daftar warganya ke view
        return view('admin_desa.wilayah.show_rt', compact('rt', 'wargas'));
    }
}