<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Desa;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Models\PengajuanSurat;
use App\Models\Lembaga;
use App\Models\Kegiatan;
use App\Models\Fasum;
use App\Models\PerangkatDesa;
use App\Models\KategoriBantuan;
use App\Models\DataIbuHamil;
use App\Models\DataKesehatanAnak;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard based on their user_type.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        if ($user->user_type === 'super_admin') {
            // Data untuk Dashboard Super Admin
            $totalDesa = Desa::count();
            $totalAdminDesa = User::where('user_type', 'admin_desa')->count();
            $totalActiveSubscriptions = Desa::where('subscription_status', 'active')->count();
            $totalInactiveSubscriptions = Desa::where('subscription_status', 'inactive')->count();

            $usersByType = User::select('user_type', DB::raw('count(*) as total'))
                ->groupBy('user_type')
                ->get()
                ->pluck('total', 'user_type')
                ->toArray();

            return view('superadmin.dashboard', compact(
                'totalDesa',
                'totalAdminDesa',
                'totalActiveSubscriptions',
                'totalInactiveSubscriptions',
                'usersByType'
            ));

        } else {
            // Data untuk Dashboard Admin Desa, RW, RT, Kader
            // Pastikan user memiliki relasi desa yang valid sebelum mengambil data spesifik desa
            if (!$user->desa) {
                // Ini seharusnya tidak terjadi jika user_type bukan super_admin dan data desa sudah terisi
                // Tapi sebagai fallback, bisa redirect atau tampilkan error
                Auth::logout();
                return view('auth.login')->with('error', 'Akun Anda tidak terhubung dengan desa manapun. Silakan hubungi Super Admin.');
            }

            $stats = [];
            $desa = $user->desa; // Ambil objek desa dari user yang login

            // 1. tataAdministrasi
            $stats['tataAdministrasi'] = [
                'nama_desa' => $desa->nama_desa ?? 'Nama Desa',
                'nama_kades' => $desa->nama_kades ?? '-',
                'jumlah_perangkat' => PerangkatDesa::count(), // Global scope akan memfilter per desa user
            ];

            // 2. tataLembaga
            $stats['tataLembaga'] = [
                'total' => Lembaga::count(), // Global scope akan memfilter per desa user
                'kegiatan' => Kegiatan::count(), // Global scope akan memfilter per desa user
            ];

            // 3. tataFasum
            $stats['tataFasum'] = [
                'total' => Fasum::count(), // Global scope akan memfilter per desa user
                'baik' => Fasum::where('status_kondisi', 'Baik')->count(), // Global scope akan memfilter per desa user
                'perbaikan' => Fasum::whereIn('status_kondisi', ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'])->count(), // Global scope akan memfilter per desa user
            ];

            // 4. tataWarga
            $stats['tataWarga'] = [
                'jumlah_rw' => $desa->rws()->count(),
                'jumlah_rt' => $desa->rts()->count(),
                'total_warga' => Warga::count(),
                'total_kk' => KartuKeluarga::count(),

                // Jenis kelamin
                'total_laki' => Warga::where('jenis_kelamin', 'Laki-laki')->count(),
                'total_perempuan' => Warga::where('jenis_kelamin', 'Perempuan')->count(),

                // Usia
                'usia_balita' => Warga::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= 5')->count(),
                'usia_anak' => Warga::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 12')->count(),
                'usia_remaja' => Warga::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 13 AND 17')->count(),
                'usia_muda' => Warga::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 35')->count(),
                'usia_lansia' => Warga::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count(),

                // Status janda/duda
                'status_janda' => KartuKeluarga::whereHas('kepalaKeluarga', function ($q) {
                    $q->where('jenis_kelamin', 'Perempuan')
                    ->whereHas('statusPerkawinan', fn($sp) => $sp->whereIn('nama', ['Cerai Hidup', 'Cerai Mati']));
                })->count(),


                'status_duda' => KartuKeluarga::whereHas('kepalaKeluarga', function ($q) {
                    $q->where('jenis_kelamin', 'Laki-laki')
                    ->whereHas('statusPerkawinan', fn($sp) => $sp->whereIn('nama', ['Cerai Hidup', 'Cerai Mati']));
                })->count(),


                // Yatim
                'status_yatim' => Warga::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 17')
                    ->where(fn($q) => $q->whereNull('nama_ayah_kandung')->orWhere('nama_ayah_kandung', ''))
                    ->count(),

                // Domisili
                'domisili_asli' => Warga::whereHas('statusKependudukan', fn($sk) => $sk->where('nama', 'Warga Asli'))->count(),
                'domisili_pendatang' => Warga::whereHas('statusKependudukan', fn($sk) => $sk->whereIn('nama', ['Pendatang', 'Sementara']))->count(),

                // Pekerjaan
                'pengangguran' => Warga::whereHas('pekerjaan', fn($p) => $p->whereIn('nama', ['Tidak Bekerja', 'Belum / Tidak Bekerja']))->count(),

                // Bantuan
                'bantuan' => KategoriBantuan::withCount('penerimaBantuans')->get(),
            ];

            // 5. tataSurat
            $stats['tataSurat'] = [
                'total' => PengajuanSurat::count(), // Global scope akan memfilter per desa user
                'diproses' => PengajuanSurat::whereIn('status_permohonan', ['Diajukan', 'Diproses Desa'])->count(),
                'selesai' => PengajuanSurat::where('status_permohonan', 'Disetujui')->count(),
                'ditolak' => PengajuanSurat::where('status_permohonan', 'Ditolak')->count(),
            ];

            // 6. tataKesehatan
            $stats['tataKesehatan'] = [
                'total_balita' => DataKesehatanAnak::count(), // Global scope akan memfilter per desa user
                'total_bumil' => DataIbuHamil::count(), // Global scope akan memfilter per desa user
            ];

            return view('dashboard', compact('stats'));
        }
    }
}
