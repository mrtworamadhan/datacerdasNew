<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\RW;
use App\Models\RT;
use App\Models\LogKependudukan;
use App\Models\StatusKependudukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\WargaExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class WargaController extends Controller
{
    public function index(Request $request, string $subdomain)
    {
        // -- 1. PENGATURAN FILTER & QUERY DASAR --
        $user = Auth::user();
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Query dasar untuk data warga aktif (tidak meninggal)
        // Ini akan menjadi basis untuk semua perhitungan statistik warga
        $wargaQuery = Warga::query()
            ->whereHas('statusKependudukan', fn($q) => $q->where('nama', '!=', 'Meninggal'));

        $baseKkQuery = KartuKeluarga::query();

        // -- 2. PENGHITUNGAN STATISTIK --

        // A. Statistik Jenis Kelamin
        $statistik['jenis_kelamin'] = [
            'laki_laki' => (clone $wargaQuery)->where('jenis_kelamin', 'Laki-laki')->count(),
            'perempuan' => (clone $wargaQuery)->where('jenis_kelamin', 'Perempuan')->count(),
        ];

        // B. Statistik Kelompok Usia
        $now = Carbon::now();
        $statistik['kelompok_usia'] = [
            'balita'     => (clone $wargaQuery)->whereBetween('tanggal_lahir', [$now->copy()->subYears(5), $now])->count(),
            'anak'       => (clone $wargaQuery)->whereBetween('tanggal_lahir', [$now->copy()->subYears(12), $now->copy()->subYears(5)])->count(),
            'remaja'     => (clone $wargaQuery)->whereBetween('tanggal_lahir', [$now->copy()->subYears(17), $now->copy()->subYears(12)])->count(),
            'dewasa'     => (clone $wargaQuery)->whereBetween('tanggal_lahir', [$now->copy()->subYears(40), $now->copy()->subYears(17)])->count(),
            'pralansia'  => (clone $wargaQuery)->whereBetween('tanggal_lahir', [$now->copy()->subYears(60), $now->copy()->subYears(40)])->count(),
            'lansia'     => (clone $wargaQuery)->where('tanggal_lahir', '<=', $now->copy()->subYears(60))->count(),
        ];

        // C. Statistik Status Khusus (asumsi nama status di tabel status_khusus adalah 'Janda', 'Yatim', 'Piatu')
        $statistik['status_khusus'] = [
            'janda' => (clone $baseKkQuery)->whereHas('kepalaKeluarga', fn($q) => $q->where('jenis_kelamin', 'Perempuan')
                    ->whereHas('statusPerkawinan', fn($sp) => $sp
                    ->whereIn('nama', ['Cerai Hidup', 'Cerai Mati'])))
                    ->count(),
 
            'yatim' => (clone $wargaQuery)->whereHas('hubunganKeluarga', fn($q) => $q
                    ->where('nama', 'Anak'))
                    ->whereHas('kartuKeluarga.kepalaKeluarga', fn($q) => $q
                    ->where('jenis_kelamin', 'Perempuan')
                    ->whereHas('statusPerkawinan', fn($sp) => $sp
                    ->where('nama', 'Cerai Mati')))
                    ->count(),
                    
            'piatu' => (clone $wargaQuery)->whereHas('hubunganKeluarga', fn($q) => $q
                    ->where('nama', 'Anak'))
                    ->whereHas('kartuKeluarga.kepalaKeluarga', fn($q) => $q
                    ->where('jenis_kelamin', 'Laki-laki')
                    ->whereHas('statusPerkawinan', fn($sp) => $sp
                    ->where('nama', 'Cerai Mati')))
                    ->count()
        ];

        // D. Statistik Peristiwa Kependudukan (dari Log, sesuai filter bulan & tahun)
        $logQuery = LogKependudukan::whereYear('tanggal_peristiwa', $tahun)->whereMonth('tanggal_peristiwa', $bulan);
        
        $statistik['peristiwa'] = [
            'lahir' => (clone $logQuery)->where('jenis_peristiwa', 'Lahir')->count(),
            'meninggal' => (clone $logQuery)->where('jenis_peristiwa', 'Meninggal')->count(),
            'datang' => (clone $logQuery)->where('jenis_peristiwa', 'Datang')->count(),
            'pindah' => (clone $logQuery)->where('jenis_peristiwa', 'Pindah')->count(),
        ];

        // -- 3. MENGAMBIL DATA WARGA UNTUK TABEL (DENGAN PAGINASI & PENCARIAN) --
        $queryTabelWarga = Warga::with('kartuKeluarga.kepalaKeluarga', 'rw', 'rt', 'statusKependudukan')
            ->whereHas('statusKependudukan', fn($q) => $q->where('nama', '!=', 'Meninggal'));

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $queryTabelWarga->where(function($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', '%' . $searchTerm . '%')
                ->orWhere('nik', 'like', '%' . $searchTerm . '%');
            });
        }

        $wargas = $queryTabelWarga->latest()->paginate(15);

        // -- 4. MENGIRIM SEMUA DATA KE VIEW --
        return view('admin_desa.warga.index', compact('wargas', 'statistik', 'bulan', 'tahun'));
    }

    public function show(string $subdomain, Warga $warga)
    {
        // Menggunakan eager loading untuk memuat relasi sekaligus, ini lebih efisien
        $warga->load(
            'kartuKeluarga.kepalaKeluarga',
            'rw',
            'rt',
            'statusKependudukan',
            'agama',
            'pendidikan',
            'pekerjaan',
            'logKependudukan.pencatat' // Memuat log dan siapa user yang mencatatnya
        );
        $semuaStatus = StatusKependudukan::all();

        return view('admin_desa.warga.show', compact('warga', 'semuaStatus'));

    }

    public function updateStatus(Request $request, string $subdomain, Warga $warga)
    {
        $request->validate([
            'status_kependudukan_id' => 'required|exists:status_kependudukans,id',
        ]);

        $warga->update([
            'status_kependudukan_id' => $request->status_kependudukan_id,
        ]);

        return redirect()->back()->with('success', 'Status kependudukan warga berhasil diperbarui.');
    }

    public function searchWarga(Request $request, string $subdomain)
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
        $wargas = $query->where(function ($q) use ($searchTerm) {
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

    public function searchKeluarga(Request $request, string $subdomain)
    {
        $searchTerm = $request->input('q');
        $user = Auth::user();

        if (!$searchTerm) {
            return response()->json(['results' => []]);
        }

        // Mulai query dasar
        $query = KartuKeluarga::query();

        // Terapkan filter berdasarkan peran pengguna
        if ($user->user_type === 'admin_rt' && $user->rt_id) {
            $query->where('rt_id', $user->rt_id);
        } elseif ($user->user_type === 'admin_rw' && $user->rw_id) {
            $query->where('rw_id', $user->rw_id);
        }
        // Untuk admin_desa, tidak perlu filter tambahan karena sudah dihandle Global Scope

        // Lanjutkan dengan query pencarian NIK atau Nama
        $keluarga = $query->where(function ($q) use ($searchTerm) {
            $q->where('no_kk', 'LIKE', "%{$searchTerm}%");
        })
            ->with(['kartuKeluarga', 'rt', 'rw']) // Eager load untuk performa
            ->limit(10)
            ->get();

        // Format data agar sesuai dengan yang dibutuhkan Select2
        $formattedResults = $keluarga->map(function ($keluarga) {
            return [
                'id' => $keluarga->id,
                'text' => sprintf(
                    '%s (NIK: %s) - RW %s / RT %s',
                    $keluarga->nama_lengkap,
                    $keluarga->no_kk,
                    $keluarga->rw->nomor_rw ?? '-',
                    $keluargag->rt->nomor_rt ?? '-'
                )
            ];
        });

        return response()->json(['results' => $formattedResults]);
    }
    public function searchKK(Request $request, string $subdomain)
    {
        $searchTerm = $request->input('q');

        $queryKK = KartuKeluarga::with('kepalaKeluarga');

        if ($searchTerm) {
            $queryKK->where(function ($q) use ($searchTerm) {
                $q->where('nomor_kk', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('kepalaKeluarga', function ($q2) use ($searchTerm) {
                        $q2->where('nama_lengkap', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $kartuKeluargas = $queryKK->limit(10)->get();

        $formattedResults = $kartuKeluargas->map(function ($kk) {
            return [
                'id' => $kk->id,
                'text' => sprintf(
                    'KK: %s (Kepala: %s)',
                    $kk->nomor_kk,
                    $kk->kepalaKeluarga->nama_lengkap ?? '-'
                )
            ];
        });

        return response()->json(['results' => $formattedResults]);
    }

    public function exportWargaSemua(string $subdomain)
    {
        $namaFile = 'Data Warga Desa - ' . now()->format('d-m-Y') . '.xlsx';
        return Excel::download(new WargaExport(), $namaFile);
    }

    public function exportWargaPerRw(string $subdomain, RW $rw)
    {
        $namaFile = 'Data Warga RW ' . $rw->nomor_rw . ' - ' . now()->format('d-m-Y') . '.xlsx';
        // Kirim rw_id ke constructor WargaExport
        return Excel::download(new WargaExport($rw->id), $namaFile);
    }

    public function exportWargaPerRt(string $subdomain, RT $rt)
    {
        $namaFile = 'Data Warga RT ' . $rt->nomor_rt . ' - ' . now()->format('d-m-Y') . '.xlsx';
        // Kirim rw_id ke constructor WargaExport
        return Excel::download(new WargaExport($rt->id), $namaFile);
    }

}