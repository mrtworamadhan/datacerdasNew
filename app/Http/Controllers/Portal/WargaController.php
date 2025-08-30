<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\Agama;
use App\Models\StatusPerkawinan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\GolonganDarah;
use App\Models\HubunganKeluarga;
use App\Models\StatusKependudukan;
use App\Models\StatusKhusus;
use App\Models\SuratSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class WargaController extends Controller
{
    /**
     * Menampilkan halaman pencarian warga untuk diupdate.
     */
    public function index(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa;
       
        return view('portal.warga.index', compact('desa'));
    }
    public function edit(string $subdomain, Warga $warga)
    {
        $user = Auth::user();
        $desa = $user->desa;

        if ($user->hasRole('admin_rt') && $warga->rt_id !== $user->rt_id) {
            abort(403, 'Anda tidak berhak mengakses data warga ini.');
        }
        if ($user->hasRole('admin_rw') && $warga->rw_id !== $user->rw_id) {
            abort(403, 'Anda tidak berhak mengakses data warga ini.');
        }

        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
        $agamaOptions              = Agama::pluck('nama', 'id');
        $statusPerkawinanOptions   = StatusPerkawinan::pluck('nama', 'id');
        $pekerjaanOptions          = Pekerjaan::pluck('nama', 'id');
        $pendidikanOptions         = Pendidikan::pluck('nama', 'id');
        $kewarganegaraanOptions     = ['WNI', 'WNA'];
        $golonganDarahOptions      = GolonganDarah::pluck('nama', 'id');
        $hubunganKeluargaOptions   = HubunganKeluarga::pluck('nama', 'id');
        $statusKependudukanOptions = StatusKependudukan::pluck('nama', 'id');
        $statusKhususOptions       = StatusKhusus::pluck('nama', 'id');

        return view('portal.warga.edit', compact(
            'desa',
            'warga', 
            'subdomain',
            'jenisKelaminOptions',
            'agamaOptions',
            'statusPerkawinanOptions',
            'pekerjaanOptions',
            'pendidikanOptions',
            'kewarganegaraanOptions',
            'golonganDarahOptions',
            'hubunganKeluargaOptions',
            'statusKependudukanOptions',
            'statusKhususOptions'
        ));
    }

    /**
     * Memperbarui data warga di database.
     */
    public function update(Request $request, string $subdomain, Warga $warga)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nik' => [
                'nullable', 
                'string',
                'digits:16',
                Rule::unique('wargas')->ignore($warga->id)->where(function ($query) use ($user) {
                    return $query->where('desa_id', $user->desa_id);
                }),
            ],
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama_id' => 'nullable|exists:agamas,id',
            'pekerjaan_id' => 'nullable|exists:pekerjaans,id',
            'pendidikan_id' => 'nullable|exists:pendidikans,id',
            'status_perkawinan_id' => 'nullable|exists:status_perkawinans,id',
            'golongan_darah_id' => 'nullable|exists:golongan_darahs,id',
            'hubungan_keluarga_id' => 'nullable|exists:hubungan_keluargas,id',
            'status_kependudukan_id' => 'nullable|exists:status_kependudukans,id',
            'kewarganegaraan' => 'required|string|max:50',
            'alamat_lengkap' => 'required|string|max:255',            
            'nama_ayah_kandung' => 'nullable|string|max:255',
            'nama_ibu_kandung' => 'nullable|string|max:255',
        ]);

        if ($warga->status_data === 'Data Sementara' && !empty($validated['nik'])) {
            $validated['status_data'] = 'Terverifikasi';
        }

        DB::beginTransaction();
        try {
            $warga->update($validated);

            DB::commit();
            return redirect()->route('portal.laporan.tidak_lengkap', ['subdomain' => $subdomain])->with('success', 'Data Warga berhasil diperbarui!');
        } catch (\Exception | \Throwable $e) { // Catch Throwable for broader error handling
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Data Warga: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form untuk mengedit status warga.
     */
    public function editStatus(string $subdomain, Warga $warga)
    {
        $user = auth()->user();
        
        $user = Auth::user();
        $desa = $user->desa;
        
        $statusKhususOptions = StatusKhusus::pluck('nama', 'id');        
        $semuaStatus = StatusKependudukan::all();
        
        $warga->status_khusus = is_array($warga->status_khusus)
        ? $warga->status_khusus
        : json_decode($warga->status_khusus, true) ?? [];

        return view('portal.warga.edit_status', compact('warga', 'statusKhususOptions', 'desa', 'semuaStatus'));
    }

    /**
     * Mengupdate data status warga.
     */
    public function updateStatus(Request $request, string $subdomain, Warga $warga)
    {
        $validated = $request->validate([
            'status_kependudukan_id' => 'required|exists:status_kependudukans,id',
            'status_khusus' => 'nullable|array',
        ]);
        // dd($validated);
        $warga->update($validated);

        return redirect()->route('portal.warga.index', ['subdomain' => $subdomain])
                         ->with('success', 'Status warga ' . $warga->nama_lengkap . ' berhasil diperbarui.');
    }
}