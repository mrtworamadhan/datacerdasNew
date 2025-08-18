<?php

namespace App\Http\Controllers;

use App\Models\RW; 
use App\Models\RT; 
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen wilayah yang berisi daftar RW.
     */
    public function index(string $subdomain)
    {
        // Ambil semua data RW untuk desa yang sedang aktif (Global Scope akan bekerja di sini).
        // Kita juga akan langsung menghitung jumlah RT, KK, dan Warga di setiap RW
        // menggunakan withCount() agar lebih efisien.
        $rws = RW::withCount(['rts', 'kartuKeluargas', 'wargas'])
                 ->paginate(10); // Gunakan paginasi untuk jaga-jaga jika RW banyak

        // Kirim data ke view
        return view('admin_desa.wilayah.index', compact('rws'));
    }

    public function showRw(string $subdomain, Rw $rw)
    {
        // Mengambil data RT yang berada di bawah RW yang dipilih.
        // Kita juga langsung hitung jumlah KK dan Warga di setiap RT.
        $rts = RT::where('rw_id', $rw->id)
                 ->withCount(['kartuKeluargas', 'wargas'])
                 ->paginate(10);

        // Kirim data RW dan daftar RT ke view
        return view('admin_desa.wilayah.show_rw', compact('rw', 'rts'));
    }

    public function showRt(string $subdomain, Rt $rt)
    {
        // Mengambil data warga yang berada di bawah RT yang dipilih.
        // Kita juga load relasi kartuKeluarga agar bisa ditampilkan di tabel.
        $wargas = Warga::where('rt_id', $rt->id)
                      ->with('kartuKeluarga')
                      ->paginate(20); // Paginasi untuk daftar warga

        // Kirim data RT dan daftar warganya ke view
        return view('admin_desa.wilayah.show_rt', compact('rt', 'wargas'));
    }

    public function getProvinces()
    {
        $provinces = DB::table('wilayah')
            ->whereRaw("CHAR_LENGTH(kode) = 2") // kode provinsi hanya 2 digit
            ->select('kode', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json($provinces);
    }

    // Ambil Kota/Kabupaten berdasarkan kode Provinsi (kode 5 karakter)
    public function getCities($province_id)
    {
        $cities = DB::table('wilayah')
            ->whereRaw("CHAR_LENGTH(kode) = 5") // kode kota/kabupaten 5 digit
            ->where('kode', 'like', "$province_id%") // Harus diawali kode Provinsi
            ->select('kode', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json($cities);
    }

    // Ambil Kecamatan berdasarkan kode Kota/Kabupaten (kode 8 karakter)
    public function getSubdistricts($city_id)
    {
        $subdistricts = DB::table('wilayah')
            ->whereRaw("CHAR_LENGTH(kode) = 8") // kode kecamatan 8 digit
            ->where('kode', 'like', "$city_id%")
            ->select('kode', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json($subdistricts);
    }

    // Ambil Desa/Kelurahan berdasarkan kode Kecamatan (kode 13 karakter)
    public function getVillages($subdistrict_id)
    {
        $villages = DB::table('wilayah')
            ->whereRaw("CHAR_LENGTH(kode) = 13") // kode desa 13 digit
            ->where('kode', 'like', "$subdistrict_id%")
            ->select('kode', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json($villages);
    }
}